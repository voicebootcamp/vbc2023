<?php

/**
 * @version    1.0.0
 * @package    Contact Form Quix element
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Crypt\Crypt;
use Joomla\CMS\Language\Text as JText;

/**
 * QuixSimpleContactElement helper class
 *
 * @since 2.0.0
 */
class QuixFormElement
{

    /**
     * @throws \Exception
     * @since 4.0.0
     */
    public static function getAjax()
    {
        // Check for request forgeries.
        if ( ! JSession::checkToken('post')) {
            return new Exception("<div class='alert alert-danger qx-alert qx-alert-danger'>".JText::_('JINVALID_TOKEN')."</div>");
        }
        $app          = JFactory::getApplication();
        $lang         = $app->getLanguage();
        $base_dir     = dirname(__FILE__);
        $language_tag = 'en-GB';
        $lang->load('form', $base_dir, null, true, $language_tag);

        $data = $app->input->get('jform', array(), 'array');

        // $info = json_decode(base64_decode($data['info']));
        $info = self::getInfoFromEncrypted($data['info']);

        // form options
        $form_advanced = $info['general']['form_advanced'] ?? $info['general']['form_basic'];

        $options = [];
        foreach ($form_advanced as $name => $formAdvOptions) {
            $options[$name] = $formAdvOptions;
        }

        if ($options['custom_message'] and ! empty($options['message_success'])) {
            $successMessage = $options['message_success'];
        } else {
            $successMessage = JText::_('COM_QUIX_FORM_SUBMIT_SUCCESSFUL');
        }

        if ($options['custom_message'] and ! empty($options['message_error'])) {
            $errorMessage = $options['message_error'];
        } else {
            $errorMessage = JText::_('COM_QUIX_FORM_SUBMIT_ERROR');
        }

        if ($options['custom_message'] and ! empty($options['captcha_error'])) {
            $captchaError = $options['captcha_error'];
        } else {
            $captchaError = JText::_('COM_QUIX_FORM_CAPTCHA_ERROR');
        }


        //validate captcha first
        $validate = self::validateCaptcha($data, $options);
        if ( ! $validate) {
            return new Exception("<div class='alert alert-danger qx-alert qx-alert-danger'>".$captchaError."</div>");
        }

        //prepare data of fields
        $form_fields     = $info['general']['form_fields'];
        $new_form_fields = []; // array(index => array( index => object name->name, object value->value ))
        foreach ($form_fields as $key => $form_field) {
            $new_form_fields[$key] = [];

            foreach ($form_field as $key2 => $field) {
                $new_form_fields[$key][$key2]        = new stdClass;
                $new_form_fields[$key][$key2]->name  = $key2;
                $new_form_fields[$key][$key2]->value = $field;
            }
        }

        // validate form value
        $validateForm = self::validateSubmit($data, $new_form_fields);
        if ( ! $validateForm) {
            return new Exception("<div class='alert alert-danger qx-alert qx-alert-danger'>".JText::_('COM_QUIX_FORM_NOT_VALID')."</div>");
        }

        $afterSubmit                    = $info['general']['form_action_after_submit'];
        $info['general']['form_fields'] = $new_form_fields;
        $actions                        = $afterSubmit['actions'];

        $results = array();
        JLoader::registerPrefix('QuixFormElementHelper', __DIR__.'/helpers/');

        foreach ($actions as $action) {
            $className = 'QuixFormElementHelper'.ucfirst($action);

            $name   = "form_".$action;
            $config = $info['general'][$name];

            $results[] = $className::action($data, $config, $info);

        }

        if (in_array(false, $results)) {
            // get the error
            return new Exception("<div class='alert alert-warning qx-alert qx-alert-warning'>".$errorMessage."</div>");
        } else {
            $_SESSION['quix_form_captcha'] = [
                'first_number'  => rand(1, 10),
                'second_number' => rand(1, 10)
            ];

            // return success
            return ("<div class='alert alert-success qx-alert qx-alert-success'>".$successMessage."</div>");

        }
    }

    /**
     * @throws \Exception
     * @since 4.1.0
     */
    public static function validateCaptcha($data, $options): bool
    {
        $app               = JFactory::getApplication();
        $requiredRecaptcha = $options['required-recaptcha'] ?? false;
        $reCaptchaType     = $options['recaptcha_type'] ?? 'math';

        // captcha validation....
        if ($requiredRecaptcha) {

            if ($reCaptchaType == 'recaptcha_invisible') {
                $config    = $app->getConfig()->get('captcha');
                $captcha   = JCaptcha::getInstance($config);
                $completed = $captcha->CheckAnswer($data['info']);

                if ($completed === false) {
                    return false;
                }
            } else {
                $firstNumber  = $_SESSION['quix_form_captcha']['first_number'];
                $secondNumber = $_SESSION['quix_form_captcha']['second_number'];

                if (($firstNumber + $secondNumber) != $data['recaptcha_value']) {
                    return false;
                }
            }
        }

        return true;
    }

    public static function validateSubmit($data, $formFiels): bool
    {
        $required = [];

        foreach ($formFiels as $key => $fields) {
            $name = strtolower($fields['title']->value);

            foreach ($fields as $key2 => $field) {
                if ($field->name == 'required') {
                    if ( ! empty($field->value)) {
                        $required[$name] = $field->value;
                    }
                }
            }
        }
        $valid = true;
        if ($required) {
            foreach ($required as $key => $item) {
                if ( ! isset($data[$key]) or empty($data[$key])) {
                    $valid = false;
                    break;
                }
            }
        }

        return $valid;
    }

    /**
     * @throws \Exception
     * @since 4.1.0
     */
    public static function getInfoFromEncrypted($string)
    {
        $app       = \JFactory::getApplication();
        $configSys = JFactory::getConfig();
        $session   = $app->getSession();
        if ($session->get('quix_form_secret')) {
            $key = $session->get('quix_form_secret');
        } else {
            $secret   = $configSys->get('secret');
            $encCrypt = new Crypt(null, null);
            $key      = $encCrypt->generateKey();
            $session->set('quix_form_secret', $key);
        }
        $enc = new Crypt(null, $key);

        $decrypt = $enc->decrypt($string);

        return json_decode($decrypt, true);
    }

}
