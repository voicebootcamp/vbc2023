<?php

/**
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    4.0.0
 */

// No direct access
use Joomla\CMS\Language\Text as JText;
use Joomla\Registry\Registry;

defined('_JEXEC') or die;

class QuixHelperLicense
{

    const PRODUCT_NAME = 'Quix Pro';
    const PRODUCT_XML = JPATH_ROOT.'/administrator/components/com_quix/quix.xml';

    const STORE_URL = 'https://www.themexpert.com/index.php?option=com_digicom&task=responses&source=authapi&catid=38';
    const RENEW_URL = 'https://www.themexpert.com/renew/';

    // License Statuses
    const STATUS_VALID = 'valid';
    const STATUS_INVALID = 'invalid';
    const STATUS_EXPIRED = 'expired';
    const STATUS_SITE_INACTIVE = 'site_inactive';
    const STATUS_DISABLED = 'disabled';

    /**
     * Is Quix Pro installed
     * validate from admin xml
     *
     * @return bool
     * @since 3.0.0
     */
    public static function isPro()
    {
        $form = simplexml_load_file(self::PRODUCT_XML);

        if (isset($form->tag)) {
            return (string) $form->tag === 'pro' || (string) $form->tag === '##QUIXNXT_VERSION_TAG##';
        }

        return false;
    }

    /**
     * get current version from XML
     *
     * @since 3.0.0
     */
    public static function getVersion()
    {
        $xml = simplexml_load_file(self::PRODUCT_XML);

        if (isset($xml->version)) {
            return $xml->version;
        }

        return QUIXNXT_VERSION;
    }

    /**
     * Remote activation
     *
     * @param $licenseKey
     *
     * @since 3.0.0
     */
    public static function activateLicense($licenseKey)
    {
        // TODO: Implement remote activation
    }

    /**
     * Remote deactivation
     *
     * @param $licenseKey
     *
     * @since 3.0.0
     */
    public static function deactivateLicense($licenseKey)
    {
        // TODO: Implement remote activation
    }

    /**
     * Get license data from local storage
     *
     * @param $force
     *
     * @since 3.0.0
     */
    public static function getLicenseData($force = false)
    {
    }

    /**
     * Set license data to local storage
     *
     * @param $data
     *
     * @since 3.0.0
     */
    public static function setLicenseData($data)
    {


    }

    /**
     * Get license key
     *
     * @since 3.0.0
     */
    public static function getLicenseKey()
    {
    }

    /**
     * Get activation error message
     *
     * @param $error
     *
     * @return mixed|string
     * @since 3.0.0
     */
    public static function getActivationErrorMessage($error)
    {
        $errors = array(
            'no_activations_left' => sprintf(JText::_('<strong>You have no more activations left.</strong> <a href="%s" target="_blank">Please upgrade to a more advanced license</a> (you\'ll only need to cover the difference).'),
                'https://go.themexpert.com/upgrade/'),
            'expired'             => sprintf(JText::_('<strong>Your License Has Expired.</strong> <a href="%s" target="_blank">Renew your license today</a> to keep getting feature updates, premium support and unlimited access to the template library.'),
                'https://go.themexpert.com/renew/'),
            'missing'             => JText::_('Your license is missing. Please check your key again.'),
            'revoked'             => JText::_('<strong>Your license key has been cancelled</strong> (most likely due to a refund request). Please consider acquiring a new license.'),
            'key_mismatch'        => JText::_('Your license is invalid for this domain. Please check your key again.'),
        );

        if (isset($errors[$error])) {
            $error_msg = $errors[$error];
        } else {
            $error_msg = JText::_('An error occurred. Please check your internet connection and try again. If the problem persists, contact our support.').' ('.$error.')';
        }

        return $error_msg;

    }

