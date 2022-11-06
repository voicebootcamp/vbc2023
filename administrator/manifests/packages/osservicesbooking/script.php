<?php

/**
 * @package        Joomla
 * @subpackage     OS Services Booking
 * @author         Dang Thuc Dam
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
class Pkg_OsservicesbookingInstallerScript
{
	protected $installType;

	public function preflight($type, $parent)
	{
		if (!version_compare(JVERSION, '3.4.0', 'ge'))
		{
			JError::raiseWarning(null, 'Cannot install OS Services Booking in a Joomla release prior to 3.4.0');

			return false;
		}
	}

	/**
	 * method to install the component
	 *
	 * @return void
	 */
	public function install($parent)
	{
		$this->installType = 'install';
	}

	public function update($parent)
	{
		$this->installType = 'update';
	}

	public function postflight($type, $parent)
	{
	
	}
}