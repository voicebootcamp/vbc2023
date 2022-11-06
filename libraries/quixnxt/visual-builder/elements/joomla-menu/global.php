<?php

use QuixNxt\AssetManagers\StyleManager;

if( ! class_exists('QuixJoomlaMenuElement') )
{
  include_once ( __DIR__ . '/helper.php' );
}

if ( ! defined('LOAD_JOOMLA_MENU_CSS')) {
  define('LOAD_JOOMLA_MENU_CSS', true);
  StyleManager::getInstance()->add(file_get_contents(__DIR__.'/style.css'));
}
