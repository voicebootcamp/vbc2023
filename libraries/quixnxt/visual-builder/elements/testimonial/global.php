<?php

if ( ! defined('LOAD_TESTIMONIAL_CSS')) {
  define('LOAD_TESTIMONIAL_CSS', true);
  \QuixNxt\AssetManagers\StyleManager::getInstance()->add(file_get_contents(__DIR__.'/style.css'));
}
