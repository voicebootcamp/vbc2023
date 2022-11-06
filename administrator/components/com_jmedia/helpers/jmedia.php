<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jmedia
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * JMedia helper class.
 *
 * @since       1.0
 */
abstract class JMediaHelper
{
    /**
     * Checks if the file can be uploaded
     *
     * @param  array  $file    File information
     * @param  string  $error  An error message to be returned
     *
     * @return  boolean
     *
     * @since       1.5
     * @deprecated  4.0  Use JHelperMedia::canUpload instead
     */
    public static function canUpload($file, $error = '')
    {
        try {
            JLog::add(
                sprintf('%s() is deprecated. Use JHelperMedia::canUpload() instead.', __METHOD__),
                JLog::WARNING,
                'deprecated'
            );
        } catch (RuntimeException $exception) {
            // Informational log only
        }

        $mediaHelper = new JHelperMedia;

        return $mediaHelper->canUpload($file, 'com_jmedia');
    }

    /**
     * Method to parse a file size
     *
     * @param  integer  $size  The file size in bytes
     *
     * @return  string  The converted file size
     *
     * @since       1.6
     * @deprecated  4.0  Use JHtml::_('number.bytes') instead
     */
    public static function parseSize($size)
    {
        try {
            JLog::add(
                sprintf("%s() is deprecated. Use JHtml::_('number.bytes') instead.", __METHOD__),
                JLog::WARNING,
                'deprecated'
            );
        } catch (RuntimeException $exception) {
            // Informational log only
        }

        return JHtml::_('number.bytes', $size);
    }

    public static function getFooter()
    {
        JPluginHelper::importPlugin('content');
        $result = JFactory::getApplication()->triggerEvent('onJMediaPrepareFooter', array('com_jmedia.footer'));
        if ( ! $result) {
            return '<div class="jmedia-footer">
				<span><a href="https://www.themexpert.com/jmedia" style="color: #7f8fa4;" target="_blank"><i class="icon-question"></i>Need Help</a></span>
			    <span style="float:right;">Designed and Developed by <a href="https://www.themexpert.com">ThemeXpert</a></span>
			</div>';
        } else {
            return implode("/n", $result);
        }
    }

