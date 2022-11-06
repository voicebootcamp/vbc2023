<?php
/**
 * @version    3.0.0
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use QuixNxt\Utils\Schema;

/**
 * Class QuixFrontendHelperAssets
 *
 * @since  3.0.0
 */
class QuixFrontendHelperAssets
{

    public static function loadLiveBuilderAssets($loadTemplateHelper = true)
    {
        $document = JFactory::getDocument();
        $input    = \JFactory::getApplication()->input;

        // Asset Helper
        if ($loadTemplateHelper) {

            // we are loading builder
            JFactory::getApplication()->input->set('jchbackend', 1);

            if (QUIXNXT_DEBUG) {
                $document->addScriptDeclaration('window.__REACT_DEVTOOLS_GLOBAL_HOOK__ = window.parent.window.__REACT_DEVTOOLS_GLOBAL_HOOK__');
            }

            $id     = $input->get('id');
            $type   = $input->get('type', 'page');
            $model  = $input->get('view');
            $_token = \JSession::getFormToken();

            $config    = \JComponentHelper::getParams('com_media');
            $imagePath = $config->get('image_path', 'images');

            $script = "var qx_site_url = '".QUIXNXT_URL."';window.QUIXNXT_URL = '".QUIXNXT_URL."';window.QUIXNXT_VERSION = '".QUIXNXT_VERSION."';window.section = [];window.row = [];window.column = [];";


            $api    = JUri::root() . 'index.php?option=com_quix&task='.$type.'.apply';
            $script .= "var quix = {id: '".$id."',type: '".$type."',model: '".$model."',_token: '".$_token."',api: '".$api."',image_path: '".$imagePath."',version: '".QUIXNXT_VERSION."',url: '".QUIXNXT_SITE_URL."', blocks:'[]', collections:'[]', presets:'[]'};";

            $document->addScriptDeclaration($script);

        }

    }

    public static function prepareApiScript()
    {
        $doc = JFactory::getDocument();

        // check pro version + activation
        $free = QuixHelper::isFreeQuix();
        $pro  = QuixHelper::isProActivated();
        if ($free or empty($pro) or $pro == null or ! $pro) {
            $responseApiValidation = [
                'success' => 'false', 'message' => 'No valid pro license has been found or license period has expired!.', 'messages' => null, 'data' => null
            ];
        } else {
            $responseApiValidation = ['success' => 'true', 'message' => 'Thank you. Valid Pro license has been found.', 'messages' => null, 'data' => null];
        }

        $responseCat = JHtml::_('category.options', 'com_content');
        array_unshift($responseCat, JHtml::_('select.option', 'root', JText::_('JGLOBAL_ROOT')));

        $responseCaptcha = [];
        $joomla_captcha  = JFactory::getConfig()->get('captcha');
        if ($joomla_captcha != '0') {
            $captcha = \JPluginHelper::getPlugin('captcha');
            $params          = new \JRegistry($captcha ? $captcha[0]->params : []);
            $responseCaptcha = $params->get('public_key', '');
        }

        $doc->addScriptDeclaration('window.QUIX_API_VALIDATION = '.json_encode($responseApiValidation).';');
        $doc->addScriptDeclaration('window.QUIX_API_GETJOOMLACATEGORIES = '.json_encode($responseCat).';');
        $doc->addScriptDeclaration('window.QUIX_API_CAPTCHEPUBLICKEY = '.json_encode($responseCaptcha).';');
        $doc->addScriptDeclaration('(function($) {$(function(){quixHeartBeatApi.init("'.JUri::root().'index.php?option=com_quix&task=live&'.JSession::getFormToken().'=1'.'");});})(jQuery);');
    }

    public static function processDataForBuilder($data, ?string $builderVersion)
    {
        // Prepare assets
        $cleaner = Schema::getCleaner();
        $data    = $data ? json_decode($data, true) : [];
        if ( ! empty($data)) {
            if (array_key_exists('data', $data)) {
                $data = $data['data'];
            }
            if (Schema::_isAssoc($data)) {
                $data = [$data];
            }

            /* old versions data migration */
            if($builderVersion < '4.0.0'){
                $adapter = Schema::getAdapter(Schema::QUIX_V2, Schema::QUIX_V3);
                $data    = $adapter->transform($data);
            }

            $data = $cleaner->mergeRecursive($data);
        }

        return json_encode($data);
    }

    public static function processRawData(string $data, ?string $builderVersion)
    {
        // Prepare assets
        $cleaner = Schema::getCleaner();
        $data    = $data ? json_decode($data, true) : [];
        if ( ! empty($data)) {
            if (array_key_exists('data', $data)) {
                $rawData = $data['data'];
            }else{
                $rawData = $data;
            }

            if (Schema::_isAssoc($rawData)) {
                $rawData = [$rawData];
            }

            /* old versions data migration */
            if($builderVersion < '4.0.0'){
                $adapter = Schema::getAdapter(Schema::QUIX_V2, Schema::QUIX_V3);
                $rawData    = $adapter->transform($rawData);
            }

            $rawData = $cleaner->mergeRecursive($rawData);


            /* prepare output */
            if (array_key_exists('data', $data)) {
                $data['data'] = $rawData;
            }else{
                $data = $rawData;
            }
        }

        return json_encode($data);
    }
}
