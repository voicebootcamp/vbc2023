<?php

use QuixNxt\AssetManagers\ScriptManager;
use QuixNxt\AssetManagers\StyleManager;

if ( ! defined('LOAD_VIDEO_CSS') && elementRequestedFromBuilder()) {
    define('LOAD_VIDEO_CSS', true);
    ScriptManager::getInstance()->addUrl(QuixAppHelper::getQuixUrl('visual-builder/elements/video/assets/plyr.js'));
    StyleManager::getInstance()->addUrl(QuixAppHelper::getQuixUrl('visual-builder/elements/video/assets/plyr.css'));
}