    public static function addMediaCommonScript()
    {
        JFactory::getDocument()->addScriptDeclaration("var COM_JMEDIA_BASEURL = '".JUri::base()."';");
        JFactory::getDocument()->addScriptDeclaration("var COM_JMEDIA_AUTHOR = '".JFactory::getUser()->id."';");

        JFactory::getDocument()->addScriptDeclaration("
const fileFormats = {
  'video': ['webm', 'mpg', 'mp2', 'mpeg', 'mpe', 'mpv', 'ogg', 'mp4', 'm4p', 'm4v', 'avi', 'wmv', 'mov', 'qt', 'flv', 'swfavchd', 'movie'],
  'audio': ['dsd', 'ogg', 'wma', 'wma', 'alac', 'mp3', 'aac', 'flac', 'aiff', 'wav'],
};
");
        /**
         * Event for other plugins to trigger there script
         *
         * @event onJMediaDisplayScript
         */
        JFactory::getApplication()->triggerEvent('onJMediaDisplayScript', ['com_jmedia.media']);

        /**
         * Initialize the filemanager loading script
         */
        $session = JFactory::getSession();
        $path    = $session->get('JMEDIA_LAST_PATH', '/');

        JFactory::getDocument()->addScriptDeclaration("
jQuery(document).ready(function(){
    Filemanager.mount(
        document.getElementById('JMediaWrapper'), 
        {
          url: 'index.php',
          root_url: '".COM_JMEDIA_BASEURL."',
          path: '".$path."',
          http: {
             query_params: {
               'option': 'com_jmedia',
               'task': 'api.action',
               '".JSession::getFormToken()."': 1,
             },
          },
        }, function() {
           console.log('Filemanager init');
        });
    });
");

    }

    public static function addMediaModalScriptCommon()
    {
        JFactory::getDocument()->addScriptDeclaration("function prepareFileFormat(extension) {
            if (extension === 'svg') {
                return 'svg';
            } else if (fileFormats.video.includes(extension)) {
                return 'video';
            } else if (fileFormats.audio.includes(extension)) {
                return 'audio';
            } else {
                return 'image';
            }
        }");
    }

    public static function addMediaModalScriptJ3()
    {
        self::addMediaCommonScript();
        self::addMediaModalScriptCommon();

        $input      = JFactory::getApplication()->input;
        $fieldInput = $input->get('fieldid', '');
        $isMoo      = $input->getInt('ismoo', 0);
        $e_name     = $input->get('e_name', '', 'string');
        $callback   = $input->get('callback', '', 'string');
        $source     = $input->get('source', 'joomla', 'string');

        /**
         * Mootools compatibility
         *
         * There is an extra option passed in the URL for the iframe &ismoo=0 for the bootstrap fields.
         * By default the value will be 1 or defaults to mootools behaviour
         *
         * This should be removed when mootools won't be shipped by Joomla.
         */
        if ( ! empty($fieldInput)) { // Media Form Field
            if ($isMoo) {
                $onClick = "
            function JMediaFieldValue(value){
                window.parent.jInsertFieldValue(value, '".$fieldInput."');
                if(typeof window.parent?.jModalClose === 'function'){
                    window.parent.jModalClose();
                }
                window.parent.jQuery('.modal.in').modal('hide');
            };";
            } else {
                $onClick = "
            function JMediaFieldValue(value){
                window.parent.jInsertFieldValue(value, '".$fieldInput."');
                if(typeof window.parent?.jModalClose === 'function'){
                    window.parent.jModalClose();
                }
                window.parent.jQuery('.modal.in').modal('hide');
            };";
            }
        } elseif ($callback) { // XTD Image plugin
            $onClick = '
            function JMediaFieldValue(val){
                window.parent.'.$callback.'(val);
            };';
        } else { // XTD Image plugin
            if ($e_name) {
                $selection = "'".$e_name."'";
            } else {
                $selection = 'Object.keys(Joomla.editors.instances)[0]';
            }

            $onClick = "
          function JMediaFieldValue(url){
              var tag = '',
              attr = [];      
              if (url)
              {
                  tag = '<img src=\"' + url + '\" ' + attr.join(' ') + '/>';
              }
              /** Use the API, if editor supports it **/
              if (window.Joomla && Joomla.editors.instances.hasOwnProperty(".$selection.')) {
                  console.log(Joomla.editors.instances.hasOwnProperty('.$selection.'));
                  window.parent.Joomla.editors.instances['.$selection."].replaceSelection(tag);
              } else {
                  window.parent.jInsertEditorText(tag, $selection);
              }
              window.parent.jModalClose();
          };";
        }

        JFactory::getDocument()->addScriptDeclaration($onClick);

        if ($source !== 'quix') {
            JFactory::getDocument()->addScriptDeclaration("
Filemanager.Pluggable.plugin('core').addHandler(
    'default',
    (item) => true,
    (item) => {
        console.log('JMedia - source regular');
        let tag = '".COM_JMEDIA_PREFIX."' + item.path;
            JMediaFieldValue(tag);
    }, {
        'icon': '<span class=\"icon-publish\"></span>',
        'title': 'Insert'
    },
    0
);");
        } else {
            JFactory::getDocument()->addScriptDeclaration("
Filemanager.Pluggable.plugin('core').addHandler(
    'default',
    (item) => true,
    (item) => {
        console.log('JMedia - source quix');
        let tag = '".COM_JMEDIA_PREFIX."' + item.path;
        if (item.is_file) {
            const file = {}; 
            file.type = prepareFileFormat(item.extension);
            file[file.type] = tag;
            JMediaFieldValue(file);
        } else {
            JMediaFieldValue(item);
        }
    }, {
        'icon': '<span class=\"icon-publish\"></span>',
        'title': 'Insert'
    },
    0
);");
        }

    }

    public static function addMediaModalScriptJ4()
    {
        self::addMediaCommonScript();
        self::addMediaModalScriptCommon();

        $input      = JFactory::getApplication()->input;
        $fieldInput = $input->get('fieldid', '');
        $e_name     = $input->get('e_name', '', 'string');
        $callback   = $input->get('callback', '', 'string');
        $source     = $input->get('source', 'joomla', 'string');

        if ( ! empty($fieldInput)) { // Media Form Field
            $onClick = "function JMediaFieldValue(value){
              Joomla.selectedMediaFile = value;
              window.parent.Joomla.selectedMediaFile = value;
              window.parent.Joomla.Modal.getCurrent().querySelector('.button-save-selected').click();
            };";
        } elseif ($callback) { // XTD Image plugin
            $onClick = 'function JMediaFieldValue(val){window.parent.'.$callback.'(val);}';
        } else { // XTD Image plugin
            if ($e_name) {
                $selection = "'".$e_name."'";
            } else {
                $selection = 'Object.keys(Joomla.editors.instances)[0]';
            }

            $onClick = "
          function JMediaFieldValue(fileInfo){
              window.parent.Joomla.selectedFile = fileInfo;
              window.parent.Joomla.getImage(fileInfo, $selection);
              window.parent.Joomla.Modal.getCurrent().close();
          };";
        }

        JFactory::getDocument()->addScriptDeclaration($onClick);


        $methodSingleClick = 'Filemanager.EventBus.$on("ITEMS_SELECTED", e => {
    let item = e[0];
    if(item !== undefined && item.is_dir === false){
      let file = Filemanager.Pluggable.plugin("core").accessor().items().files.find(f=>f.id === item.id);
      let response = {
        path: "local-0:" + file.path,
        fileType: file.image_info.mime,
        extension: file.extension,
        thumb: undefined,
      };
      window.parent.Joomla.selectedFile = response;
    }
});';

        JFactory::getDocument()->addScriptDeclaration($methodSingleClick);

        if ($source !== 'quix') {
            JFactory::getDocument()->addScriptDeclaration("
Filemanager.Pluggable.plugin('core').addHandler(
    'default',
    (item) => true,
    (item) => {
        if (item.is_file) {
            let response = {
                path: 'local-0:' + item.path,
                fileType: item.image_info.mime,
                extension: item.extension,
                thumb: undefined,
              };
            JMediaFieldValue(response);
        } else {
            JMediaFieldValue(item);
        }
    }, {
        'icon': '<span class=\"icon-publish\"></span>',
        'title': 'Insert'
    },
    0
);");
        } else {
            JFactory::getDocument()->addScriptDeclaration("
Filemanager.Pluggable.plugin('core').addHandler(
    'default',
    (item) => true,
    (item) => {
        let tag = '".COM_JMEDIA_PREFIX."' + item.path;
        if (item.is_file) {
            const file = {};
            file.type = prepareFileFormat(item.extension);
            file[file.type] = tag;
            JMediaFieldValue(file);
        } else {
            JMediaFieldValue(item);
        }
    }, {
        'icon': '<span class=\"icon-publish\"></span>',
        'title': 'Insert'
    },
    0
);");
        }

    }


}
