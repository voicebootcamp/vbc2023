<?php

/**
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    3.0.0
 */

// No direct access
defined('_JEXEC') or die;

/**
 * View class for a list of Quix.
 *
 * @since  1.0.0
 */
class QuixViewPages extends JViewLegacy
{
  protected $items;

  protected $pagination;

  protected $state;
  protected $canDo;

  protected $sidebar;

  /**
   * Display the view
   *
   * @param   string  $tpl  Template name
   *
   * @return void
   *
   * @throws Exception
   * @since 3.0.0
   */
  public function display($tpl = null): void
  {
    $this->state      = $this->get('State');
    $this->items      = $this->get('Items');
    $this->pagination = $this->get('Pagination');

    // Check for errors.
    if (count($errors = $this->get('Errors'))) {
      throw new Exception(implode("\n", $errors));
    }

    QuixHelper::addSubmenu('pages');

    $this->addToolbar();

//        if (JVERSION < 4) {
//            $this->sidebar = false; //JHtmlSidebar::render();
//        }

    parent::display($tpl);
  }

  /**
   * Add the page title and toolbar.
   *
   * @return void
   *
   * @throws \Exception
   * @since    1.6
   */
  protected function addToolbar(): void
  {
    $this->canDo = QuixHelper::getActions();

    JToolBarHelper::title(JText::_('COM_QUIX_TITLE_PAGES'), 'generic');

    if (JVERSION >= 4) {
      JToolbarHelper::preferences('com_quix');

      if ($this->canDo->get('core.create')) {
        $link    = JRoute::_(JUri::root() . 'index.php?option=com_quix&task=page.add&quixlogin=true');
        $toolbar = JToolBar::getInstance('toolbar');
        $toolbar->appendButton(
          'Custom',
          "<a href='" . $link . "' target='_blank' class='btn btn-primary hasTooltip' data-title='" . JText::_(
            'Visual Builder'
          ) . "' data-content='" . JText::_(
            'With Visual Builder'
          ) . "' data-placement='top'><i class='icon-new'></i> " . JText::_('JTOOLBAR_NEW') . '</a>',
          'new-visual'
        );
      }
    } else {
      // Check if the form exists before showing the add/edit buttons
      $formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/page';

      $bar    = JToolBar::getInstance('toolbar');
      $layout = new JLayoutFile('toolbar.collapse');
      $bar->appendButton('Custom', $layout->render([]), 'collapse');

      if (file_exists($formPath) && $this->canDo->get('core.create')) {
        // if ($app->input->get('legacy', true) === true) {
        //     JToolBarHelper::addNew('page.add', 'COM_QUIX_JTOOLBAR_NEW_OLD');
        // }

        $link    = JRoute::_(JUri::root() . 'index.php?option=com_quix&task=page.add&quixlogin=true');
        $toolbar = JToolBar::getInstance('toolbar');
        $toolbar->appendButton(
          'Custom',
          "<a href='" . $link . "' target='_blank' class='btn btn-primary hasTooltip' data-title='" . JText::_(
            'Visual Builder'
          ) . "' data-content='" . JText::_(
            'With Visual Builder'
          ) . "' data-placement='top'><i class='icon-new'></i> " . JText::_('JTOOLBAR_NEW') . '</a>',
          'new-visual'
        );
      }

      if ($this->canDo->get('core.edit.state')) {
        if (isset($this->items[0]->state)) {
          JToolBarHelper::divider();
          JToolBarHelper::custom('pages.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
          JToolBarHelper::custom(
            'pages.unpublish',
            'unpublish.png',
            'unpublish_f2.png',
            'JTOOLBAR_UNPUBLISH',
            true
          );
        } elseif (isset($this->items[0])) {
          // If this component does not use state then show a direct delete button as we can not trash
          JToolBarHelper::deleteList('', 'pages.delete', 'JTOOLBAR_DELETE');
        }

        if (isset($this->items[0]->checked_out)) {
          JToolBarHelper::custom('pages.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
        }
      }

      // Show trash and delete for components that uses the state field
      if (isset($this->items[0]->state)) {
        if ($this->state->get('filter.state') === -2 && $this->canDo->get('core.delete')) {
          JToolBarHelper::deleteList('', 'pages.delete', 'JTOOLBAR_EMPTY_TRASH');
          JToolBarHelper::divider();
        } elseif ($this->canDo->get('core.edit.state')) {
          JToolBarHelper::trash('pages.trash', 'JTOOLBAR_TRASH');
          JToolBarHelper::divider();
        }
      }

      if ($this->canDo->get('core.admin')) {
        $bar    = JToolBar::getInstance('toolbar');
        $layout = new JLayoutFile('toolbar.options');
        $bar->appendButton('Custom', $layout->render([]), 'options');
      }

      if ($this->canDo->get('core.edit.state')) {
        JToolbarHelper::divider();
        $bar = JToolBar::getInstance('toolbar');

        // Instantiate a new JLayoutFile instance and render the layout
        $layout = new JLayoutFile('toolbar.mysettings');
        $bar->appendButton('Custom', $layout->render([]), 'mysettings');

        $layout = new JLayoutFile('toolbar.clearcache');
        $bar->appendButton('Custom', $layout->render([]), 'clearcache');
      }

      // Set sidebar action - New in 3.0
//            JHtmlSidebar::setAction('index.php?option=com_quix&view=pages');
//
//            $this->extra_sidebar = '';
//            JHtmlSidebar::addFilter(
//                JText::_('JOPTION_SELECT_PUBLISHED'),
//                'filter_published',
//                JHtml::_(
//                    'select.options',
//                    JHtml::_('jgrid.publishedOptions'),
//                    'value',
//                    'text',
//                    $this->state->get('filter.state'),
//                    true
//                )
//            );
//            JHtmlSidebar::addFilter(
//                JText::_('JOPTION_SELECT_LANGUAGE'),
//                'filter_language',
//                JHtml::_(
//                    'select.options',
//                    JHtml::_('contentlanguage.existing', true, true),
//                    'value',
//                    'text',
//                    $this->state->get('filter.language'),
//                    true
//                )
//            );
    }
  }

  /**
   * Method to order fields
   *
   * @return array
   * @since 3.0.0
   */
  protected function getSortFields(): array
  {
    return [
      'a.`id`'       => JText::_('JGRID_HEADING_ID'),
      'a.`title`'    => JText::_('COM_QUIX_PAGES_TITLE'),
      'a.`ordering`' => JText::_('JGRID_HEADING_ORDERING'),
      'a.`state`'    => JText::_('JSTATUS'),
      'a.`access`'   => JText::_('COM_QUIX_PAGES_ACCESS'),
      'a.`language`' => JText::_('JGRID_HEADING_LANGUAGE'),
    ];
  }
}
