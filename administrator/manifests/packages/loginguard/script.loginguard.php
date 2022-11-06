<?php
/**
 * @package   AkeebaLoginGuard
 * @copyright Copyright (c)2016-2022 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Prevent direct access
defined('_JEXEC') or die;

use Joomla\CMS\Encrypt\Aes;
use Joomla\CMS\Factory;
use Joomla\CMS\Installer\Adapter\PackageAdapter;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Table\Extension;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;

class Pkg_LoginguardInstallerScript
{
	private const UNINSTALL_STACK = [
		'com_loginguard',
		'plg_actionlog_loginguard',
		'plg_system_loginguard',
		'plg_loginguard_email',
		'plg_loginguard_fixed',
		'plg_loginguard_pushbullet',
		'plg_loginguard_smsapi',
		'plg_loginguard_totp',
		'plg_loginguard_webauthn',
		'plg_loginguard_yubikey',
		'plg_user_loginguard',
	];

	private const MAP_STACK = [
		'plg_loginguard_email'      => 'plg_multifactorauth_email',
		'plg_loginguard_fixed'      => 'plg_multifactorauth_fixed',
		'plg_loginguard_pushbullet' => 'plg_multifactorauth_pushbullet',
		'plg_loginguard_smsapi'     => 'plg_multifactorauth_smsapi',
		'plg_loginguard_totp'       => 'plg_multifactorauth_totp',
		'plg_loginguard_webauthn'   => 'plg_multifactorauth_webauthn',
		'plg_loginguard_yubikey'    => 'plg_multifactorauth_yubikey',
	];

	/**
	 * Caches the extension names to IDs so we don't query the database too many times.
	 *
	 * @var   array
	 * @since 7.0.0
	 */
	private $extensionIds = [];

	/**
	 * Runs before installing the package
	 *
	 * @param   string          $type    Installation type
	 * @param   PackageAdapter  $parent  The package installation adapter which is calling us
	 *
	 * @return  bool
	 *
	 * @since   7.0.0
	 */
	public function preflight(string $type, $parent): bool
	{
		// Do not run on uninstallation.
		if ($type === 'uninstall')
		{
			return true;
		}

		// Prevent users from installing this on Joomla 4.1 and earlier
		if (version_compare(JVERSION, '4.1.99999', 'le'))
		{
			$suggestedVersion = version_compare(JVERSION, '3.9999.9999', 'le')
				? '5' : '6';

			$msg = "<p>This version of Akeeba LoginGuard cannot run on Joomla 4.1 or earlier. Please download and install Akeeba LoginGuard $suggestedVersion instead.</p>";

			Log::add($msg, Log::WARNING, 'jerror');

			return false;
		}

		// Migrate settings. Must be in pre-flight, before Joomla removes the component!
		$this->migrateTfaRecords($parent->db);

		return true;
	}

	/**
	 * Runs after install, update or discover_update. In other words, it executes after Joomla! has finished installing
	 * or updating your component. This is the last chance you've got to perform any additional installations, clean-up,
	 * database updates and similar housekeeping functions.
	 *
	 * @param   string            $type    install, update or discover_update
	 * @param   PackageAdapter  $parent  Parent object
	 *
	 * @return  bool
	 * @throws  Exception
	 *
	 */
	public function postflight($type, $parent)
	{
		// Do not run on uninstallation.
		if ($type === 'uninstall')
		{
			return true;
		}

		// Enable and reconfigure core and LoginGuard MFA plugins using the settings of the legacy LoginGuard plugins
		foreach (self::MAP_STACK as $oldPlugin => $newPlugin)
		{
			$this->migratePlugin($oldPlugin, $newPlugin, $parent->db);
		}

		// Remove obsolete extensions
		foreach (self::UNINSTALL_STACK as $extension)
		{
			$this->uninstallExtension($extension, $parent->db);
		}

		return true;
	}

	/**
	 * Migrate the legacy LoginGuard TFA records to the nwe Joomla MFA format.
	 *
	 * @param   DatabaseInterface  $db
	 *
	 * @since   7.0.0
	 */
	public function migrateTfaRecords(DatabaseInterface $db)
	{
		// Remove records for users having both Joomla MFA and LoginGuard set up.
		$innerQuery = $db->getQuery(true)
		                 ->select($db->quoteName('user_id'))
		                 ->from('#__loginguard_tfa')
		                 ->group($db->quoteName('user_id'));
		$outerQuery = $db->getQuery(true)
		                 ->delete($db->quoteName('#__user_mfa'))
		                 ->where(
			                 $db->quoteName('user_id') . ' IN (' .
			                 (string) $innerQuery . ')'
		                 );
		try
		{
			$db->setQuery($outerQuery)->execute();
		}
		catch (Exception $e)
		{
			// It may fail if there are no records or LoginGuard is no logner installed. That's OK.
		}

		// Set up an encryption/decryption service and get the LoginGuard and Joomla passwords
		$aes = new Aes('cbc');

		$loginguardPassword = $this->getPassword();

		try
		{
			$joomlaPassword = Factory::getApplication()->get('secret', '');
		}
		catch (Exception $e)
		{
			$joomlaPassword = '';
		}

		$query = $db->getQuery(true)
		            ->select('*')
		            ->from($db->quoteName('#__loginguard_tfa'));

		// Let's iterate the LoginGuard entries
		$start = 0;
		$limit = 1000;

		while (true)
		{
			// Get up to 1000 records
			try
			{
				$db->setQuery($query, $start, $limit);
				$results = $db->loadObjectList() ?: [];
			}
			catch (Exception $e)
			{
				$results = [];
			}

			if (empty($results))
			{
				break;
			}

			// Decrypt the records we read
			$results = array_map(
				function (object $record) use ($aes, $loginguardPassword) {
					$record->options = ($record->options ?? '') ?: '';

					if (substr($record->options, 0, 12) != '###AES128###')
					{
						return $record;
					}

					$aes->setPassword($loginguardPassword);
					$data            = substr($record->options, 12);
					$record->options = rtrim($aes->decryptString($data, true), "\0");

					try
					{
						$temp = @json_decode($record->options, true);
					}
					catch (Exception $e)
					{
						$temp = null;
					}

					if (!is_array($temp))
					{
						$aes->setPassword($loginguardPassword, true);
						$record->options = rtrim($aes->decryptString($data, true), "\0");
					}

					return $record;
				},
				$results
			);

			// Re-encrypt the records with the Joomla password
			$aes->setPassword($joomlaPassword);

			$results = array_map(
				function (object $record) use ($aes) {
					unset ($record->id);

					$record->options = '###AES128###' . $aes->encryptString($record->options, true);

					return $record;
				},
				$results
			);

			// Commit the converted records using a transaction
			$db->transactionStart();

			foreach ($results as $record)
			{
				$db->insertObject('#__user_mfa', $record, 'id');
			}

			$db->transactionCommit();

			$start += $limit;
		}

		// Remove LoginGuard records for users having both Joomla MFA and LoginGuard set up.
		$innerQuery = $db->getQuery(true)
		                 ->select($db->quoteName('user_id'))
		                 ->from('#__user_mfa')
		                 ->group($db->quoteName('user_id'));
		$outerQuery = $db->getQuery(true)
		                 ->delete($db->quoteName('#__loginguard_tfa'))
		                 ->where(
			                 $db->quoteName('user_id') . ' IN (' .
			                 (string) $innerQuery . ')'
		                 );
		try
		{
			$db->setQuery($outerQuery)->execute();
		}
		catch (Exception $e)
		{
			// It may fail if there are no records or LoginGuard is no logner installed. That's OK.
		}
	}

	/**
	 * Returns the password used to encrypt information in the component
	 *
	 * @return  string
	 *
	 * @since   7.0.0
	 */
	private function getPassword(): string
	{
		$constantName = 'LOGINGUARD_FOF_ENCRYPT_SERVICE_SECRETKEY';
		$filePath     = JPATH_ADMINISTRATOR . '/components/com_loginguard/encrypt_service_key.php';

		if (defined($constantName))
		{
			return constant($constantName);
		}

		if (!file_exists($filePath))
		{
			return '';
		}

		include_once $filePath;

		return !defined($constantName) ? '' : constant($constantName);
	}

	/**
	 * Uninstall an extension by name.
	 *
	 * @param   string             $extension
	 * @param   DatabaseInterface  $db
	 *
	 * @return  bool
	 */
	private function uninstallExtension(string $extension, DatabaseInterface $db): bool
	{
		// Let's get the extension ID. If it's not there we can't uninstall this extension, right..?
		$eid = $this->getExtensionId($extension, $db);

		if (empty($eid))
		{
			return false;
		}

		// Get an Extension table object and Installer object.
		$row       = new Extension($db);
		$installer = Installer::getInstance();

		// Load the extension row or fail the uninstallation immediately.
		try
		{
			if (!$row->load($eid))
			{
				return false;
			}
		}
		catch (Throwable $e)
		{
			// If the database query fails or Joomla experiences an unplanned rapid deconstruction let's bail out.
			return false;
		}

		// Can't uninstalled protected extensions
		/** @noinspection PhpUndefinedFieldInspection */
		if ((int) $row->locked === 1)
		{
			return false;
		}

		// An extension row without a type? What have you done to your database, you MONSTER?!
		if (empty($row->type))
		{
			return false;
		}

		// Do the actual uninstallation. Try to trap any errors, just in case...
		try
		{
			return $installer->uninstall($row->type, $eid);
		}
		catch (Throwable $e)
		{
			return false;
		}
	}

	/**
	 * Returns the extension ID for a Joomla extension given its name.
	 *
	 * This is deliberately public so that custom handlers can use it without having to reimplement it.
	 *
	 * @param   string             $extension  The extension name, e.g. `plg_system_example`.
	 * @param   DatabaseInterface  $db
	 *
	 * @return  int|null  The extension ID or null if no such extension exists
	 * @since   7.0.0
	 */
	private function getExtensionId(string $extension, DatabaseInterface $db): ?int
	{
		if (isset($this->extensionIds[$extension]))
		{
			return $this->extensionIds[$extension];
		}

		$this->extensionIds[$extension] = null;

		$criteria = $this->extensionNameToCriteria($extension);

		if (empty($criteria))
		{
			return $this->extensionIds[$extension];
		}

		$query = $db->getQuery(true)
		            ->select($db->quoteName('extension_id'))
		            ->from($db->quoteName('#__extensions'));

		foreach ($criteria as $key => $value)
		{
			$type = is_numeric($value) ? ParameterType::INTEGER : ParameterType::STRING;
			$type = is_bool($value) ? ParameterType::BOOLEAN : $type;
			$type = is_null($value) ? ParameterType::NULL : $type;

			/**
			 * This is required since $value is passed by reference in bind(). If we do not do this unholy trick the
			 * $value variable is overwritten in the next foreach() iteration, therefore all criteria values will be
			 * equal to the last value iterated. Groan...
			 */
			$varName    = 'queryParam' . ucfirst($key);
			${$varName} = $value;

			$query->where($db->qn($key) . ' = :' . $key)
			      ->bind(':' . $key, ${$varName}, $type);
		}

		try
		{
			$this->extensionIds[$extension] = (int) $db->setQuery($query)->loadResult();
		}
		catch (RuntimeException $e)
		{
			return null;
		}

		return $this->extensionIds[$extension];
	}

	/**
	 * Convert a Joomla extension name to `#__extensions` table query criteria.
	 *
	 * The following kinds of extensions are supported:
	 * * `pkg_something` Package type extension
	 * * `com_something` Component
	 * * `plg_folder_something` Plugins
	 * * `mod_something` Site modules
	 * * `amod_something` Administrator modules. THIS IS CUSTOM.
	 * * `file_something` File type extension
	 * * `lib_something` Library type extension
	 *
	 * @param   string  $extensionName
	 *
	 * @return  string[]
	 */
	private function extensionNameToCriteria(string $extensionName): array
	{
		$parts = explode('_', $extensionName, 3);

		switch ($parts[0])
		{
			case 'pkg':
				return [
					'type'    => 'package',
					'element' => $extensionName,
				];

			case 'com':
				return [
					'type'    => 'component',
					'element' => $extensionName,
				];

			case 'plg':
				return [
					'type'    => 'plugin',
					'folder'  => $parts[1],
					'element' => $parts[2],
				];

			case 'mod':
				return [
					'type'      => 'module',
					'element'   => $extensionName,
					'client_id' => 0,
				];

			// That's how we note admin modules
			case 'amod':
				return [
					'type'      => 'module',
					'element'   => substr($extensionName, 1),
					'client_id' => 1,
				];

			case 'file':
				return [
					'type'    => 'file',
					'element' => $extensionName,
				];

			case 'lib':
				return [
					'type'    => 'library',
					'element' => $parts[1],
				];
		}

		return [];
	}

	/**
	 * Migrates the publishing status and configuration between plugins.
	 *
	 * @param   string             $oldPlugin  The name of the old plugin
	 * @param   string             $newPlugin  The name of the new plugins
	 * @param   DatabaseInterface  $db         The Joomla database driver object
	 *
	 * @since   7.0.0
	 */
	private function migratePlugin(string $oldPlugin, string $newPlugin, DatabaseInterface $db)
	{
		// Try to find the plugins and abort if they do not exist
		$oldId = $this->getExtensionId($oldPlugin, $db);
		$newId = $this->getExtensionId($oldPlugin, $db);

		if ($oldId === null || $newId === null)
		{
			return;
		}

		$query = $db->getQuery(true)
		            ->select([$db->quoteName('enabled'), $db->quoteName('params')])
		            ->from($db->quoteName('#__extensions'))
		            ->where($db->quoteName('extension_id') . ' = :eid')
		            ->bind(':eid', $eid, ParameterType::INTEGER);

		try
		{
			$oldInfo = $db->setQuery($query)->loadObject() ?: null;
		}
		catch (Exception $e)
		{
			return;
		}

		if ($oldInfo === null)
		{
			return;
		}

		$query = $db->getQuery(true)
			->update($db->quoteName('#__extensions'))
			->set([
				$db->quoteName('enabled') . ' = :enabled',
				$db->quoteName('params') . ' = :params',
			])
			->where($db->quoteName('extension_id') . ' = :eid')
			->bind(':enabled', $oldInfo->enabled, ParameterType::INTEGER)
			->bind(':params', $oldInfo->params, ParameterType::STRING)
			->bind(':eid', $newPlugin, ParameterType::INTEGER);

		try
		{
			$db->setQuery($query)->execute();
		}
		catch (Exception $e)
		{
			// If he dies, he dies.
		}
	}
}