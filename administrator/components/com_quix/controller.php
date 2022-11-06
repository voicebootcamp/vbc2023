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

use Joomla\CMS\Language\Text as JText;
use Joomla\Filesystem\Folder as JFolder;

/**
 * Class QuixController
 *
 * @since  1.0.0
 */
class QuixController extends JControllerLegacy
{
    /**
     * The default view.
     *
     * @var    string
     * @since  1.6
     */
    protected $default_view = 'pages';

    protected $QuixListPages = [
        'dashboard',
        'pages',
        'collections',
        'themes',
        'integrations',
        'elements',
        'filemanager',
        'rank',
        'optimize',
        'amp',
        'help',
        'config'
    ];

    /**
     * Method to display a view.
     *
     * @param  boolean  $cachable    If true, the view output will be cached
     * @param  mixed  $urlparams     An array of safe url parameters and their variable types,
     *                               for valid values see {@link JFilterInput::clean()}
     *
     * @throws Exception
     * @since    1.5
     */
    public function display($cachable = false, $urlparams = false)
    {
        QuixHelper::checkSystemPlugin();
        $view     = $this->input->get('view', 'pages');
        $layout   = $this->input->get('layout', 'default');
        $id       = $this->input->getInt('id');
        $document = JFactory::getDocument();
        JFactory::getApplication()->input->set('view', $view);


        // Check for edit form.
        if ($view === 'page' && $layout === 'edit' && ! $this->checkEditId('com_quix.edit.page', $id)) {
            // Somehow the person just went to the form - we don't allow that.
            JFactory::getApplication()->enqueueMessage(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id), 'error');
            $this->setRedirect(JRoute::_('index.php?option=com_quix&view=pages', false));
        } elseif ($view === 'collection'
                  && $layout === 'edit'
                  && ! $this->checkEditId('com_quix.edit.collection', $id)
        ) {
            // Somehow the person just went to the form - we don't allow that.
            JFactory::getApplication()->enqueueMessage(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id), 'error');
            $this->setRedirect(JRoute::_('index.php?option=com_quix&view=collections', false));
        } elseif (in_array($view, $this->QuixListPages, true)) {
            $version = QuixAppHelper::getQuixMediaVersion();
            JHtml::_('jquery.framework');

            if (JVERSION < 4) {
                JHtml::_('behavior.framework');
            } else {
                $document
                    ->addStyleSheet(\JUri::root()."media/quixnxt/css/joomla4.css");
            }

            $document
                ->addStyleSheet(\JUri::root()."media/quixnxt/css/quix-core.css", ['version' => $version])
                ->addStyleSheet(\JUri::root()."media/quixnxt/css/qxicon.css", ['version' => $version])
                ->addStyleSheet(\JUri::root()."media/quixnxt/css/new-admin.css", ['version' => $version])
                ->addScript(\JUri::root()."media/quixnxt/js/qxkit.js", ['version' => $version])
                ->addScript(JUri::root(true).'/administrator/components/com_quix/assets/script.js', ['version' => $version], ['defer' => true]);
        }

        if (JVERSION >= 4) {
            // Prepare elements icon for Joomla4
            QuixHelper::elementIconsForJoomla4();
        }

        parent::display();
    }

    /**
     * @throws Exception
     * @since 3.0.0
     */
    public function modal(): void
    {
        echo \QuixNxt\View\View::getInstance()->make(QUIXNXT_PATH.'/app/modal.php', []);
        jexit();
    }

    public function collections(): void
    {
        header('Content-type: application/json');
        try {
            // Get an instance of the generic articles model
            $app   = JFactory::getApplication();
            $input = $app->input;

            // Get an instance of the generic articles model
            $model = JModelLegacy::getInstance('Collections', 'QuixModel', ['ignore_request' => true]);

            // Set the filters based on the module params
            $model->setState('list.start', 0);
            $model->setState('list.limit', 999);

            if ( ! $input->get('details', false)) {
                $model->setState('list.select', 'a.id, a.uid, a.title, a.type');
            }

            $model->setState('filter.state', 1);

            // Access filter
            $access     = ! JComponentHelper::getParams('com_quix')->get('show_noauth');
            $authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
            $model->setState('filter.access', $access);

            // Retrieve Content
            $items = $model->getItems();

            echo new JResponseJson($items);

            $app->close();
        } catch (Exception $E) {
        }
    }

    /**
     * @throws Exception
     * @since 3.0.0
     */
    public function collection(): void
    {
        header('Content-type: application/json');

        try {
            // Get an instance of the generic articles model
            $input = JFactory::getApplication()->input;
            $model = JModelLegacy::getInstance('Collection', 'QuixModel', ['ignore_request' => true]);
            $id    = $input->get('id');
            // Retrieve Content
            $item = $model->getItem($id);

            echo new JResponseJson($item);
        } catch (Exception $e) {
            echo new JResponseJson($e);
        }

        JFactory::getApplication()->close();
    }

    /**
     * updateElement
     * url: index.php?option=com_quix&task=updateElement
     *
     * @data  : : alias: joomla-article, status: 0,1,2, token: 0000=1
     * @method: post data
     * @throws Exception
     * @since 3.0.0
     */
    public function updateElement(): void
    {
        header('Content-type: application/json');

        $input = JFactory::getApplication()->input;

        if (empty($input->get('alias', ''))) {
            $err = new Exception('Empty Alias not allowed');
            echo new JResponseJson($err);
            JFactory::getApplication()->close();
        } else {
            $alias = $input->get('alias', '');
            $model = $this->getModel('Element');
            $table = $model->getTable();
            $table->load(['alias' => $alias]);
            $status = $input->get('status', 0);

            QuixHelper::cachecleaner('lib_quix', true);
            QuixHelper::cachecleaner('lib_quix', false);

            if ($table->id) {
                $table->status = $status;
                if ( ! $table->store()) {
                    $err = new Exception($this->getError());
                    echo new JResponseJson($err);
                    JFactory::getApplication()->close();
                } else {
                    echo new JResponseJson('Element updated successfully!');
                    JFactory::getApplication()->close();
                }
            } else {
                // as not exist, create and set status
                $table->alias  = $alias;
                $table->status = $status;

                if ( ! $table->store()) {
                    $err = new Exception($this->getError());
                    echo new JResponseJson($err);
                    JFactory::getApplication()->close();
                } else {
                    echo new JResponseJson('Element updated successfully!');
                    JFactory::getApplication()->close();
                }
            }
        }
    }

    /**
     * url: index.php?option=com_quix&task=addElement
     * data: : alias: joomla-article, status: 0,1,2, token: 0000=1
     * method: post data
     *
     * @throws Exception
     * @since 3.0.0
     */
    public function addElement(): void
    {
        header('Content-type: application/json');

        $input = JFactory::getApplication()->input;

        if (empty($input->get('alias', ''))) {
            $err = new Exception('Empty Alias not allowed');
            echo new JResponseJson($err);
            JFactory::getApplication()->close();
        } else {
            $alias = $input->get('alias', '');
            $model = $this->getModel('Element');
            $table = $model->getTable();
            $table->load(['alias' => $alias]);
            if ($table->id) {
                $err = new Exception('Sorry! Element exist!');
                echo new JResponseJson($err);
                JFactory::getApplication()->close();
            } else {
                $status        = $input->get('status', 0);
                $table->alias  = $alias;
                $table->status = $status;

                if ( ! $table->store()) {
                    $err = new Exception($this->getError());
                    echo new JResponseJson($err);
                    JFactory::getApplication()->close();
                } else {
                    echo new JResponseJson('Element updated successfully!');
                    JFactory::getApplication()->close();
                }
            }
        }
    }

    /**
     * url: index.php?option=com_quix&task=removeElement
     * data: : alias: joomla-article, status: 0,1,2, token: 0000=1
     * method: post data
     *
     * @throws Exception
     * @since 3.0.0
     */
    public function removeElement(): void
    {
        header('Content-type: application/json');

        $input = JFactory::getApplication()->input;

        if (empty($input->get('alias', ''))) {
            $err = new Exception('Empty Alias not allowed');
            echo new JResponseJson($err);
            JFactory::getApplication()->close();
        } else {
            $alias = $input->get('alias', '');
            $model = $this->getModel('Element');
            $table = $model->getTable();
            $table->load(['alias' => $alias]);

            QuixHelper::cachecleaner('lib_quix', true);
            QuixHelper::cachecleaner('lib_quix', false);

            if ($table->id) {
                if ( ! $table->delete()) {
                    $err = new Exception($this->getError());
                    echo new JResponseJson($err);
                    JFactory::getApplication()->close();
                } else {
                    echo new JResponseJson('Element removed successfully!');
                    JFactory::getApplication()->close();
                }
            } else {
                $err = new Exception('Sorry! Element not found.');
                echo new JResponseJson($err);
                JFactory::getApplication()->close();
            }
        }
    }

    /**
     * url: index.php?option=com_quix&task=Elements
     * @method: get
     *
     * @return void : json
     * @throws Exception
     * @since 3.0.0
     */
    public function elements(): void
    {
        header('Content-type: application/json');
        // Get an instance of the generic articles model
        $input = JFactory::getApplication()->input;

        // Get an instance of the generic articles model
        $model = JModelLegacy::getInstance('Elements', 'QuixModel', ['ignore_request' => true]);

        // Set the filters based on the module params
        $model->setState('list.start', 0);
        $model->setState('list.limit', 999);

        if ( ! $input->get('details', false)) {
            $model->setState('list.select', 'a.*');
        }

        $model->setState('filter.state', '*');

        // Access filter
        $access     = ! JComponentHelper::getParams('com_quix')->get('show_noauth');
        $authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
        $model->setState('filter.access', $access);

        // Retrieve Content
        $items = $model->getItems();

        echo new JResponseJson($items);

        JFactory::getApplication()->close();
    }

    /**
     * @throws Exception
     * @since 3.0.0
     */

    public function updateAjax(): void
    {
        // update fonts google
        QuixHelper::getUpdateGoogleFontsList();

        echo new JResponseJson('Quix');
        JFactory::getApplication()->close();
    }

    /**
     * @throws Exception
     * @since 3.0.0
     */
    public function live(): void
    {
        echo new JResponseJson('Quix');
        JFactory::getApplication()->close();
    }

    /**
     * @throws Exception
     * @since 3.0.0
     */
    public function verify(): void
    {
        $result = QuixHelper::verifyLicense();

        if ( ! is_object($result)) {
            echo new JResponseJson('SUCCESS', $result);
        } else {
            echo new JResponseJson($result);
        }

        JFactory::getApplication()->close();
    }

    /**
     * @return bool
     * @since 3.0.0
     */
    public function updateSchema(): bool
    {

        QuixHelperSchema::updateDB();

        $this->setMessage('Schema Updated successfully!', 'success');
        $this->setRedirect(JRoute::_('index.php?option=com_quix&view=pages', true));

        return true;
    }

    /**
     * @return bool
     * @since 3.0.0
     */
    public function cleanPageAssets(): bool
    {
        $cssFiles = JFolder::files(JPATH_ROOT.'/media/quix/frontend/css');
        array_map(
            static function ($file) {
                if ($file === 'index.html') {
                    return;
                }
                JFile::delete(JPATH_ROOT.'/media/quix/frontend/css/'.$file);
            },
            $cssFiles
        );

        $jsFiles = JFolder::files(JPATH_ROOT.'/media/quix/frontend/js');
        array_map(
            static function ($file) {
                if ($file === 'index.html') {
                    return;
                }
                JFile::delete(JPATH_ROOT.'/media/quix/frontend/js/'.$file);
            },
            $jsFiles
        );

        $this->setMessage('Page assets cleaned successfully!', 'success');
        $this->setRedirect('index.php?option=com_quix&view=help', true);

        return true;
    }

    /**
     * clear cache action
     * happens through old toolbar button and after installation hooks
     * /media/quix/css,
     * /media/quix/js,
     * /media/quix/frontend/builder
     *
     * QuixHelper::cachecleaner('com_quix');
     * QuixHelper::cachecleaner('mod_quix');
     * QuixHelper::cachecleaner('lib_quix');
     * QuixHelper::cachecleaner('quix-twig');
     *
     * additional cache cleaning : step 2 : purgePageCache
     * media/quix/frontend/css,
     * media/quix/frontend/js,
     * media/quix/frontend/html,
     * media/quix/frontend/json
     *
     * @return bool
     * @since 3.0.0
     */
    public function clear_cache(): bool
    {
        try {
            $input = JFactory::getApplication()->input;
            if ($input->get('step', 1, 'int') === 1) {
                // quix()->getCache()->clearCache();
                QuixHelperCache::cleanCache();
                echo new JResponseJson('', JText::_('Quix builder cache cleaned.'), false, false);
            } else {
                QuixHelperCache::purgePageCache();
                echo new JResponseJson('', JText::_('Quix builder cache cleaned.'), false, false);
            }
        } catch (Exception $e) {
            echo new JResponseJson($e);
        }

        jexit(0);
    }

}
