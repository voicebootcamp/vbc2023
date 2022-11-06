<?php
/**
 * @package     MPF
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2016 Ossolution Team, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;

class MPFFormFieldDate extends MPFFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 */
	protected $type = 'Date';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 * @var OSMembershipHelperBootstrap $bootstrapHelper
	 */
	protected function getInput($bootstrapHelper = null)
	{
		$config       = OSMembershipHelper::getConfig();
		$dateFormat   = $config->date_field_format ?: '%Y-%m-%d';
		$iconCalendar = $bootstrapHelper ? $bootstrapHelper->getClassMapping('icon-calendar') : 'icon-calendar';

		try
		{
			return str_replace('icon-calendar', $iconCalendar, HTMLHelper::_('calendar', $this->value, $this->name, $this->name, $dateFormat, $this->attributes));
		}
		catch (Exception $e)
		{
			return str_replace('icon-calendar', $iconCalendar, HTMLHelper::_('calendar', '', $this->name, $this->name, $dateFormat, $this->attributes)) . ' Value <strong>' . $this->value . '</strong> is invalid. Please correct it with format YYYY-MM-DD';
		}
	}
}
