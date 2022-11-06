<?php

use QuixNxt\AssetManagers\StyleManager;

if ( ! defined('LOAD_PRICING_TABLE_CSS')) {
  define('LOAD_PRICING_TABLE_CSS', true);
  StyleManager::getInstance()->add(file_get_contents(__DIR__.'/style.css'));
}
