<?php

use QuixNxt\AssetManagers\ScriptManager;
use QuixNxt\AssetManagers\StyleManager;

if ( ! defined('LOAD_ANIMATED_HEADLINE_CSS')) {
    define('QX_ELEMENT_ANIMATED_HEADLINE_CSS', true);
    if (elementRequestedFromBuilder()) {
        ScriptManager::getInstance()->addUrl(\QuixAppHelper::getQuixUrl('visual-builder/elements/animated-headline/script.js'));
    } else {
        StyleManager::getInstance()->add(file_get_contents(__DIR__.'/style.css'));
        ScriptManager::getInstance()->add(file_get_contents(__DIR__.'/script.js'));
    }
}
