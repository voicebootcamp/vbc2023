<?php

/**
 * @version    CVS: 1.0.0
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * View class for a list of Quix.
 *
 * @since  1.6
 */
class QuixViewCollections extends JViewLegacy
{
    protected $app;

    protected $items;

    protected $canDo;

    protected $pagination;

    protected $state;

    /**
     * Display the view
     *
     * @param  null  $tpl  Template name
     *
     * @return void
     *
     * @throws Exception
     * @since 3.0.0
     */
    public function display($tpl = null)
    {
        $this->items = $this->get('Items');
        $this->state = $this->get('State');
        $this->app   = JFactory::getApplication('administrator');

        $this->pagination = $this->get('Pagination');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        QuixHelper::addSubmenu('collections');

        $this->addToolbar();

        // $this->sidebar = JHtmlSidebar::render();

        if (JFactory::getApplication()->input->get('layout', 'default') === 'new') {
            /**
             * clear any system message to avoid interruption to this modal
             */
            JFactory::getApplication()->getMessageQueue(true);;
        }

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return void
     *
     * @throws Exception
     * @since    1.6
     */
    protected function addToolbar()
    {
        JToolBarHelper::title(JText::_('COM_QUIX_TITLE_COLLECTIONS'), 'generic');

        // require_once JPATH_COMPONENT.'/helpers/quix.php';
        //
        $this->canDo = QuixHelper::getActions($this->state->get('filter.category_id'));


        // $bar    = JToolBar::getInstance('toolbar');
        // $layout = new JLayoutFile('toolbar.collapse');
        // $bar->appendButton('Custom', $layout->render([]), 'collapse');

        // Check if the form exists before showing the add/edit buttons
        // $formPath = JPATH_COMPONENT_ADMINISTRATOR.'/views/collection';

        // if($app->input->get('legacy') == true){
        // 	if (file_exists($formPath))
        // 	{
        // 		if ($this->canDo->get('core.create'))
        // 		{
        // 			JToolBarHelper::addNew('collection.add', 'COM_QUIX_JTOOLBAR_NEW_LIB_OLD');
        // 		}
        // 	}
        // }
        // if ($this->canDo->get('core.create')) {
        //     $link    = JRoute::_(JUri::root().'index.php?option=com_quix&task=collection.add&quixlogin=true');
        //     $toolbar = JToolBar::getInstance('toolbar');
        //
        //     $toolbar->appendButton(
        //         'Custom',
        //         "<a href='#' data-toggle='modal' class='btn hasTooltip' data-target='#newLibraryModal' data-title='".JText::_(
        //             'Visual Builder'
        //         )."'><i class='icon-new'></i> ".JText::_('JTOOLBAR_NEW').'</a>',
        //         'new-visual'
        //     );
        //     // $toolbar->appendButton('Custom', "<a href='".$link ."' target='_blank' class='btn hasTooltip' data-title='".JText::_('New Library')."' data-content='".JText::_('New Library with new builder')."' data-placement='bottom'>".JText::_('JTOOLBAR_NEW')."</a>", 'new');
        //     // JToolBarHelper::addNew('collection.add', 'COM_QUIX_JTOOLBAR_NEW_LIB_OLD');
        //     // $toolbar->appendButton('Custom', '<button onclick="Joomla.submitbutton(\'collection.add\');" class="btn btn-small button-new"><span class="icon-new icon-white" aria-hidden="true"></span>'.JText::_('COM_QUIX_JTOOLBAR_NEW_LIB_OLD'), 'new');
        // }

        // if ($this->canDo->get('core.edit.state')) {
        //     if (isset($this->items[0]->state)) {
        //         JToolBarHelper::divider();
        //         JToolBarHelper::custom('collections.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
        //         JToolBarHelper::custom(
        //             'collections.unpublish',
        //             'unpublish.png',
        //             'unpublish_f2.png',
        //             'JTOOLBAR_UNPUBLISH',
        //             true
        //         );
        //     } elseif (isset($this->items[0])) {
        //         // If this component does not use state then show a direct delete button as we can not trash
        //         JToolBarHelper::deleteList('', 'collections.delete', 'JTOOLBAR_DELETE');
        //     }
        //
        //     // if (isset($this->items[0]->state))
        //     // {
        //     // 	JToolBarHelper::divider();
        //     // 	JToolBarHelper::archiveList('collections.archive', 'JTOOLBAR_ARCHIVE');
        //     // }
        //
        //     // if (isset($this->items[0]->checked_out)) {
        //     //     JToolBarHelper::custom('collections.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
        //     // }
        // }

        // Show trash and delete for components that uses the state field
        // if (isset($this->items[0]->state)) {
        //     if ($this->state->get('filter.state') == -2 && $this->canDo->get('core.delete')) {
        //         JToolBarHelper::deleteList('', 'collections.delete', 'JTOOLBAR_EMPTY_TRASH');
        //         JToolBarHelper::divider();
        //     } elseif ($this->canDo->get('core.edit.state')) {
        //         JToolBarHelper::trash('collections.trash', 'JTOOLBAR_TRASH');
        //         JToolBarHelper::divider();
        //     }
        // }
        //
        // if ($this->canDo->get('core.admin')) {
        //     JToolBarHelper::preferences('com_quix');
        // }
        //
        // if ($this->canDo->get('core.edit.state')) {
        //     JToolbarHelper::divider();
        //     $bar = JToolBar::getInstance('toolbar');
        //
        //     // Instantiate a new JLayoutFile instance and render the layout
        //     JHtml::_('behavior.modal', 'a.quixSettings');
        //     $layout = new JLayoutFile('toolbar.mysettings');
        //
        //     $bar->appendButton('Custom', $layout->render([]), 'mysettings');
        //
        //     $layout = new JLayoutFile('toolbar.clearcache');
        //     $bar->appendButton('Custom', $layout->render([]), 'clearcache');
        // }
        //
        // // Set sidebar action - New in 3.0
        // JHtmlSidebar::setAction('index.php?option=com_quix&view=collections');
        //
        // $this->extra_sidebar = '';
        //
        // JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
        // JHtmlSidebar::addFilter(
        //     JText::_('COM_QUIX_SELECT_COLLECTION_TYPE'),
        //     'filter_collection',
        //     JHtml::_(
        //         'select.options',
        //         JHtml::_('collectiontype.listLibrary', true),
        //         'value',
        //         'text',
        //         $this->state->get('filter.collection', ''),
        //         true
        //     )
        // );
        //
        // JHtmlSidebar::addFilter(
        //     JText::_('JOPTION_SELECT_PUBLISHED'),
        //     'filter_published',
        //     JHtml::_(
        //         'select.options',
        //         JHtml::_('jgrid.publishedOptions'),
        //         'value',
        //         'text',
        //         $this->state->get('filter.state'),
        //         true
        //     )
        // );
    }

    /**
     * Method to order fields
     *
     * @return void
     * @since 3.0.0
     */
    protected function getSortFields()
    {
        return [
            'a.`id`'       => JText::_('JGRID_HEADING_ID'),
            'a.`title`'    => JText::_('JGLOBAL_TITLE'),
            'a.`ordering`' => JText::_('JGRID_HEADING_ORDERING'),
            'a.`state`'    => JText::_('JSTATUS'),
            // 'a.`access`' => JText::_('COM_QUIX_COLLECTIONS_ACCESS'),
            // 'a.`language`' => JText::_('JGRID_HEADING_LANGUAGE'),
        ];
    }
}
