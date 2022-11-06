<?php

use QuixNxt\AssetManagers\ScriptManager;
use QuixNxt\AssetManagers\StyleManager;

if ( ! defined('LOAD_COUNTER_CSS')) {
    define('LOAD_COUNTER_CSS', true);
    StyleManager::getInstance()->add(file_get_contents(__DIR__.'/style.css'));
    ScriptManager::getInstance()->add(file_get_contents(__DIR__.'/counter.js'));
}
