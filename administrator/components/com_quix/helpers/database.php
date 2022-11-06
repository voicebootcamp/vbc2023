<?php

/**
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    4.0.0
 */

use Joomla\Registry\Registry;

defined('_JEXEC') or die;

class QuixHelperDatabase
{

    /**
     * @param  array  $data
     *
     * @return array
     * @since 4.1.0
     */
    public static function populateCollectionDefaultData(array $data): array
    {
        if($data['builder'] === 'classic'){
            $data['builder_version'] = $data['builder_version'] ?? '1.9.3';
        }else{
            $data['builder_version'] = $data['builder_version'] ?? QUIXNXT_VERSION;
        }

        if (isset($data['metadata'])) {
            $registry = new Registry;
            $registry->loadArray($data['metadata']);
            $data['metadata'] = (string) $registry;
        }
        if (empty($data['id'])) {
            $data['uid'] = md5(uniqid(rand(), true));
        } elseif (empty($data['uid'])) {
            $data['uid'] = md5(uniqid(rand(), true));
        }

        if ( ! isset($data['catid'])) {
            $data['catid'] = 0;
        }
        if ( ! isset($data['metadata'])) {
            $data['metadata'] = '';
        }
        if ( ! isset($data['language'])) {
            $data['language'] = '';
        }
        if ( ! isset($data['checked_out'])) {
            $data['checked_out'] = 0;
        }
        if ( ! isset($data['params'])) {
            $data['params'] = '';
        }
        if ( ! isset($data['hits'])) {
            $data['hits'] = 0;
        }
        if ( ! isset($data['xreference'])) {
            $data['xreference'] = '';
        }
        if ( ! isset($data['ordering'])) {
            $data['ordering'] = 0;
        }

        return $data;
    }
}
