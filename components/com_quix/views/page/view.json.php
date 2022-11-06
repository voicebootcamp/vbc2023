<?php

/**
 * @name    QuixViewPage
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Exception\ExceptionHandler;
use Joomla\CMS\Language\Text;

/**
 * View to edit
 *
 * @since  1.6
 */
class QuixViewPage extends JViewLegacy
{
    protected $state;

    protected $item;

    protected $params;


    /**
     * Display the view
     *
     * @param  string  $tpl  Template name
     *
     * @return void
     *
     * @throws Exception
     * @since 2.0.0
     */
    public function display($tpl = null)
    {
        $this->state  = $this->get('State');
        $this->item   = $this->get('Data');
        $this->params = $this->state->get('params');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            $error = new Exception(implode("\n", $errors), 500);
            ExceptionHandler::render($error);
        }

        if ( ! isset($this->item->id) || ! $this->item->id) {
            $error = new Exception(Text::_('JERROR_PAGE_NOT_FOUND'), 404);
            ExceptionHandler::render($error);
        }

        $this->item->type = 'page';

        // Check the view access to the article (the model has already computed the values).
        if ($this->item->params->get('access-view') === false && ($this->item->params->get('show_noauth', '0') === 0)) {
            $error = new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
            ExceptionHandler::render($error);
        }

        if (JFactory::getApplication()->input->get('api', false)) {
            echo new JResponseJson($this->item->data);
            exit();
        }

        try {
            QuixAppHelper::renderQuixInstance($this->item);
        } catch (Exception $e) {
            ExceptionHandler::render($e);
        }

        echo new JResponseJson($this->item);
    }
}
