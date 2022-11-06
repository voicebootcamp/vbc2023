<?php

/**
 * @package         Google Structured Data
 * @version         5.1.6 Pro
 *
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/script.install.helper.php';

class PlgGSDGridboxInstallerScript extends PlgGSDGridboxInstallerScriptHelper
{
    public $alias = 'gridbox';
    public $extension_type = 'plugin';
    public $plugin_folder = "gsd";
    public $show_message = false;
    public $autopublish = false;
}