<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

class OSMembershipViewGroupmemberRaw extends MPFViewHtml
{
	public function display()
	{
		$this->setLayout('groupadmins');

		$planId       = $this->input->getInt('plan_id');
		$groupAdminId = $this->input->getInt('group_admin_id');

		$options   = [];
		$options[] = HTMLHelper::_('select.option', 0, Text::_('OSM_SELECT_GROUP'), 'user_id', 'name');

		if ($planId)
		{
			$db    = Factory::getDbo();
			$query = $db->getQuery(true)
				->select('DISTINCT user_id, CONCAT(first_name, " ", last_name) AS name')
				->from('#__osmembership_subscribers AS a')
				->where('plan_id = ' . $planId)
				->where('group_admin_id = 0')
				->group('user_id')
				->order('name');
			$db->setQuery($query);
			$groupAdmins = $db->loadObjectList();

			if (count($groupAdmins))
			{
				$options = array_merge($options, $groupAdmins);
			}
		}

		$this->lists['group_admin_id'] = HTMLHelper::_('select.genericlist', $options, 'group_admin_id', ' class="form-select"', 'user_id', 'name', $groupAdminId);

		parent::display();
	}
}
