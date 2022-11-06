<?php
/**
 * @package		Quix
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;
use \QuixNxt\Utils\Cache;

class plgQuixContentInstallerScript
{
    public function postflight($type, $parent)
    {

        if(class_exists('Cache')){
            Cache::clear();
        }

        $session = JFactory::getSession();
        $session->set('quix_install_cleancache', 1);
    }
}
