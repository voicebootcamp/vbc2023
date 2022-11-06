<?php
/**
 * @package         Regular Labs Library
 * @version         22.10.1331
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://regularlabs.com
 * @copyright       Copyright © 2022 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

namespace RegularLabs\Library;

defined('_JEXEC') or die;

class SimpleCategory
{
    public static function save($table, $item_id, $category, $id_column = 'id')
    {
        $db = DB::get();

        $query = $db->getQuery(true)
            ->select(DB::quoteName($id_column))
            ->from(DB::quoteName('#__' . $table))
            ->where(DB::quoteName($id_column) . ' = ' . $item_id);

        $item_exists = $db->setQuery($query)->loadResult();

        if ($item_exists)
        {
            $query = $db->getQuery(true)
                ->update(DB::quoteName('#__' . $table))
                ->set(DB::quoteName('category') . ' = ' . DB::quote($category))
                ->where(DB::quoteName($id_column) . ' = ' . $item_id);

            $db->setQuery($query)->execute();

            return;
        }

        $query = 'SHOW COLUMNS FROM `#__' . $table . '`';
        $db->setQuery($query);

        $columns = $db->loadColumn();

        $values             = array_fill_keys($columns, '');
        $values[$id_column] = $item_id;
        $values['category'] = $category;

        $query = $db->getQuery(true)
            ->insert(DB::quoteName('#__' . $table))
            ->columns(DB::quoteName($columns))
            ->values(implode(',', DB::quote($values)));

        $db->setQuery($query)->execute();
    }
}
