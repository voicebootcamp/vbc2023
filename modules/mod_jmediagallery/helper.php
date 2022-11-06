<?php
/**
 * @version    1.0.0
 * @package    com_jmedia
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2020. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined ('_JEXEC') or die ('restricted access');

jimport( 'joomla.filesystem.folder' );

/**
 * Helper class for JMedia Gallery Module
 */

 class ModImageGalleryHelper
 {
    //  Get Image Directory
    public static function imgDir($path) 
    {
        return JFolder::folders($path, $filter = '.', $recurse = true, $full = false, $exclude = array('.svn', 'CVS', '.DS_Store', '__MACOSX', '_thumbs', 'index.html', '.mp3', '.mp4'), $excludefilter = array('^\..*', '.mp3', '.mp4', '_thumbs'));
    }

    // Get Images with root path
    public static function imgFile($path, &$params)
    {
        $recurseSubFolder = $params->get('recurseSubFolder');
        return JFolder::files($path, $filter = '.', $recurse = $recurseSubFolder, $full = true, $exclude = array('.svn', 'CVS', '.DS_Store', '__MACOSX', '_thumbs', 'index.html', '.mp3', '.mp4'), $excludefilter = array('^\..*', '.*~', '.mp3', '.mp4', '_thumbs'), $naturalSort = false);
    }

    // Get selectable directory from admin
	public static function getFolder(&$params)
	{
        $rootDir = JComponentHelper::getParams('com_media');
        $getRoot = $rootDir->get('image_path');

        $folder   = $params->get('imageFolder');

        $fullPath = $getRoot . '/' . $folder;
		return $fullPath;
    }
    
    public static function getOptions(&$params, $options = array())
    {
        $filter = $params->get('enableFilter');
        $layout = $params->get('changeColumn');
        $loadBootstrap = $params->get('loadBootstrap');

        return ['filter' => $filter, 'layout' => $layout, 'loadBootstrap' => $loadBootstrap];
    }
 }