<?php

if ( ! defined('LOAD_SMART_TAB_CSS')) {
  define('LOAD_SMART_TAB_CSS', true);
  \QuixNxt\AssetManagers\StyleManager::getInstance()->add(file_get_contents(__DIR__.'/style.css'));
}
