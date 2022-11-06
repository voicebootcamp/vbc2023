<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\Installer;

class Pkg_OsmembershipInstallerScript
{
	/**
	 * The original version, use for update process
	 *
	 * @var string
	 */
	protected $installedVersion = '2.7.0';

	/**
	 * Perform some check to see if the extension could be installed/updated
	 *
	 * @param   string                                        $type
	 * @param   \Joomla\CMS\Installer\Adapter\PackageAdapter  $parent
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function preflight($type, $parent)
	{
		if (!version_compare(JVERSION, '3.9.0', 'ge'))
		{
			Factory::getApplication()->enqueueMessage('Cannot install Membership Pro in a Joomla release prior to 3.9.0', 'warning');

			return false;
		}

		if (version_compare(PHP_VERSION, '7.2.0', '<'))
		{
			Factory::getApplication()->enqueueMessage('Membership Pro requires PHP 7.2.0+ to work. Please contact your hosting provider, ask them to update PHP version for your hosting account.', 'warning');

			return false;
		}

		$this->getInstalledVersion();

		if (version_compare($this->installedVersion, '2.7.0', '<'))
		{
			Factory::getApplication()->enqueueMessage('Update from older version than 2.7.0 is not supported! You need to update to version 2.26.0 first. Please contact support to get that old Membership Pro version', 'warning');

			return false;
		}

		if (version_compare($this->installedVersion, '3.0.0', '<'))
		{
			$installer = new Installer;

			$db    = Factory::getDbo();
			$query = $db->getQuery(true);

			$plugins = [
				['osmembership', 'spout'],
			];

			foreach ($plugins as $plugin)
			{
				$query->clear()
					->select('extension_id')
					->from('#__extensions')
					->where($db->quoteName('folder') . ' = ' . $db->quote($plugin[0]))
					->where($db->quoteName('element') . ' = ' . $db->quote($plugin[1]));
				$db->setQuery($query);
				$id = $db->loadResult();

				if ($id)
				{
					try
					{
						$installer->uninstall('plugin', $id, 0);
					}
					catch (\Exception $e)
					{

					}
				}
			}
		}
	}

	/**
	 * Get installed version of the component
	 *
	 * @return void
	 */
	/**
	 * Get installed version of the component
	 *
	 * @return void
	 */
	private function getInstalledVersion()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('manifest_cache')
			->from('#__extensions')
			->where($db->quoteName('element') . ' = "com_osmembership"')
			->where($db->quoteName('type') . ' = "component"');
		$db->setQuery($query);
		$manifestCache = $db->loadResult();

		if ($manifestCache)
		{
			$manifest               = json_decode($manifestCache);
			$this->installedVersion = $manifest->version;
		}
	}
}