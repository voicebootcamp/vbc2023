<?php
defined('JPATH_PLATFORM') or die;

$php_config = array (
  'instantloading' => 
  array (
    0 => 'trigger="mouseover"',
    1 => 'intensity="65"',
    2 => 'filter-type="1"',
    3 => 'filters="[]"',
  ),
  'headers' => 
  array (
    'X-Content-Type-Options' => 
    array (
      0 => 'nosniff',
      1 => true,
    ),
    'X-XSS-Protection' => 
    array (
      0 => '1; mode=block',
      1 => true,
    ),
  ),
);