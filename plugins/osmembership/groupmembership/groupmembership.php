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
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Table\Table;

class plgOSMembershipGroupmembership extends CMSPlugin
{
	/**
	 * Database object.
	 *
	 * @var    JDatabaseDriver
	 */
	protected $db;

	/**
	 * Run when a membership activated
	 *
	 * @param   OSMembershipTableSubscriber  $row
	 */
	public function onMembershipActive($row)
	{
		if ($row->user_id && !$row->group_admin_id)
		{
			// Change subscription end date of the group members
			$db    = $this->db;
			$query = $db->getQuery(true);
			$query->select('MAX(to_date)')
				->from('#__osmembership_subscribers')
				->where('user_id=' . $row->user_id . ' AND plan_id=' . $row->plan_id . ' AND published = 1');
			$db->setQuery($query);
			$maxToDate = $db->loadResult();

			if ($maxToDate)
			{
				$query->clear()
					->update('#__osmembership_subscribers')
					->set('published = 1')
					->set('to_date = ' . $db->quote($maxToDate))
					->where('group_admin_id = ' . $row->user_id)
					->where('plan_id = ' . $row->plan_id);
				$db->setQuery($query);
				$db->execute();

				// Need to trigger onMembershipActive event
				$query->clear()
					->select('id')
					->from('#__osmembership_subscribers')
					->where('plan_id = ' . $row->plan_id)
					->where('group_admin_id = ' . $row->user_id);
				$db->setQuery($query);
				$groupMemberIds = $db->loadColumn();

				if (count($groupMemberIds))
				{
					$app = Factory::getApplication();

					foreach ($groupMemberIds as $groupMemberId)
					{
						$groupMember = Table::getInstance('Subscriber', 'OSMembershipTable');
						$groupMember->load($groupMemberId);
						$app->triggerEvent('onMembershipActive', [$groupMember]);
					}

					// Update subscription status to active, just in case they were marked as expired before for some reasons
					$query->clear()
						->update('#__osmembership_subscribers')
						->set('published = 1')
						->where('plan_id = ' . $row->plan_id)
						->where('group_admin_id = ' . $row->user_id);
					$db->setQuery($query);
					$db->execute();
				}
			}

			if ($row->act == 'upgrade')
			{
				// Process upgrade group members to new membership
				$fromPlan = OSMembershipHelperDatabase::getPlan($row->from_plan_id);
				$toPlan   = OSMembershipHelperDatabase::getPlan($row->plan_id);

				if ($fromPlan->number_group_members > 0 && $toPlan->number_group_members > 0)
				{
					// Get all group members of old plan
					$query->clear()
						->select('id')
						->from('#__osmembership_subscribers')
						->where('plan_id = ' . (int) $row->from_plan_id)
						->where('group_admin_id = ' . $row->user_id);
					$db->setQuery($query);
					$groupMemberIds = $db->loadColumn();

					if (count($groupMemberIds))
					{
						$app = Factory::getApplication();

						foreach ($groupMemberIds as $groupMemberId)
						{
							$groupMember = Table::getInstance('Subscriber', 'OSMembershipTable');
							$groupMember->load($groupMemberId);
							$groupMember->plan_id   = $row->plan_id;
							$groupMember->from_date = $row->from_date;
							$groupMember->to_date   = $row->to_date;
							$groupMember->published = 1;
							$groupMember->store();
							$app->triggerEvent('onMembershipActive', [$groupMember]);
						}
					}
				}
			}
		}
	}

	/**
	 * Run when a membership expired die
	 *
	 * @param   OSMembershipTableSubscriber  $row
	 */
	public function onMembershipExpire($row)
	{
		if ($row->user_id && !$row->group_admin_id)
		{
			$db    = $this->db;
			$query = $db->getQuery(true);
			$query->select('COUNT(*)')
				->from('#__osmembership_subscribers')
				->where('published = 1')
				->where('plan_id = ' . $row->plan_id)
				->where('user_id = ' . $row->user_id);
			$db->setQuery($query);
			$total = (int) $db->loadResult();

			if (!$total)
			{
				// Expired subscription, so need to trigger all group members as expired
				$query->clear()
					->select('id')
					->from('#__osmembership_subscribers')
					->where('plan_id = ' . $row->plan_id)
					->where('group_admin_id = ' . $row->user_id);
				$db->setQuery($query);
				$groupMemberIds = $db->loadColumn();

				if (count($groupMemberIds))
				{
					$app = Factory::getApplication();

					foreach ($groupMemberIds as $groupMemberId)
					{
						$groupMember = Table::getInstance('Subscriber', 'OSMembershipTable');
						$groupMember->load($groupMemberId);
						$app->triggerEvent('onMembershipExpire', [$groupMember]);
					}

					// Need to mark the subscription as expired
					$query->clear()
						->update('#__osmembership_subscribers')
						->set('published = 2')
						->where('plan_id = ' . $row->plan_id)
						->where('group_admin_id = ' . $row->user_id);
					$db->setQuery($query);
					$db->execute();
				}
			}
		}
	}
}
