<?php
/**
 * @version    CVS: 1.0.0
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;

/**
 * Collections list controller class.
 *
 * @since  1.6
 */
class QuixControllerThemes extends JControllerAdmin
{
    /**
     * Proxy for getModel.
     *
     * @param   string  $name    Optional. Model name
     * @param   string  $prefix  Optional. Class prefix
     * @param   array   $config  Optional. Configuration array for model
     *
     * @return  object	The Model
     *
     * @since    1.6
     */
    public function getModel($name = 'collection', $prefix = 'QuixModel', $config = [])
    {
        $model = parent::getModel($name, $prefix, ['ignore_request' => true]);

        return $model;
    }

    /**
     * Method to save the submitted ordering values for records via AJAX.
     *
     * @return  void
     *
     * @since   3.0
     */
    public function saveOrderAjax()
    {
        // Get the input
        $input = JFactory::getApplication()->input;
        $pks = $input->post->get('cid', [], 'array');
        $order = $input->post->get('order', [], 'array');

        // Sanitize the input
        ArrayHelper::toInteger($pks);
        ArrayHelper::toInteger($order);

        // Get the model
        $model = $this->getModel();

        // Save the ordering
        $return = $model->saveorder($pks, $order);

        if ($return) {
            echo '1';
        }

        // Close the application
        JFactory::getApplication()->close();
    }

    /**
     * Method to clone an existing products.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function duplicate()
    {
        $model = $this->getModel();

        // Check for request forgeries
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $pks = $this->input->post->get('cid', [], 'array');
        ArrayHelper::toInteger($pks);

        if (empty($pks)) {
            throw new Exception(JText::_('COM_QUIX_ERROR_NO_COLLECTION_SELECTED'));
        }

        // dulicate the selected the items.
        if (!$model->duplicate($pks)) {
            $this->setMessage($model->getError());
        } else {
            $this->setMessage(JText::plural('COM_QUIX_N_COLLECTIONS_DUPLICATED', count($pks)));
        }

        $this->setRedirect('index.php?option=com_quix&view=themes');
    }
}