    /**
     * Verifies the username and api key received from input
     * users can have it from there dashboard.
     *
     * @param $username
     * @param $key
     *
     * @return false|mixed
     * @since   2.1.0
     * @access  public
     */
    public static function verifyApiKey($username, $key)
    {
        $url = 'https://www.themexpert.com/index.php?option=com_digicom&task=responses&source=authapi&catid=38&username='.$username.'&key='.$key;

        $httpOption = new Registry;
        $http       = JHttpFactory::getHttp($httpOption);
        $str        = $http->get($url);

        if ($str->code != 200 && $str->code != 310) {
            return false;
        }

        return json_decode($str->body);
    }

    /**
     * Verify license data received from server
     *
     * @return \Exception|string
     * @throws \Exception
     * @since 2.0.0
     */
    public static function parseLicenseResponse()
    {
        $input    = JFactory::getApplication()->input;
        $data     = $input->get('data', '', 'string');
        $response = json_decode($data);
        if ($response === false or ! $response->success) {
            return new Exception(
                JText::_(
                    'Unable to verify your license or your hosting provider has blocked outgoing connections. Details: '.$response->message
                )
            );
        }

        $validLicense = self::validateLicense($response);
        // json_encode(['hasPro' => true, 'hasFree' => false, 'hasLicense' => true, 'name' => $proProduct, 'id' => $proID]);

        if ( ! $validLicense['hasLicense']) {
            return new Exception(
                JText::_('Your license is missing. Chances are, you\'ve entered wrong credentials or your order has expired.')
            );
        }

        if ($validLicense['hasLicense'] && $validLicense['hasPro']) {
            return JText::_(
                'Your <strong>'.$validLicense['name'].'</strong> license has activated for this site. Now you are eligible for automatic update and support for this website.'
            );
        }

        if ($validLicense['hasLicense'] && $validLicense['hasFree']) {
            return new Exception(
                JText::_(
                    'Quix free license has activated for your site. Checkout <strong>Quix PRO</strong> and unlock the true magic of page building.'
                )
            );
        }

        return new Exception(JText::_('Verify license failed!'));
    }

    /**
     * Validate the license from license list object
     * find users license, could be free/pro
     *
     * @param $data object will contain category wise license data
     *
     * @return array|false[]
     * @since 3.0.0
     */
    public static function validateLicense(object $data)
    {
        $products   = $data->data;
        $quixPro    = [116, 118, 127, 202, 220]; // agency, pro, extended license id[117 is free]
        $hasPro     = false;
        $hasFree    = false;
        $proProduct = '';
        $proID      = 0;

        foreach ($products as $key => $product) {
            if (in_array($product->id, $quixPro) && ($product->has_access == 1)) {
                $hasPro     = true;
                $proProduct = $product->name;
                $proID      = $product->id;

                break;
            }

            if ($product->id == 117 && $product->has_access === true) {
                $hasFree = true;
            }
        }

        // now return result
        if ($hasPro) {
            return ['hasPro' => true, 'hasFree' => $hasFree, 'hasLicense' => true, 'name' => $proProduct, 'id' => $proID];
        } elseif ($hasFree) {
            return ['hasPro' => false, 'hasFree' => true, 'hasLicense' => true];
        } else {
            return ['hasPro' => false, 'hasFree' => false, 'hasLicense' => false];
        }
    }

    /**
     * Check if user has activated his license
     * from local storage
     *
     * @return mixed|null
     * @lang  sql
     * @since 3.0.0
     */
    public static function isProActivated()
    {
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->quoteName("params"))
              ->from($db->quoteName('#__quix_configs'))
              ->where($db->quoteName('name')." = ".$db->quote('activated'));
        $db->setQuery($query);

        return $db->loadResult();
    }

    /**
     * Check users credentials from config table
     *
     * @return mixed
     * @throws \Exception
     * @since 3.0.0
     */
    public static function hasCredentials()
    {
        $config = JModelLegacy::getInstance('Config', 'QuixModel', ['ignore_request' => false]);
        $config->generateState();

        return $config->getItem();
    }

    /**
     * Get License status
     * @since 3.0.0
     */
    public static function licenseStatus(){

        $free = self::isPro() === false;
        $pro  = self::isProActivated();

        if ($free) {
            return'free';
        } elseif ($pro) {
            return 'pro';
        } else {
            return 'inactive';
        }
    }
}
