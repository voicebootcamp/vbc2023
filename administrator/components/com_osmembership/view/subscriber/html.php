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
use Joomla\CMS\Plugin\PluginHelper;

class OSMembershipViewSubscriberHtml extends MPFViewItem
{
	public function display()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$item  = $this->model->getData();

		$query->select('a.*, b.title AS plan_title, b.lifetime_membership, b.enable_renewal, b.recurring_subscription')
			->from('#__osmembership_subscribers AS a')
			->innerJoin('#__osmembership_plans AS b ON a.plan_id = b.id')
			->where('a.profile_id = ' . $item->id)
			->order('a.id DESC');
		$db->setQuery($query);
		$items = $db->loadObjectList();

		if (OSMembershipHelper::isUniquePlan($item->user_id))
		{
			$planId = $item->plan_id;
		}
		else
		{
			$planId = 0;
		}

		//Form fields
		$rowFields = OSMembershipHelper::getProfileFields($planId, true, $item->language);

		// Disable readonly for adding/editing subscription
		foreach ($rowFields as $rowField)
		{
			$rowField->readonly = 0;
		}

		$data = OSMembershipHelper::getProfileData($item, $planId, $rowFields);

		if (!isset($data['country']) || !$data['country'])
		{
			$config          = OSMembershipHelper::getConfig();
			$data['country'] = $config->default_country;
		}

		$form = new MPFForm($rowFields);
		$form->setData($data)->bindData();
		$form->buildFieldsDependency();

		//Trigger third party add-on
		PluginHelper::importPlugin('osmembership');

		//Trigger plugins
		$results       = Factory::getApplication()->triggerEvent('onProfileDisplay', [$item]);
		$this->item    = $item;
		$this->config  = OSMembershipHelper::getConfig();
		$this->plugins = $results;
		$this->items   = $items;
		$this->form    = $form;
		parent::display();
	}

	protected function addToolbar()
	{

	}
}
