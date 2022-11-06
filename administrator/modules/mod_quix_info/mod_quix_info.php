<?php
/**
 * @package     Quix.Administrator
 * @subpackage  mod_quix_info
 *
 * @copyright   Copyright (C) 2005 - 2018 ThemeXpert, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;


JLoader::register('ModQuixInfoHelper', __DIR__.'/helper.php');

$isPro     = ModQuixInfoHelper::isPro();
$authorise = ModQuixInfoHelper::isProAuthinticated();

if ($isPro && $authorise) { // && !$jchOptimized
    return;
}

if (JVERSION >= 4) {
    $layout = 'joomla4';
} else {
    /** @var \Joomla\CMS\Object\CMSObject $params */
    /** @noinspection PhpIncludeInspection */
    $layout = $params->get('layout', 'default');
}
/** @noinspection PhpIncludeInspection */
require ModuleHelper::getLayoutPath('mod_quix_info', $layout);
