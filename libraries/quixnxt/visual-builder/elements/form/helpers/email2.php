<?php

/**
 * @version    1.0.0
 * @package    Contact Form Quix element
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
use Joomla\CMS\Mail\Exception\MailDisabledException;

defined('_JEXEC') or die;


/**
 * QuixSimpleContactElement helper class
 *
 * @since 4.0.0
 */
class QuixFormElementHelperEmail2
{
    /**
     * Basic method
     * params data = form data, config = action hooks settings, info = elements settings
     *
     * @throws \PHPMailer\PHPMailer\Exception
     * @since 4.0.0
     */
    public static function action($data, $config, $info)
    {
        $app       = JFactory::getApplication();
        $sysConfig = JFactory::getConfig();

        $configNew = array();
        foreach ($config as $keyName => $conf) {
            $configNew[$keyName]        = new stdClass;
            $configNew[$keyName]->name  = $keyName;
            $configNew[$keyName]->value = $conf;
        }

        /*
        * validation successful, do your job here
        */
        // Now send email
        $mail = JFactory::getMailer();

        // add recipient        
        $recipient = $configNew['email2_to']->value;
        if (empty($recipient)) {
            $recipient = $sysConfig->get('mailfrom');
        }
        $mail->addRecipient($recipient);

        /**
         * sender is system email
         * @depecated since joomla 4 support and quix 4.1
         * For sendmail it cause sendmail not found, so we are depending on joomla default process to set sender
         */
        // $name  = $sysConfig->get('fromname');
        // $email = JStringPunycode::emailToPunycode($sysConfig->get('mailfrom'));
        // $mail->setSender(array($email, $name));

        // set subject
        $subject = $configNew['email2_subject']->value;
        $mail->setSubject($subject);

        // set reply_to
        $reply_to = $configNew['reply2_to']->value ?? 'none';
        if ($reply_to == 'system') {
            $mail->addReplyTo($email);
        } elseif ($reply_to == 'emailfield' && isset($data['email']) && ! empty($data['email'])) { // from users input
            $mail->addReplyTo($data['email']);
        }


        // add cc and bcc
        if ( ! empty($configNew['email2_cc']->value)) {
            $emailCC  = explode(',', $configNew['email2_cc']->value);
            $ccEmails = [];
            foreach ($emailCC as $key => $eCC) {
                $ccEmails[] = JStringPunycode::emailToPunycode($eCC);
            }
            $mail->addCc($ccEmails);
        }
        if ( ! empty($configNew['email2_bcc']->value)) {
            $email_bcc = explode(',', $configNew['email2_bcc']->value);
            $bccEmails = [];
            foreach ($email_bcc as $key2 => $emailBCC) {
                $bccEmails[] = JStringPunycode::emailToPunycode($emailBCC);
            }
            $mail->addBcc($bccEmails);
        }

        // get shortcodes
        $prepareShortcode = self::getAllData($data, $config, $info);

        // set subject
        $subjectText = $configNew['email2_subject']->value;
        $subject     = strtr($subjectText, $prepareShortcode);
        $mail->setSubject($subject);

        // prepare body
        $content = $configNew['email2_content']->value;
        if (is_array($content)) {
            $content = implode("", $content);
        }


        $body = strtr($content, $prepareShortcode);

        // add meta
        $email_metas = $configNew['email2_meta']->value;
        $credit      = false;
        if (count($email_metas)) {
            $bodyTag       = '<p style="text-align: center;font-family: monospace;padding: 10px 0;margin: 0;"><small>';
            foreach ($email_metas as $key => $meta) {
                switch ($meta) {
                    case 'date':
                        $bodyTag .= Date("Y/m/d").' | ';
                        break;

                    case 'time':
                        $bodyTag .= Date("h:i:sa").' | ';
                        break;

                    case 'page_url':
                        $bodyTag .= $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].' | ';
                        break;

                    case 'user_agent':
                        $bodyTag .= $_SERVER['HTTP_USER_AGENT'].' | ';
                        break;

                    case 'remote_ip':
                        if ( ! empty($_SERVER['HTTP_CLIENT_IP'])) {
                            $ip = $_SERVER['HTTP_CLIENT_IP'];
                        } elseif ( ! empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                        } else {
                            $ip = $_SERVER['REMOTE_ADDR'];
                        }
                        $bodyTag .= $ip.' | ';
                        break;
                    case 'credit':
                        $credit = true;
                        break;

                }
            }
            $bodyTag .= '</small></p>';

            $body .= $bodyTag;
        }
        if ($credit) {
            $body .= '<p><small><center>Powered by Quix - Joomla page builder</center></small></p>';
        }

        $mail->setBody($body);

        // email_send as
        if ($configNew['email2_sendas']->value == 'html') {
            $mail->isHTML(true);
            $mail->Encoding = 'base64';
        } else {
            $mail->isHTML(false);
        }

        try {
            return $mail->Send();
        } catch (MailDisabledException | phpMailerException | Exception | Throwable $exception) {
            return false;
        }

    }

    /*
    * data is form data
    * config is after email hook, is this event hook
    * info is element config
    */
    public static function getAllData($data, $config, $info): array
    {
        $codes      = [];
        $formFiles = $info['general']['form_fields'];

        foreach ($formFiles as $key => $fields) {

            $name  = strtolower($fields['title']->value);
            $value = ($data[$name] ?? '');

            foreach ($fields as $key2 => $field) {
                if ($field->name == 'shortcode') {
                    if ( ! empty($field->value)) {
                        $codes[$field->value] = $value;
                    }
                }
            }
        }

        // now add all-fields
        $body = '<table cellpadding="5" cellspacing="1" border="0" bgcolor="#FFFFFF"><tbody>';
        foreach ($data as $key => $value) {
            if ($key == 'info') {
                continue;
            }
            $body  .= "<tr><th align='left' valign='top'>".ucfirst($key).'</th>';
            $value = is_array($value) ? implode(', ', $value) : $value;
            $body  .= '<td>'.$value."</td></tr>\n";
        }
        $body                  .= '<tbody></table>';
        $codes['[all-fields]'] = $body;

        return $codes;
    }

}
