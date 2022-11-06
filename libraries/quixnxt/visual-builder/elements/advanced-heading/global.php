<?php

use QuixNxt\AssetManagers\StyleManager;

if ( ! defined('LOAD_ADVANCED_HEADING_CSS')) {
    define('LOAD_ADVANCED_HEADING_CSS', true);
    if ( ! elementRequestedFromBuilder()) {
        StyleManager::getInstance()->add(file_get_contents(__DIR__.'/style.css'));
    }
}
