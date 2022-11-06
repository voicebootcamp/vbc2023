<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\Installer;

class Pkg_EventbookingInstallerScript
{
	/**
	 * The original version, use for update process
	 *
	 * @var string
	 */
	protected $installedVersion = '3.7.0';

	/**
	 * Perform basic system requirements check before installing the package
	 *
	 * @param   string    $type
	 * @param   JAdapter  $parent
	 *
	 * @return bool
	 */
	public function preflight($type, $parent)
	{
		if (version_compare(JVERSION, '3.9.0', '<'))
		{
			JError::raiseWarning(null, 'Cannot install Events Booking in a Joomla! release prior to 3.9.0');

			return false;
		}

		if (version_compare(PHP_VERSION, '7.2.0', '<'))
		{
			JError::raiseWarning(null, 'Events Booking requires PHP 7.2.0+ to work. Please contact your hosting provider, ask them to update PHP version for your hosting account.');

			return false;
		}

		$this->getInstalledVersion();

		if (version_compare($this->installedVersion, '3.7.0', '<'))
		{
			JError::raiseWarning(null, 'Update from older version than 3.7.0 is not supported! You need to update to version 3.17.6 first. Please contact support to get that old Events Booking version');

			return false;
		}

		if (version_compare($this->installedVersion, '4.0.0', '<'))
		{
			$installer = new Installer;

			$db    = Factory::getDbo();
			$query = $db->getQuery(true);

			$plugins = [
				['eventbooking', 'spout'],
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
	 * Finalize package installation
	 *
	 * @param   string    $type
	 * @param   JAdapter  $parent
	 *
	 * @return bool
	 */
	public function postflight($type, $parent)
	{
		// Do not perform redirection anymore if installed version is greater than or equal 3.8.3
		if (strtolower($type) == 'install' || version_compare($this->installedVersion, '3.8.3', '>='))
		{
			return true;
		}

		$app = JFactory::getApplication();
		$app->setUserState('com_installer.redirect_url', 'index.php?option=com_eventbooking&task=update.update&install_type=' . strtolower($type));
		$app->input->set('return', base64_encode('index.php?option=com_eventbooking&task=update.update&install_type=' . strtolower($type)));
	}

	/**
	 * Get installed version of the component
	 *
	 * @return void
	 */
	private function getInstalledVersion()
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('manifest_cache')
			->from('#__extensions')
			->where($db->quoteName('element') . ' = "com_eventbooking"')
			->where($db->quoteName('type') . ' = "component"');
		$db->setQuery($query);
		$manifestCache = $db->loadResult();

		if ($manifestCache)
		{
			$manifest = json_decode($manifestCache);

			$this->installedVersion = $manifest->version;
		}
	}
}