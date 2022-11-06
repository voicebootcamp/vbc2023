<?php
/**
 * @package         Sourcerer
 * @version         9.3.0PRO
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://regularlabs.com
 * @copyright       Copyright © 2022 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

use Joomla\CMS\Filesystem\File as JFile;
use Joomla\CMS\Filesystem\Folder as JFolder;

class PlgEditorsXtdSourcererInstallerScript
{
    public function postflight($install_type, $adapter)
    {
        if ( ! in_array($install_type, ['install', 'update']))
        {
            return true;
        }

        self::deleteJoomla3Files();

        return true;
    }

    private static function delete($files = [])
    {
        foreach ($files as $file)
        {
            if (is_dir($file))
            {
                JFolder::delete($file);
            }

            if (is_file($file))
            {
                JFile::delete($file);
            }
        }
    }

    private static function deleteJoomla3Files()
    {
        self::delete(
            [
                JPATH_SITE . '/plugins/editors-xtd/sourcerer/layouts',
                JPATH_SITE . '/plugins/editors-xtd/sourcerer/fields.xml',
                JPATH_SITE . '/plugins/editors-xtd/sourcerer/helper.php',
                JPATH_SITE . '/plugins/editors-xtd/sourcerer/popup.php',
                JPATH_SITE . '/plugins/editors-xtd/sourcerer/popup.tmpl.php',
            ]
        );
    }
}
