<?php
/**
 * @version    CVS: 1.0.0
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Message configuration model.
 *
 * @since  1.6
 */
class QuixModelDashboard extends JModelLegacy
{
    /**
     * Constructor. Initialises variables.
     *
     * @param   mixed  $properties  - See JObject::__construct
     */
    public function __construct($properties = null)
    {
        // Construct JObject
        parent::__construct($properties);
    }
}
