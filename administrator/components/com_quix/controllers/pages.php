<?php
/**
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    3.0.0
 */

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\Utilities\ArrayHelper;

/**
 * Pages list controller class.
 *
 * @since  1.6
 */
class QuixControllerPages extends AdminController
{

    /**
     * Method to save the submitted ordering values for records via AJAX.
     *
     * @return  void
     *
     * @throws \Exception
     * @since   3.0
     */
    public function saveOrderAjax()
    {
        // Get the input
        $input = JFactory::getApplication()->input;
        $pks   = $input->post->get('cid', array(), 'array');
        $order = $input->post->get('order', array(), 'array');

        // Sanitize the input
        ArrayHelper::toInteger($pks);
        ArrayHelper::toInteger($order);

        // Get the model
        $model = $this->getModel();

        // Save the ordering
        $return = $model->saveorder($pks, $order);

        if ($return) {
            echo Text::_('Updated');
        }

        // Close the application
        JFactory::getApplication()->close();
    }

    /**
     * Method to clone an existing products.
     *
     * @return  void
     *
     * @throws \Exception
     * @since   1.0.0
     */
    public function duplicate()
    {
        $model = $this->getModel();

        // Check for request forgeries
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $pks = $this->input->post->get('cid', array(), 'array');
        ArrayHelper::toInteger($pks);

        if (empty($pks)) {
            throw new \RuntimeException(JText::_('COM_QUIX_ERROR_NO_PAGE_SELECTED'));
        }

        // duplicate the selected the items.
        if ( ! $model->duplicate($pks)) {
            $this->setMessage($model->getError());
        } else {
            $this->setMessage(JText::plural('COM_QUIX_N_PAGES_DUPLICATED', count($pks)));
        }

        $this->setRedirect('index.php?option=com_quix&view=pages')->redirect();
    }

    /**
     * reset page hits
     *
     * @return  void
     *
     * @throws \Exception
     * @since   1.0.0
     */
    public function resetHits()
    {
        $model = $this->getModel();

        // Check for request forgeries
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $pks = $this->input->post->get('cid', array(), 'array');
        ArrayHelper::toInteger($pks);

        if (empty($pks)) {
            throw new \RuntimeException(JText::_('COM_QUIX_ERROR_NO_PAGE_SELECTED'));
        }

        // duplicate the selected the items.
        if ( ! $model->resetHits($pks)) {
            $this->setMessage($model->getError());
        } else {
            $this->setMessage(JText::plural('COM_QUIX_N_PAGES_HIT_RESETS', count($pks)));
        }

        $this->setRedirect('index.php?option=com_quix&view=pages')->redirect();
    }

    /**
     * Clear Page cache
     *
     * @return  void
     *
     * @throws \Exception
     * @since   3.0.0
     */
    public function clearCache(): void
    {
        // Check for request forgeries
        JSession::checkToken() || jexit(JText::_('JINVALID_TOKEN'));

        $pks = $this->input->post->get('cid', array(), 'array');
        ArrayHelper::toInteger($pks);

        if (empty($pks)) {
            throw new \RuntimeException(JText::_('COM_QUIX_ERROR_NO_PAGE_SELECTED'));
        }

        $id = array_shift($pks);
        try {
            if (JFolder::exists(JPATH_ROOT.'/media/quixnxt/storage/views/page/'.$id)) {
                JFolder::delete(JPATH_ROOT.'/media/quixnxt/storage/views/page/'.$id);
            }

            $this->setMessage(JText::_('COM_QUIXNXT_CACHE_CLEARED'));
        } catch (Exception $e) {
            $this->setMessage(JText::sprintf('COM_QUIXNXT_CACHE_CLEAR_ERROR', $e->getMessage()), 'error');
        }

        $this->setRedirect('index.php?option=com_quix&view=pages')->redirect();
    }

    /**
     * Proxy for getModel
     *
     * @param  string  $name    The model name. Optional.
     * @param  string  $prefix  The class prefix. Optional.
     * @param  array  $config   The array of possible config values. Optional.
     *
     * @return  object  The model.
     *
     * @since   1.6
     */
    public function getModel($name = 'Page', $prefix = 'QuixModel', $config = array('ignore_request' => true)): object
    {
        return parent::getModel($name, $prefix, $config);
    }
}
