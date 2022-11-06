<?php
/* ======================================================
 # Web357 Framework for Joomla! - v1.9.1 (free version)
 # -------------------------------------------------------
 # For Joomla! CMS (v4.x)
 # Author: Web357 (Yiannis Christodoulou)
 # Copyright (©) 2014-2022 Web357. All rights reserved.
 # License: GNU/GPLv3, http://www.gnu.org/licenses/gpl-3.0.html
 # Website: https:/www.web357.com
 # Demo: https://demo.web357.com/joomla/
 # Support: support@web357.com
 # Last modified: Thursday 31 March 2022, 12:05:22 PM
 ========================================================= */

 
defined('_JEXEC') or die;

// Registers Web357 framework's namespace
JLoader::registerNamespace('Web357Framework', __DIR__ . '/Web357Framework/', false, false, 'psr4' );

JLoader::registerAlias('Functions', '\\Web357Framework\\Functions');
JLoader::registerAlias('VersionChecker', '\\Web357Framework\\VersionChecker');