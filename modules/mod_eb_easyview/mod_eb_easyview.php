<?php
/**
 * @package        Joomla
 * @subpackage     Events Booking
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2010 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

$menuItemId = $params->get('menu_item_id', '0');

if (!$menuItemId)
{
	return;
}

// Require library + register autoloader
require_once JPATH_ADMINISTRATOR . '/components/com_eventbooking/libraries/rad/bootstrap.php';

$menuItem = Factory::getApplication()->getMenu()->getItem($menuItemId);

if ($menuItem)
{
	EventbookingHelper::loadLanguage();
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

	$request += $_POST;

	$input  = new RADInput($request);
	$config = require JPATH_ADMINISTRATOR . '/components/com_eventbooking/config.php';
	RADController::getInstance('com_eventbooking', $input, $config)
		->execute();
}
