<?php

/*------------------------------------------------------------------------
# emails.php - Ossolution Services Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2022 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die;

/**
 * Employee table
 *
 * @package		Joomla.Administrator
 * @subpackage	com_osservicesbooking
 * @since		1.5
 */
class OsAppTableLog extends JTable
{
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $_db
	 */	
	public function __construct(&$_db)
	{
		parent::__construct('#__app_sch_email_logs', 'id', $_db);
	}
}