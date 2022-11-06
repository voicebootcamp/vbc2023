<?php

use QuixNxt\AssetManagers\StyleManager;

if ( ! defined('LOAD_PERSON_PRO_CSS')) {
  define('LOAD_PERSON_PRO_CSS', true);
  StyleManager::getInstance()->add(file_get_contents(__DIR__.'/style.css'));
}
