<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jmedia
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
include JPATH_LIBRARIES.'/jmedia/php-server/vendor/autoload.php';

/**
 * File JMedia Controller
 *
 * @since  1.6
 */
class JMediaControllerApi extends JControllerLegacy
{
    /**
     * Fonts JSON
     *
     * @return  json
     *
     * @since   1.0
     */
    public function fontJSON(): json
    {
        // Check for request forgeries
        // $this->checkToken('request');

        $path  = JPATH_SITE.'/media/com_jmedia/json/qx-fonts.json';
        $fonts = file_get_contents($path);
        header('Content-Type: application/json');
        echo $fonts;
        jexit();
    }
}
