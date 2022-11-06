<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

/**
 * OSMembership Component State Model
 *
 * @package        Joomla
 * @subpackage     Membership Pro
 */
class OSMembershipModelState extends MPFModelAdmin
{
	/**
	 * Update state_id make it the same with id
	 *
	 * @param   JTable    $row
	 * @param   MPFInput  $input
	 * @param   bool      $isNew
	 */
	protected function afterStore($row, $input, $isNew)
	{
		if ($isNew)
		{
			$db    = $this->getDbo();
			$query = $db->getQuery(true);
			$query->update('#__osmembership_states')
				->set('state_id = id')
				->where('id=' . $row->id);
			$db->setQuery($query);
			$db->execute();
		}
	}
}
