<?php
/**
 * @version    1.0.0
 * @package    com_jmedia
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2020. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined ('_JEXEC') or die ('restricted access');

JLoader::register('ModImageGalleryHelper', __DIR__ . '/helper.php');

use Joomla\CMS\Factory;
$document = Factory::getDocument();

$getFolder = ModImageGalleryHelper::getFolder($params);
$getOptions = ModImageGalleryHelper::getOptions($params, $options = []);

$folder = ModImageGalleryHelper::imgDir($getFolder);
$files = ModImageGalleryHelper::imgFile($getFolder, $params);

// Add style and script file
if ($getOptions['loadBootstrap'] != 0) {
    $document->addStyleSheet(JURI::base() . '/modules/mod_jmediagallery/assets/bootstrap.min.css');
}
$document->addStyleSheet(JURI::base() . '/modules/mod_jmediagallery/assets/style.css');
$document->addScript('http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js');
$document->addScript(JURI::base() . '/modules/mod_jmediagallery/assets/jquery.mixitup.min.js');
$document->addScript(JURI::base() . '/modules/mod_jmediagallery/assets/jquery.magnific-popup.min.js');
$document->addScript(JURI::base() . '/modules/mod_jmediagallery/assets/script.js');


$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'), ENT_COMPAT, 'UTF-8');

require JModuleHelper::getLayoutPath('mod_jmediagallery', $params->get('layout', 'default'));
