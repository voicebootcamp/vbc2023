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

require_once JPATH_COMPONENT_SITE.'/helpers/route.php';

/**
 * HTML page View class for the quix component
 *
 * @since  1.5
 */
class QuixViewForm extends JViewLegacy
{
    protected $form;

    protected $doc;

    protected $item;

    protected $params;

    protected $user;

    protected $builderTemplate;

    protected $return_page;

    protected $state;

    protected $type;

    protected $config;

    protected $iframeUrl;

    protected $Itemid;

    /**
     * Should we show a captcha form for the submission of the page?
     *
     * @var   bool
     * @since 3.7.0
     */
    protected $captchaEnabled = false;

    /**
     * Execute and display a template script.
     *
     * @param  null  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  mixed  A string if successful, otherwise an Error object.
     * @throws Exception
     * @since 1.0.0
     */
    public function display($tpl = null)
    {
        if(defined('QUIX_BUILDER_VIEW')){
            define('QUIX_BUILDER_VIEW', true);
        }

        $user         = JFactory::getUser();
        $app          = JFactory::getApplication();
        $this->doc    = JFactory::getDocument();
        $this->config = JComponentHelper::getComponent('com_quix')->params;
        $this->type   = $app->input->get('type', 'page');

        // Get model data.
        $this->state = $this->get('State');
        $this->item  = $this->get('Item');

        $this->form = $this->get('Form');

        if (empty($this->item->id)) {
            $authorised = $user->authorise('core.create', 'com_quix') || count(
                    $user->getAuthorisedCategories('com_quix', 'core.create')
                );
        } else {
            $authorised = $this->item->params->get('access-edit');
        }

        if ($authorised !== true) {
            $return = base64_encode(JUri::getInstance());
            if ($user->get('guest')) {
                $login_url_with_return = 'index.php?option=com_users&view=login&return='.$return;
                $app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'notice');
                $app->redirect($login_url_with_return, 403);
            } else {
                $app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
                $app->redirect($return, 403);
            }
        }

        // TODO:: is checking
        $checkedOut = ($this->item->checked_out == 0 || $this->item->checked_out == $user->id);
        if ( ! $checkedOut) {
            $uri = 'index.php?option=com_quix&view='.$this->type.'&id='.$this->item->id;
            $app->enqueueMessage(JText::_('JLIB_APPLICATION_ERROR_CHECKIN_USER_MISMATCH'), 'error');
            $app->redirect($uri, 403);
        }
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JErrorPage::render($errors);
        }

        // Create a shortcut to the parameters.
        $params       = &$this->state->params;
        $this->params = $params;

        // Override global params with page specific params
        $this->params->merge($this->item->params);
        $this->user = $user;

        // Propose current language as default when creating new page
        if (empty($this->item->id) && JLanguageMultilang::isEnabled()) {
            $lang = JFactory::getLanguage()->getTag();
            $this->form->setFieldAttribute('language', 'default', $lang);
        }

        $captchaSet = $params->get('captcha', JFactory::getApplication()->get('captcha', '0'));
        foreach (JPluginHelper::getPlugin('captcha') as $plugin) {
            if ($captchaSet === $plugin->name) {
                $this->captchaEnabled = true;
                break;
            }
        }

        $route        = QuixFrontendHelperRoute::getPageRoute($this->item->id);
        $uri          = JURI::getInstance($route);
        $this->Itemid = $uri->getVar('Itemid', '');

        if ( ! $this->Itemid) {
            $menu = JMenu::getInstance('site');
            // there are no menu Itemid found, lets dive into menu finder
            $menuItem = $menu->getItems('link', 'index.php?option=com_quix&view=page&id='.$this->item->id, true);
            if (isset($menuItem->id)) {
                $this->Itemid = $menuItem->id;
            }
        }

        $this->return_page = base64_encode(
            Juri::root().'index.php?option=com_quix&view='.$this->type.'&id='.(int) $this->item->id.($this->Itemid ? '&Itemid='.$this->Itemid : '')
        );

        // TODO: make it html default,
        // for quix work, changing to component
        $output          = $app->input->get('output', 'html');
        $tmpl            = $output !== 'html' ? '&tmpl=component' : '';
        $id              = (int) $this->item->id;
        $this->iframeUrl = JUri::root().'index.php?option=com_quix&view=form&layout=iframe&builder=frontend&type='
                           .$this->type.'&id='.$id.$tmpl;

        $this->_prepareDocument();
        $this->addPageList();
        parent::display($tpl);
    }

    /**
     * Prepares the pagelist
     * added to header as script
     *
     * @return  void
     * @throws Exception
     * @since 3.0.0
     */
    protected function addPageList()
    {
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

        // Prepare page data
        $table  = '#__quix';
        $fields = ['id', 'title', 'state'];
        $query->select($fields)
              ->from($table)
              ->order('id desc')
              ->where('state in (0, 1, 2)')
              ->where('builder = "frontend"')
              ->setLimit(999);

        if ($this->item->id) {
            $query->where('id != '.$this->item->id);
        }

        $db->setQuery($query);
        $list = $db->loadObjectList();
        $data = json_encode($list);
        $this->doc->addScriptDeclaration('var QuixPageList = '.$data.';');
        if ($this->type == 'page') {
            $menu = JMenu::getInstance('site');
            // there are no menu Itemid found, lets dive into menu finder
            $menuItem = $menu->getItems('link', 'index.php?option=com_quix&view=page&id='.$this->item->id, true);

            if (isset($menuItem->id)) {
                $hasMenu = true;
            } else {
                $hasMenu = false;
            }

            $this->doc->addScriptDeclaration('var QuixPageHasMenu = "'.$hasMenu.'";');
        } else {
            $this->doc->addScriptDeclaration('var QuixPageHasMenu = "false";');
        }

        $table  = '#__quix_collections';
        $fields = ['id', 'title', 'type', 'state'];
        $query  = $db->getQuery(true);
        $query->select($fields)
              ->from($table)
              ->order('id desc')
              ->where('state in (0, 1, 2)')
              ->where('builder = "frontend"')
              ->setLimit(999);

        if ($this->item->id) {
            $query->where('id != '.$this->item->id);
        }

        $db->setQuery($query);
        $list = $db->loadObjectList();
        $data = json_encode($list);
        $this->doc->addScriptDeclaration('var QuixCollectionList = '.$data.';');

        // set builder type
        $this->doc->addScriptDeclaration('var QuixBuilderType = "'.$this->type.'";');
        // TODO make it optional, as we dont have RTL support yet for builder

        // set license status
        $this->doc->addScriptDeclaration('var QuixLicenseStatus = "'.QuixHelperLicense::licenseStatus().'";');

        $this->doc->setDirection('ltr');
    }

    /**
     * Prepares the document
     *
     * @return  void
     * @throws Exception
     * @since 3.0.0
     */
    protected function _prepareDocument()
    {
        $app   = JFactory::getApplication();
        $menus = $app->getMenu();

        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu = $menus->getActive();

        if ($menu) {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        } else {
            $this->params->def('page_heading', JText::_('com_quix_form_edit_page'));
        }

        $title = 'Quix Builder';
        $this->doc->setTitle($title);

        if ($this->params->get('robots')) {
            $this->doc->setMetadata('robots', $this->params->get('robots'));
        }
    }
}
