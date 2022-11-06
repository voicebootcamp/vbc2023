<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2022 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

JFormHelper::loadFieldClass('GroupedList');

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

class JFormFieldEBMenuItems extends JFormFieldGroupedList
{
	/**
	 * The form field type.
	 *
	 * @var string
	 */
	protected $type = 'ebmenuitems';

	/**
	 * Return list of options for the field
	 *
	 * @return array
	 */
	public function getGroups()
	{
		$component = ComponentHelper::getComponent('com_eventbooking');
		$menus     = Factory::getApplication()->getMenu('site');

		$attributes = ['component_id'];
		$values     = [$component->id];
		$items      = $menus->getItems($attributes, $values);

		$groups = [];

		foreach ($items as $item)
		{
			if ($item->language !== '*')
			{
				$lang = ' (' . $item->language . ')';
			}
			else
			{
				$lang = '';
			}

			$groups[$item->menutype][] = HTMLHelper::_('select.option', $item->id, str_repeat('- ', $item->level) . $item->title . $lang);
		}

		array_unshift($groups, [HTMLHelper::_('select.option', 0, Text::_('--Menu Item--'))]);

		return $groups;
	}
}
