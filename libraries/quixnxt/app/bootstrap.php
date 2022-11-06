<?php
/**
 * @package    Quixnxt App
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    3.0.0
 */
defined('_JEXEC') or die;
jimport('quixnxt.vendor.autoload');

// Load dependent helpers
include __DIR__ . '/functions.php';

/**
 * Load constants by ensuring that if previously declared, then dont duplicate.
 */
try {
    QuixAppHelper::setQuixConstants();
} catch (Exception $e) {
    throw new RuntimeException('QuixNext application bootstrapping has failed.');
}
