<?php
/**
 * @package    Quix
 * @author    ThemeXpert http://www.themexpert.com
 * @copyright  Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license  GNU General Public License version 3 or later; see LICENSE.txt
 * @since    1.0.0
 */

use QuixNxt\Elements\ElementBag;

defined('_JEXEC') or die;

class plgQuixContent extends JPlugin
{
    /**
     * Load the language file on instantiation.
     *
     * @var    boolean
     * @since  3.1
     */
    protected $autoloadLanguage = true;

    public function __construct($subject, $config)
    {
        parent::__construct($subject, $config);

    }

    /**
     * Listener for the `onRegisterQuixElements` event
     *
     * @return  void
     *
     * @since   3.0.0
     */
    public function onRegisterQuixElements()
    {
        if(JVERSION >= 4){
            return;
        }
        // // register your elements path
        // $element_path = JPATH_SITE . '/plugins/quix/content';
        // // register assets loading url
        // $element_url = JUri::root() . '/plugins/quix/content';
		//
        // if (file_exists(JPATH_SITE . '/libraries/quixnxt/app/bootstrap.php')) {
        //     quix()->getElementsBag()->fill($element_path . '/elements', $element_url . '/elements', [], 'frontend');
        // }

        ElementBag::register(__DIR__.'/elements');
    }
}
