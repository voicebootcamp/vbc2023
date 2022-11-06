<?php

if ( ! defined('LOAD_ICON_LIST_CSS')) {
  define('LOAD_ICON_LIST_CSS', true);
  \QuixNxt\AssetManagers\StyleManager::getInstance()->add(file_get_contents(__DIR__.'/style.css'));
}
