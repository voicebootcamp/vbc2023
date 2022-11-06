<?php

/**
 * @version    CVS: 1.0.0
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;

/**
 * Quix page class.
 *
 * @since  1.6.0
 */
class QuixControllerCollection extends JControllerForm
{
    /**
     * The URL view item variable.
     *
     * @var    string
     * @since  1.6
     */
    protected $view_item = 'form';

    /**
     * The URL view list variable.
     *
     * @var    string
     * @since  1.6
     */
    protected $view_list = 'collections';

    /**
     * The URL edit variable.
     *
     * @var    string
     * @since  3.2
     */
    protected $urlVar = 'a.id';

    /**
     * Method to add a new record.
     *
     * @return  mixed  True if the record can be added, an error object if not.
     *
     * @since   1.6
     */
    public function add()
    {
        if ( ! parent::add()) {
            // Redirect to the return page.
            $this->setRedirect($this->getReturnPage());

            return;
        }

        // Redirect to the edit screen.
        $this->setRedirect(
            JRoute::_(
                'index.php?option='.$this->option.'&view='.$this->view_item.'&type=collection&id=0'
                .$this->getRedirectToItemAppend(),
                false
            )
        );

        return true;
    }

    /**
     * Method override to check if you can add a new record.
     *
     * @param  array  $data  An array of input data.
     *
     * @return  boolean
     *
     * @since   1.6
     */
    protected function allowAdd($data = [])
    {
        $user       = JFactory::getUser();
        $categoryId = ArrayHelper::getValue($data, 'catid', $this->input->getInt('catid'), 'int');
        $allow      = null;

        if ($categoryId) {
            // If the category has been passed in the data or URL check it.
            $allow = $user->authorise('core.create', 'com_quix.category.'.$categoryId);
        }

        if ($allow === null) {
            // In the absense of better information, revert to the component permissions.
            return parent::allowAdd();
        } else {
            return $allow;
        }
    }

    /**
     * Method override to check if you can edit an existing record.
     *
     * @param  array  $data  An array of input data.
     * @param  string  $key  The name of the key for the primary key; default is id.
     *
     * @return  boolean
     *
     * @since   1.6
     */
    protected function allowEdit($data = [], $key = 'id')
    {
        $recordId = (int) isset($data[$key]) ? $data[$key] : 0;
        $user     = JFactory::getUser();

        // Zero record (id:0), return component edit permission by calling parent controller method
        if ( ! $recordId) {
            return parent::allowEdit($data, $key);
        }

        // Check edit on the record asset (explicit or inherited)
        if ($user->authorise('core.edit', 'com_quix.collection.'.$recordId)) {
            return true;
        }

        // Check edit own on the record asset (explicit or inherited)
        if ($user->authorise('core.edit.own', 'com_quix.collection.'.$recordId)) {
            // Existing record already has an owner, get it
            $record = $this->getModel()->getItem($recordId);

            if (empty($record)) {
                return false;
            }

            // Grant if current user is owner of the record
            return $user->get('id') == $record->created_by;
        }

        return false;
    }

    /**
     * Method to cancel an edit.
     *
     * @param  string  $key  The name of the primary key of the URL variable.
     *
     * @return  boolean  True if access level checks pass, false otherwise.
     *
     * @since   1.6
     */
    public function cancel($key = 'id')
    {
        parent::cancel($key);

        $app = JFactory::getApplication();
        $pk  = $app->input->getInt($key);

        if ( ! $pk) {
            return $this->setRedirect(JUri::root());
        } else {
            $link = JRoute::_('index.php?option=com_quix&view=collection&id='.$pk);

            return $this->setRedirect($link);
        }

        // Load the parameters.
        $params = $app->getParams();

        $customCancelRedir = (bool) $params->get('custom_cancel_redirect');

        if ($customCancelRedir) {
            $cancelMenuitemId = (int) $params->get('cancel_redirect_menuitem');

            if ($cancelMenuitemId > 0) {
                $item = $app->getMenu()->getItem($cancelMenuitemId);
                $lang = '';

                if (JLanguageMultilang::isEnabled()) {
                    $lang = ! is_null($item) && $item->language != '*' ? '&lang='.$item->language : '';
                }

                // Redirect to the user specified return page.
                $redirlink = $item->link.$lang.'&Itemid='.$cancelMenuitemId;
            } else {
                // Redirect to the same page submission form (clean form).
                $redirlink = $app->getMenu()->getActive()->link.'&Itemid='.$app->getMenu()->getActive()->id;
            }
        } else {
            $menuitemId = (int) $params->get('redirect_menuitem');
            $lang       = '';

            if ($menuitemId > 0) {
                $lang = '';
                $item = $app->getMenu()->getItem($menuitemId);

                if (JLanguageMultilang::isEnabled()) {
                    $lang = ! is_null($item) && $item->language != '*' ? '&lang='.$item->language : '';
                }

                // Redirect to the general (redirect_menuitem) user specified return page.
                $redirlink = $item->link.$lang.'&Itemid='.$menuitemId;
            } else {
                // Redirect to the return page.
                $redirlink = $this->getReturnPage();
            }
        }

        // redirect to admin list page
        $redirlink = JUri::root().'administrator/index.php?option=com_quix&view=collections';

        $this->setRedirect($redirlink);
    }

