<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2022 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

// Require library + register autoloader
require_once JPATH_ADMINISTRATOR . '/components/com_eventbooking/libraries/rad/bootstrap.php';

// Require module helper
require_once __DIR__ . '/helper.php';

$document = Factory::getDocument();
$user     = Factory::getUser();
$config   = EventbookingHelper::getConfig();
$baseUrl  = Uri::base(true);

// Load component language
EventbookingHelper::loadLanguage();

$itemId = (int) $params->get('item_id', 0) ?: EventbookingHelper::getItemid();

$rows = modEBAdvSliderHelper::getData($params);

$sliderSettings = [
	'type'       => 'loop',
	'perPage'    => $params->get('number_items', 3),
	'speed'      => (int) $params->get('speed', 300),
	'autoplay'   => (bool) $params->get('autoplay', 1),
	'arrows'     => (bool) $params->get('arrows', 1),
	'pagination' => (bool) $params->get('pagination', 1),
	'gap'        => $params->get('gap', '1em'),
];

$numberItemsXs = $params->get('number_items_xs', 0);
$numberItemsSm = $params->get('number_items_sm', 0);
$numberItemsMd = $params->get('number_items_md', 0);
$numberItemsLg = $params->get('number_items_lg', 0);

if ($numberItemsXs)
{
	$sliderSettings['breakpoints'][576]['perPage'] = $numberItemsXs;
}

if ($numberItemsSm)
{
	$sliderSettings['breakpoints'][768]['perPage'] = $numberItemsSm;
}

if ($numberItemsMd)
{
	$sliderSettings['breakpoints'][992]['perPage'] = $numberItemsMd;
}

if ($numberItemsLg)
{
	$sliderSettings['breakpoints'][1200]['perPage'] = $numberItemsLg;
}

require JModuleHelper::getLayoutPath('mod_eb_advslider', 'default');
