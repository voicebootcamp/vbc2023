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
 * Page controller class.
 *
 * @since  1.6
 */
class QuixControllerPage extends JControllerForm
{
    /**
     * Constructor
     *
     * @throws Exception
     * @since 3.0.0
     */
    public function __construct()
    {

        $this->view_list = 'pages';
        $this->view_item = 'page';

        parent::__construct();
    }

    /**
     * Method to save a record.
     *
     * @param  string  $key     The name of the primary key of the URL variable.
     * @param  string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
     *
     * @return  boolean  True if successful, false otherwise.
     *
     * @throws Exception
     * @since   12.2
     */

    public function save($key = null, $urlVar = null)
    {
        // Check for request forgeries.
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));

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
        // Test whether the data is valid.
        $validData = $model->validate($form, $data);

        // Check for validation errors.
        if ($validData === false) {
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

        // now validate json
        // since 2.6.0
        $validJson = json_decode($validData['data']);
        if ($validJson === null) {
            // $validJson is null because the json cannot be decoded
            $err = new Exception('<h3>Invalid Content!!!</h3><p>Your data has been truncated by the server.<br>Please change them or contact our support. Thank you</p>');
            echo new JResponseJson($err);
            JFactory::getApplication()->close();
        }

        // print_r($validData);die;
        // Attempt to save the data.
        $task  = $this->getTask();
        $model = $this->getModel();
        if ( ! $model->save($validData)) {
            $err = new Exception();

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


                $this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()));

                return false;
            } else {
                echo new JResponseJson($err);
                JFactory::getApplication()->close();
            }

        } else {
            $id = $model->getState('page.id');

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
                $this->setRedirect(JRoute::_('index.php?option=com_quix&view=pages', false));

                return true;
            } else {
                // checkin the id of collection
                $this->holdEditId($context, $id);

                echo new JResponseJson(compact('id'));
                JFactory::getApplication()->close();
            }
        }

    }

    /**
     * create page only without any data
     * Create a page with ajax request
     *
     * @since 3.0.0
     */
    public function pageCreateAjax()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // print_r($_POST);die;
        $input = JFactory::getApplication()->input;
        $title = $input->get('title', '', 'string');
        if ( ! $title) {
            echo new JResponseJson (new Exception('Please use a page name.'));
            exit(0);
        }

        $model = $this->getModel();
        if ( ! $model->save(
            [
                'title' => $title, 'state' => 1, 'builder' => 'frontend', 'builder_version' => QUIXNXT_VERSION
            ]
        )) {
            echo new JResponseJson(new Exception(JText::sprintf('Failed to save. Error: %s', $model->getError())), $model->getError(), 1, true);
        } else {
            $id   = $model->getState('page.id');
            $link = JUri::root().'index.php?option=com_quix&task=page.edit&id='.$id.'&quixlogin=true';
            echo new JResponseJson($link, JText::_('Page has been created.'), false, true);
        }

        exit(0);
    }

}