    /**
     * Method to edit an existing record.
     *
     * @param  string  $key      The name of the primary key of the URL variable.
     * @param  string  $urlVar   The name of the URL variable if different from the primary key
     *                           (sometimes required to avoid router collisions).
     *
     * @return  boolean  True if access level check and checkout passes, false otherwise.
     *
     * @since   1.6
     */
    public function edit($key = null, $urlVar = 'id')
    {
        $result = parent::edit($key, $urlVar);

        if ( ! $result) {
            $this->setRedirect(JRoute::_($this->getReturnPage(), false));
        }

        return $result;
    }

    /**
     * Method to get a model object, loading it if required.
     *
     * @param  string  $name    The model name. Optional.
     * @param  string  $prefix  The class prefix. Optional.
     * @param  array  $config   Configuration array for model. Optional.
     *
     * @return  object  The model.
     *
     * @since   1.5
     */
    public function getModel($name = 'form', $prefix = '', $config = ['ignore_request' => true])
    {
        return parent::getModel($name, $prefix, $config);
    }

    /**
     * Gets the URL arguments to append to an item redirect.
     *
     * @param  integer  $recordId  The primary key id for the item.
     * @param  string  $urlVar     The name of the URL variable for the id.
     *
     * @return  string  The arguments to append to the redirect URL.
     *
     * @since   1.6
     */
    protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
    {
        // Need to override the parent method completely.
        $tmpl = $this->input->get('tmpl', 'component');

        $append = '';

        // Setup redirect info.
        if ($tmpl) {
            $append .= '&tmpl='.$tmpl;
        }

        // TODO This is a bandaid, not a long term solution.
        /**
         * if ($layout)
         * {
         *  $append .= '&layout=' . $layout;
         * }
         */

        $append .= '&layout=edit';
        $append .= '&type=collection';

        if ($recordId) {
            $append .= '&'.$urlVar.'='.$recordId;
        }

        $itemId = $this->input->getInt('Itemid');
        $return = ''; //$this->getReturnPage();
        $catId  = $this->input->getInt('catid');

        if ($itemId) {
            $append .= '&Itemid='.$itemId;
        }

        if ($catId) {
            $append .= '&catid='.$catId;
        }

        if ($return) {
            $append .= '&return='.base64_encode($return);
        }

        return $append;
    }

    /**
     * Gets the URL arguments to append to a list redirect.
     *
     * @return  string  The arguments to append to the redirect URL.
     *
     * @since   1.6
     */
    protected function getRedirectToListAppend()
    {
        $user = JFactory::getUser();
        $app  = JFactory::getApplication();

        if ($user->get('guest')) {
            $return                = base64_encode(JUri::getInstance());
            $login_url_with_return = JRoute::_('index.php?option=com_users&view=login&return='.$return);
            $app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'notice');
            $app->redirect($login_url_with_return, 403);
        }

