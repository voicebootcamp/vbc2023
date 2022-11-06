<?php

/**
 * @package         Google Structured Data
 * @version         5.1.6 Pro
 *
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2022 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace GSD\Schemas\Schemas;

// No direct access
defined('_JEXEC') or die;

use GSD\Helper;
use Joomla\String\StringHelper;

class Article extends \GSD\Schemas\Base
{
    /**
     * Return all the schema properties
     *
     * @return void
     */
    protected function initProps()
    {
        $props = [
            'publisherName' => $this->data->get('publisher_name', Helper::getSiteName()),
            'publisherLogo' => Helper::cleanImage(Helper::absURL($this->data->get('publisher_logo', Helper::getSiteLogo())))
        ];

        $this->data->loadArray($props);

        parent::initProps();
    }

    /**
     * Beyond the default housekeeping, limit the characters in the headline property to 110 in order to comply with Google's guidelines.
     *
     * Reference  https://developers.google.com/search/docs/appearance/structured-data/article#article-types
     * 
     * @return void
     */
    protected function cleanProps()
    {
        parent::cleanProps();

        $this->data->set('title', StringHelper::substr($this->data->get('title'), 0, 110));
    }
}