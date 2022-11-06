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

class Custom_Code extends \GSD\Schemas\Base
{
    /**
     * Return all the schema properties
     *
     * @return void
     */
    protected function initProps()
    {
        parent::initProps();

        // Since v5.3.1, the SchemaCleaner supports removing structured data also from the <head> that does not have the data-type="gsd" property.
        // In order to prevent the user defined custom code from being removed, we need to add the data-type property to every custom JSON+LD script.  
        $safe_custom_code = str_replace('<script type="application', '<script data-type="gsd" type="application', $this->data->get('custom_code'));
        $this->data->set('custom_code', $safe_custom_code);
    }

    /**
     * Do not clean custom script
     *
     * @return void
     */
    protected function cleanProps()
    {
    }
}