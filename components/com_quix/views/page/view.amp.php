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
use Joomla\Registry\Registry;


/**
 * View to edit
 *
 * @since  1.0.0
 */
class QuixViewPage extends JViewLegacy
{
    public $app;

    public $document;

    protected $state;

    protected $item;

    protected $params;

    protected $config;

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
        /**
         * we are not going to provide support for AMP
         * @since 4.0.0-rc1
         */
        $uri = JUri::getInstance();
        $uri->delVar('format');
        JFactory::getApplication()->redirect($uri->toString());


        $this->app      = JFactory::getApplication();
        $this->document = JFactory::getDocument();

        $this->state  = $this->get('State');
        $this->item   = $this->get('Data');
        $this->params = $this->state->get('params');
        $this->config = JComponentHelper::getComponent('com_quix')->params;


        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            $error = new Exception(implode("\n", $errors), 500);
            ExceptionHandler::render($error);
        }

        if (isset($this->item->id) && $this->item->id) {
            // hardcode type for builder use, so we know its page
            $this->item->type = 'page';

            // Check the view access to the article (the model has already computed the values).
            if ($this->item->params->get('access-view') == false && ($this->item->params->get('show_noauth', '0') == '0')) {
                $error = new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
                ExceptionHandler::render($error);
            }

            // count hits
            $this->get('Hit');

            // render quix content and trigger content plugin
            // $this->item->text = quixRenderItem($this->item);
            $this->item->text = ''; //QuixAppHelper::renderQuixInstance($this->item);
        } else {
            $error = new Exception(JText::_('JERROR_PAGE_NOT_FOUND'), 404);
            ExceptionHandler::render($error);
        }

        $this->amp_html = QuixFrontendHelperAMP::prepareOutputAmp($this->item->text);

        //add custom code to jdoc
        $registry = new Registry;
        $params   = $registry->loadString($this->item->params);

        // now prepare document for meta info

        $this->setLayout('amp');

        parent::display($tpl);
    }
}