        return;
    }

    /**
     * Get the return URL.
     *
     * If a "return" variable has been passed in the request
     *
     * @return  string  The return URL.
     *
     * @since   1.6
     */
    protected function getReturnPage()
    {
        $return = $this->input->get('return', null, 'base64');

        if (empty($return)) {
            return JUri::base();
        } else {
            return base64_decode($return);
        }
    }

    public function updateName()
    {
        $app = JFactory::getApplication();
        // Send json mime type.
        $app->mimeType = 'application/json';
        $app->setHeader('Content-Type', $app->mimeType.'; charset='.$app->charSet);
        $app->sendHeaders();

        // Check if user token is valid.
        if ( ! JSession::checkToken('get')) {
            $exception = new Exception(JText::_('JINVALID_TOKEN'));
            echo new JResponseJSON($exception);
            $app->close();
        }

        $app->input->set('type', 'collection');
        $id     = $app->input->get('id', '', 'int');
        $output = 'No id found!';

        if ($id) {
            $name = $app->input->get('name', '', 'string');

            $model = $this->getModel('form');
            $table = $model->gettable();
            $table->load($id);
            $table->title = ($name ? $name : $table->title);
            $result       = $table->store();

            if ($result) {
                $output = true;
            } else {
                $output = false;
            }
        }

        // response json
        echo new JResponseJson($output);

        // close the output
        $app->close();
    }

    public function updateState()
    {
        $app = JFactory::getApplication();
        // Send json mime type.
        $app->mimeType = 'application/json';
        $app->setHeader('Content-Type', $app->mimeType.'; charset='.$app->charSet);
        $app->sendHeaders();

        // Check if user token is valid.
        if ( ! JSession::checkToken('get')) {
            $exception = new Exception(JText::_('JINVALID_TOKEN'));
            echo new JResponseJSON($exception);
            $app->close();
        }

        $app->input->set('type', 'collection');
        $id     = $app->input->get('id', '', 'int');
        $output = 'No id found!';

        if ($id) {
            $state = $app->input->get('state', '', 'int');

            $model = $this->getModel('form');
            $table = $model->gettable();
            $table->load($id);
            if ($table->id) {
                $table->state = ($state ? $state : $table->state);
                $result       = $table->store();
                if ($result) {
                    $output = true;
                } else {
                    $output = false;
                }
            } else {
                $output = 'No id found!';
            }
        }

        // response json
        echo new JResponseJson($output);

        // close the output
        $app->close();
    }

    /**
     * Method to save a record.
     *
     * @param  null  $key       The name of the primary key of the URL variable.
     * @param  string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
     *
     * @return void True if successful, false otherwise.
     *
     * @throws \Exception
     * @since   1.6
     */
    public function save($key = null, $urlVar = 'id')
    {
        $result  = parent::save($key, $urlVar);
        $context = 'com_quix.edit.collection';
        $values  = (array) \JFactory::getApplication()->getUserState($context.'.id');

        $id = reset($values);
        if ($result) {
            $menu = JMenu::getInstance('site');
            // there are no menu Itemid found, lets dive into menu finder
            $menuItem = $menu->getItems('link', 'index.php?option=com_quix&view=collection&id='.$id, true);
            if (isset($menuItem->id)) {
                $hasMenu = true;
            } else {
                $hasMenu = false;
            }

            // saved successfully
            $url  = JRoute::_('index.php?option=com_quix&view=form&type=collection&layout=edit&tmpl=component&id='.$id);
            $view = base64_encode('index.php?option=com_quix&view=collection&preview=true&id='.(int) $id);
            $edit = JRoute::_('index.php?option=com_quix&view=form&layout=iframe&builder=frontend&type=collection&id='.$id);
            echo new JResponseJson(compact('id', 'view', 'url', 'edit', 'hasMenu'));
            JFactory::getApplication()->close();
        } else {
            $err = $this->getError();
            echo new JResponseJson($err);
            JFactory::getApplication()->close();
        }
    }

    /**
     * Function that allows child controller access to model data after the data has been saved.
     *
     * @param  JModelLegacy  $model  The data model object.
     * @param  array  $validData     The validated data.
     *
     * @return  void
     *
     * @since  1.0.0
     */
    protected function postSaveHook(JModelLegacy $model, $validData = [])
    {
        $app = JFactory::getApplication();
        $app->setUserState('com_quix.edit.collection.id', $model->getState('form.id'));

        $task = $this->getTask();
        if ($task == 'apply' or $task == 'save') {
            $id               = $model->getState('form.id');
            $model->typeAlias = 'page';
            $item             = $model->getItem($id);
            $type             = property_exists($item, 'type') ? $item->type : 'page';

            // clean up style and script cache...
            $this->cleanup($type, $item);

            if ($model->getState('form.new')) {
                $this->setRedirect(JRoute::_('index.php?option=com_quix&task=collection.edit&id='.$id, false));
            } else {
                $this->setRedirect(JRoute::_($this->getReturnPage(), false));
            }
        }

        return true;
    }

    /**
     * Clean up compiled css and js file.
     */
    protected function cleanup($type, $item)
    {
        array_map('unlink', glob(QuixAppHelper::get_compiled_assets_path()."**/{$type}-{$item->id}-{$item->builder}-*.*"));
    }

    /**
     * Method to reload a record.
     *
     * @param  string  $key     The name of the primary key of the URL variable.
     * @param  string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
     *
     * @return  void
     *
     * @since   3.8.0
     */
    public function reload($key = null, $urlVar = 'id')
    {
        return parent::reload($key, $urlVar);
    }
}
