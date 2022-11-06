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
use Joomla\Utilities\ArrayHelper;

JLoader::register('OSMembershipModelSubscriptions', JPATH_ADMINISTRATOR . '/components/com_osmembership/model/subscriptions.php');

class OSMembershipModelSubscribers extends OSMembershipModelSubscriptions
{
	/**
	 * Builds a WHERE clause for the query
	 *
	 * @param   JDatabaseQuery  $query
	 *
	 * @return $this
	 */
	protected function buildQueryWhere(JDatabaseQuery $query)
	{
		$user = Factory::getUser();

		if (!$user->authorise('core.admin', 'com_osmembership'))
		{
			$query->where('tbl.plan_id IN (SELECT id FROM #__osmembership_plans WHERE subscriptions_manage_user_id IN (0, ' . $user->id . '))');
		}

		$active = Factory::getApplication()->getMenu()->getActive();
		$params = OSMembershipHelper::getViewParams($active, ['subscribers']);

		if ($params->get('plan_ids'))
		{
			$query->where('tbl.plan_id IN (' . implode(',', ArrayHelper::toInteger($params->get('plan_ids'))) . ')');
		}

		if ($params->get('exclude_plan_ids'))
		{
			$query->where('tbl.plan_id NOT IN (' . implode(',', ArrayHelper::toInteger($params->get('exclude_plan_ids'))) . ')');
		}

		return parent::buildQueryWhere($query);
	}
}
