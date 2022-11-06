<?php

/**
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    1.0.0
 */
// No direct access

defined('_JEXEC') or die;

use Joomla\CMS\Exception\ExceptionHandler;

/**
 * View to edit
 *
 * @since  1.6
 */
class QuixViewCollection extends JViewLegacy
{
    protected $state;

    protected $item;

    protected $form;

    protected $params;

    /**
     * Display the view
     *
     * @param  string  $tpl  Template name
     *
     * @return void
     *
     * @throws Exception
     * @since 3.0.0
     */
    public function display($tpl = null)
    {
        $app          = JFactory::getApplication();
        $this->state  = $this->get('State');
        $this->item   = $this->get('Data');
        $this->params = $app->getParams('com_quix');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        try {
            QuixAppHelper::renderQuixInstance($this->item);
        } catch (Exception $e) {
            ExceptionHandler::render($e);
        }

        parent::display($tpl);
    }
}
