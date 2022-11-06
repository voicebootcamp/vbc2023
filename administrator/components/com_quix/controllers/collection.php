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
 * Collection controller class.
 *
 * @since  1.6
 */
class QuixControllerCollection extends JControllerForm
{
    /**
     * Constructor
     *
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();

        $input = JFactory::getApplication()->input;
        $type  = $input->get('type', 'section');

        $this->view_list = 'collections';
        $this->view_item = 'collection&type='.$type;

    }

    /**
     * Method to save a record.
     *
     * @param  string  $key     The name of the primary key of the URL variable.
     * @param  string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
     *
     * @return  boolean  True if successful, false otherwise.
     *
     * @since   12.2
     */

    public function save($key = null, $urlVar = null)
    {
        // Check for request forgeries.
        (JSession::checkToken('get') or JSession::checkToken()) or jexit(JText::_('JINVALID_TOKEN'));
        $app     = JFactory::getApplication();
        $model   = $this->getModel();
        $table   = $model->getTable();
        $checkin = property_exists($table, 'checked_out');
        $context = "$this->option.edit.$this->context";
        $data    = $this->input->post->get('jform', array(), 'array');

        // Determine the name of the primary key for the data.
        if (empty($key)) {
            $key = $table->getKeyName();
        }

        // To avoid data collisions the urlVar may be different from the primary key.
        if (empty($urlVar)) {
            $urlVar = $key;
        }

        $recordId = $this->input->getInt($urlVar);

        // Validate the posted data.
        // Sometimes the form needs some posted data, such as for plugins and modules.
        $form = $model->getForm($data, false);

        // adjust type
        if (isset($data['QuixItemtypeLib'])) {
            $data['type'] = $data['QuixItemtypeLib'];
        } elseif ( ! isset($data['type'])) {
            $data['type'] = 'section';
        } else {
            $data['type'] = $data['type'];
        }

        // Test whether the data is valid.
        $validData = $model->validate($form, $data);
        $task      = $this->getTask();

        // Check for validation errors.
        if ($validData === false || ! $model->save($validData)) {

            if ($task === 'save') {
                // Clear the record id and data from the session.
                $this->releaseEditId($context, $recordId);
                $app->setUserState($context.'.data', null);

                // Save succeeded, so check-in the record.
                if ($checkin && $model->checkin($validData[$key]) === false) {
                    // Save the data in the session.
                    $app->setUserState($context.'.data', $validData);

                    // Check-in failed, so go back to the record and display a notice.
                    $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()));
                    $this->setMessage($this->getError(), 'error');

                    $this->setRedirect(
                        JRoute::_(
                            'index.php?option='.$this->option.'&view='.$this->view_item
                            .$this->getRedirectToItemAppend($recordId, $urlVar), false
                        )
                    );

                    return false;
                }


                $this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()));

                return false;
            } else {

                // Get the validation messages.
                $errors = $model->getErrors();
                $msg    = '';
                // Push up to three validation messages out to the user.
                for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
                    if ($errors[$i] instanceof Exception) {
                        $msg .= $errors[$i]->getMessage();
                    } else {
                        $msg .= $errors[$i];
                    }
                }

                $err = new Exception($msg);
                echo new JResponseJson($err);
                JFactory::getApplication()->close();
            }
        } else {
            $id  = $model->getState('collection.id');
            $uid = $model->getState('collection.uid');

            if ($task == 'save') {
                // Clear the record id and data from the session.
                $this->releaseEditId($context, $recordId);
                $app->setUserState($context.'.data', null);

                // Save succeeded, so check-in the record.
                if ($checkin && $model->checkin($validData[$key]) === false) {
                    // Save the data in the session.
                    $app->setUserState($context.'.data', $validData);

                    // Check-in failed, so go back to the record and display a notice.
                    $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()));
                    $this->setMessage($this->getError(), 'error');

                    $this->setRedirect(
                        JRoute::_(
                            'index.php?option='.$this->option.'&view='.$this->view_item
                            .$this->getRedirectToItemAppend($recordId, $urlVar), false
                        )
                    );

                    return false;
                }

                // Redirect to the list screen.
                $this->setMessage(JText::_('COM_QUIX_SAVE_SUCCESS'));
                $this->setRedirect(JRoute::_('index.php?option=com_quix&view=collections', false));

                return true;
            } else {
                // checkin the id of collection
                $this->holdEditId($context, $id);
                $url = JRoute::_(JUri::root().'index.php?option=com_quix&task=collection.edit&quixlogin=true&id='.$id);
                $val = compact('id', 'uid', 'url');
                echo new JResponseJson($val);
                JFactory::getApplication()->close();
            }
        }
    }
}
