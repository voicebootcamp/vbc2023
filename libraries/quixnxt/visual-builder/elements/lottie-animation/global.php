<?php

use QuixNxt\AssetManagers\ScriptManager;

if ( ! defined('LOTTIE_ASSETS')) {
    define('LOTTIE_ASSETS', true);
    ScriptManager::getInstance()->addUrl(QuixAppHelper::getQuixUrl('visual-builder/elements/lottie-animation/lottie-player.js'));
}
