<?php
/*------------------------------------------------------------------------
# AA User IP and Location
# ------------------------------------------------------------------------
# author    AA Extensions https://aaextensions.com
# Copyright (C) 2018 AA Extensions. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: https://aaextensions.com
-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access');

$doc = JFactory::getDocument();

JHtml::_('jquery.framework'); //Jquery Library Activated

$modulePath = JURI::base() . 'modules/mod_aa_uipl/assets/';

//Adding CSS Files
$doc->addStyleSheet($modulePath.'css/style.css');



$uipl_ip                      = $params->get('uipl_ip');
$uipl_country                 = $params->get('uipl_country');
$uipl_countrycode             = $params->get('uipl_countrycode');

$uipl_region                  = $params->get('uipl_region');
$uipl_city                    = $params->get('uipl_city');

$uipl_latitude                = $params->get('uipl_latitude');
$uipl_longitude               = $params->get('uipl_longitude');
$uipl_timezone                = $params->get('uipl_timezone');
$uipl_isp                     = $params->get('uipl_isp');
$uipl_countryflag             = $params->get('uipl_countryflag');

$uipl_fwidth                  = $params->get('uipl_fwidth');
$uipl_cwidth                  = $params->get('uipl_cwidth');

$uipl_bcolor                  = $params->get('uipl_bcolor');
$uipl_fcolor                  = $params->get('uipl_fcolor');

$custom_css                   = $params->get('custom_css');
$moduleclass_sfx              = $params->get('moduleclass_sfx','');






// Add Custom CSS

$doc->addStyleDeclaration($custom_css);


require(JModuleHelper::getLayoutPath('mod_aa_uipl'));