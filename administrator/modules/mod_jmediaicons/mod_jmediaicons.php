<?php
/**
 * @version    1.0.0
 * @package    com_jmedia
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2020. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die ('restricted access');

if (JVERSION < 4) {
    return;
}
$document = JFactory::getDocument();
$input    = JFactory::getApplication()->input;

$document->addStyleSheet(JURI::base(true).'/modules/mod_jmediaicons/tmpl/css/style.css');

require JModuleHelper::getLayoutPath('mod_jmediaicons', $params->get('layout', 'default'));
