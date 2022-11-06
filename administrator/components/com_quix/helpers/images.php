<?php

/**
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    3.0.0
 */
// No direct access
defined('_JEXEC') or die;
// Import filesystem libraries. Perhaps not necessary, but does not hurt
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * Installation class to perform additional changes during install/uninstall/update
 *
 * @package     Joomla.Administrator
 * @subpackage  com_quix
 * @since       1.3.0
 */
class QuixHelperImages
{
  /*
  * add stats
  */
  public static function log($id, $type, $data = [])
  {
    if (!$id)
    {
      return;
    }

    // Create and populate an object.
    $obj                = new stdClass();
    $obj->id            = 0;
    $obj->item_id       = $id;
    $obj->item_type     = $type;
    $obj->images_count  = isset($data['images_count']) ? $data['images_count'] : 0;
    $obj->original_size = isset($data['original_size']) ? $data['original_size'] : 0;
    $obj->optimise_size = isset($data['optimise_size']) ? $data['optimise_size'] : 0;
    $obj->mobile_size   = isset($data['mobile_size']) ? $data['mobile_size'] : 0;
    $obj->params        = isset($data['extra_information']) ? $data['extra_information'] : '';

    $jsonData = json_encode($obj);

    // Then we create the sub folder called jpg
    $folder_path = JPATH_SITE . '/media/quix/cache/img-stats';

    if (!JFolder::exists($folder_path))
    {
      if (!JFolder::create($folder_path))
      {
        return false;
      }
    }

    $filepath = $folder_path . '/' . $type . '-' . $id . '.json';

    try
    {
      $myfile = fopen($filepath, 'w');
      fwrite($myfile, $jsonData);
      fclose($myfile);
    }
    catch (Exception $e)
    {
      return false;
    }

    // if (!$id) {
    //     return;
    // }

    $isExisting = self::checkNew($id, $type);

    // Create and populate an object.
    $obj                = new stdClass();
    $obj->id            = $isExisting ? $isExisting : 0;
    $obj->item_id       = $id;
    $obj->item_type     = $type;
    $obj->images_count  = isset($data['images_count']) ? $data['images_count'] : 0;
    $obj->original_size = isset($data['original_size']) ? $data['original_size'] : 0;
    $obj->optimise_size = isset($data['optimise_size']) ? $data['optimise_size'] : 0;
    $obj->mobile_size   = isset($data['mobile_size']) ? $data['mobile_size'] : 0;
    // $obj->params = isset($data['extra_information']) ? $data['extra_information'] : '';

    if ($isExisting)
    {
      $result = self::updateStats($obj);
    }
    else
    {
      $result = self::addStats($obj);
    }

    // return $result;
  }

  /*
  * Check new
  */
  public static function checkNew($id, $type)
  {
    $db    = JFactory::getDbo();
    $query = $db->getQuery(true);

    $query
      ->select('id')
      ->from('#__quix_imgstats')
      ->where('item_id = ' . intval($id))
      ->where('item_type = "' . $type . '"');

    $db->setQuery($query);

    return $db->loadResult();
  }

  /*
  * add stats
  */
  public static function addStats($obj)
  {
    // Insert the object into the user obj table.
    return JFactory::getDbo()->insertObject('#__quix_imgstats', $obj);
  }

  /*
  * update stats
  */
  public static function updateStats($obj)
  {
    // Update their details in the users table using id as the primary key.
    return JFactory::getDbo()->updateObject('#__quix_imgstats', $obj, 'id');
  }

  /*
  * update stats
  */
  public static function removeStats($item_id, $item_type)
  {
    $db = JFactory::getDbo();

    $query = $db->getQuery(true);

    // delete all custom keys for user 1001.
    $conditions = [
      $db->quoteName('item_id') . ' = ' . $item_id,
      $db->quoteName('item_type') . ' = ' . $item_type
    ];

    $query->delete($db->quoteName('#__quix_imgstats'));
    $query->where($conditions);

    $db->setQuery($query);

    return $db->execute();
  }

  /*
   * Check new
   */
  public static function get($id, $type)
  {
    // Then we create the subfolder called jpg
    $folder_path = JPATH_SITE . '/media/quix/cache/img-stats';

    if (!JFolder::exists($folder_path))
    {
      if (!JFolder::create($folder_path))
      {
        return new stdClass;
      }
    }

    $filepath = $folder_path . '/' . $type . '-' . $id . '.json';

    if (file_exists($filepath))
    {
      // Read JSON file
      $json = file_get_contents($filepath);

      //Decode JSON
      $json_data = json_decode($json);

      //return data
      return $json_data;
    }
    else
    {
      return null;
    }

    // $db = JFactory::getDbo();
    // $query = $db->getQuery(true);

    // $query
    //     ->select('*')
    //     ->from('#__quix_imgstats')
    //     ->where('item_id = ' . intval($id))
    //     ->where('item_type = "' . $type . '"');

    // $db->setQuery($query);
    // return $db->loadObject();
  }

  /**
   * Log and clean up image optimization data.
   *
   * @param   array  $imageOptimization
   *
   * @return void
   */
  public static function logAndCleanUp($item, $type, array $imageOptimization)
  {
    $model = QuixHelperImages::get($item->id, $type);

    if (!is_null($model))
    {
      $optimizedImages = json_decode($model->params)->optimized_images;
      $paralizedImages = array_diff($optimizedImages, $imageOptimization['optimized_images']);

      foreach ($paralizedImages as $image)
      {
        $splitSrc = explode('.', $image);
        array_pop($splitSrc);
        $image = implode('', $splitSrc);
        $path  = JPATH_ROOT . '/media/quix/cache/images' . $image;

        array_map('unlink', glob($path . '-*.*'));
        array_map('unlink', glob($path . '.*'));
      }
    }

    QuixHelperImages::log($item->id, $type, $data = [
      'images_count'      => $imageOptimization['count'],
      'original_size'     => $imageOptimization['original_size'],
      'optimise_size'     => $imageOptimization['optimise_size'],
      'mobile_size'       => $imageOptimization['mobile_size'],
      'extra_information' => json_encode([
        'optimized_images' => $imageOptimization['optimized_images']
      ])
    ]);
  }
}
