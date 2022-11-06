<?php
/**
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;

defined('_JEXEC') or die;
if (JVERSION < 4) :
    // Initialise variables.
    $app          = Factory::getApplication();
    $hideMainMenu = $app->input->get('hidemainmenu');

    $show_quix_menu = $params->get('show_quix_menu', 1);
    if ($show_quix_menu) {
        $hideMainMenu = false;
    }

    // Render the module layout
    require ModuleHelper::getLayoutPath('mod_quix_menu', $params->get('layout', 'default'));
else:
    require ModuleHelper::getLayoutPath('mod_quix_menu', $params->get('layout', 'j4'));
endif;
