<?php

if ( ! defined('LOAD_DUAL_BUTTON_CSS')) {
  define('LOAD_DUAL_BUTTON_CSS', true);
  \QuixNxt\AssetManagers\StyleManager::getInstance()->add(file_get_contents(__DIR__.'/style.css'));
}
