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
use NRFramework\Functions;

class Product extends \GSD\Schemas\Base
{
    /**
     * Return all the schema properties
     *
     * @return void
     */
    protected function initProps()
    {
        $props = [
            'offerPrice'      => $this->getPrice(),
            'priceValidUntil' => Helper::date($this->data->get('priceValidUntil'), true),
            'brand'           => $this->data->get('brand', Helper::getSiteName()),

            // Fallback to 'sku' property to prevent structured data warning. 
            'mpn'             => $this->data->get('mpn', $this->data->get('sku')),
        ];

        $this->data->loadArray($props);

        parent::initProps();
    }

    /**
     * Detect price range or single price. 
     *
     * @return mixed
     */
    private function getPrice()
    {
        $price = $this->data->get('offerPrice');

        if (strpos($price, '-') !== false)
        {
            $price = explode('-', $price, 2);
        }

        if (is_array($price))
        {
            return [
                Helper::formatPrice($price[0]), 
                Helper::formatPrice($price[1])
            ];
        }

        return Helper::formatPrice($price);
    }
}