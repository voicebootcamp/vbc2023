<?php

// if ( ! defined('LOAD_SOCIAL_ICON_CSS')) {
//   define('LOAD_SOCIAL_ICON_CSS', true);
//   \QuixNxt\AssetManagers\StyleManager::getInstance()->add(file_get_contents(__DIR__.'/style.css'));
// }
//

use QuixNxt\AssetManagers\StyleManager;

if ( ! defined('LOAD_SOCIAL_ICON_CSS')) {
    define('LOAD_SOCIAL_ICON_CSS', true);
    if ( ! elementRequestedFromBuilder()) {
        StyleManager::getInstance()->add(file_get_contents(__DIR__.'/style.css'));
    }
}
