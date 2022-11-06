<?php
/**
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

plgSystemQuix::initQuix();

use Joomla\CMS\Helper\ModuleHelper;

// Include the breadcrumbs functions only once
JLoader::register('ModQuixHelper', __DIR__ . '/helper.php');

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'), ENT_COMPAT, 'UTF-8');

$item = ModQuixHelper::renderShortCode($params);
require ModuleHelper::getLayoutPath('mod_quix', $params->get('layout', 'default') );
