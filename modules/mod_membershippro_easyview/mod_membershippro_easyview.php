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

$menuItemId = $params->get('menu_item_id', '0');

if (!$menuItemId)
{
	return;
}

$menuItem = Factory::getApplication()->getMenu()->getItem($menuItemId);

if ($menuItem)
{
	// Require library + register autoloader
	require_once JPATH_ADMINISTRATOR . '/components/com_osmembership/loader.php';

	OSMembershipHelper::loadLanguage();

	$request              = $menuItem->query;
	$request['Itemid']    = $menuItem->id;
	$request['hmvc_call'] = 1;

	if (!isset($request['limitstart']))
	{
		$appInput   = Factory::getApplication()->input;
		$start      = $appInput->get->getInt('start', 0);
		$limitStart = $appInput->get->getInt('limitstart', 0);

		if ($start && !$limitStart)
		{
			$limitStart = $start;
		}

		$request['limitstart'] = $limitStart;
	}

	$input  = new MPFInput($request);
	$config = [
		'default_controller_class' => 'OSMembershipController',
		'default_view'             => 'plans',
		'class_prefix'             => 'OSMembership',
		'language_prefix'          => 'OSM',
		'remember_states'          => false,
		'ignore_request'           => false,
	];

//Initialize the controller, execute the task (display) to display the view
	MPFController::getInstance('com_osmembership', $input, $config)
		->execute();
}



