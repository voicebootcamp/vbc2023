<?php
/**
 * @version    CVS: 1.0.0
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text as JText;

/**
 * pages Component Message Model
 *
 * @since  1.6
 */
class QuixControllerConfig extends JControllerLegacy
{

    /**
     * Method to save a record.
     *
     * @return  boolean
     *
     * @throws Exception
     * @since   1.6
     */
    public function save()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $app   = JFactory::getApplication();
        $model = $this->getModel('Config', 'QuixModel');
        $data  = $this->input->post->get('jform', [], 'array');

        // Validate the posted data.
        $form = $model->getForm();

        if ( ! $form) {
            JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_QUIX_FORM_COULD_NOT_LOAD', 'Config'), 'error');

            return false;
        }

        $data = $model->validate($form, $data);

        // Check for validation errors.
        if ($data === false) {
            // Get the validation pages.
            $errors = $model->getErrors();


            // Push up to three validation pages out to the user.
            for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
                if ($errors[$i] instanceof Exception) {
                    $app->enqueueMessage($errors[$i]->getMessage(), 'warning');
                } else {
                    $app->enqueueMessage($errors[$i], 'warning');
                }
            }

            // Redirect back to the main list.
            $this->setRedirect(JRoute::_('index.php?option=com_quix&view=config', false));

            return false;
        }

        // Attempt to save the data.
        if ( ! $model->save($data)) {
            // Redirect back to the main list.
            $this->setMessage(JText::sprintf('JERROR_SAVE_FAILED', $model->getError()), 'warning');
            $this->setRedirect(JRoute::_('index.php?option=com_quix&view=config', false));

            return false;
        }

        // Redirect to the list screen.
        //    $this->setMessage(JText::_('COM_QUIX_CONFIG_SAVED'));
        $this->setRedirect(JRoute::_('index.php?option=com_quix&view=config', false));

        return true;
    }

    /**
     * Validate License through API
     *
     * @throws Exception
     * @since 3.0.0
     */
    public function validateLicense()
    {
        $input    = JFactory::getApplication()->input;
        $username = $input->get('username', '', 'string');
        $key      = $input->get('key', '', 'string');

        // Verify the key
        try {
            $response = QuixHelper::verifyApiKey($username, $key);
            if ($response->success === true) {
                echo new JResponseJson($response->data);
            } else {
                echo new JResponseJson(new Exception($response->data));
            }
        } catch (Exception $e) {
            echo new JResponseJson($e);
        }

        jexit(0);
    }

    /**
     * reverse quix item's version to 2.7.0
     * some page didnt migrate, so we have to migrate again.
     *
     * @throws \Exception
     * @since 4.0.3
     */
    public function reverseVersion()
    {
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));

        $app  = JFactory::getApplication();
        $type = $app->input->get('type', 'pages', 'string');
        $id   = $app->input->get('id', 0, 'int');

        $response = QuixHelper::reverseVersion($id, $type);
        if ($response) {
            $app->enqueueMessage('Operation successful.', 'success');
        } else {
            $app->enqueueMessage('Something went wrong', 'error');
        }

        $app->redirect('index.php?option=com_quix&view='.$type);
    }
}
