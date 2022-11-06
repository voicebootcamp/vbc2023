<?php

/**
 * @copyright     Copyright (c) 2009-2022 Ryan Demmer. All rights reserved
 * @license       GNU/GPL 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * JCE is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses
 */
class WFTemplateManagerPluginConfig
{
    public static function getConfig(&$settings)
    {
        $wf = WFApplication::getInstance();

        $config = array();

        $config['selected_content_classes'] = $wf->getParam('templatemanager.selected_content_classes', '');
        $config['cdate_classes'] = $wf->getParam('templatemanager.cdate_classes', 'cdate creationdate', 'cdate creationdate');
        $config['mdate_classes'] = $wf->getParam('templatemanager.mdate_classes', 'mdate modifieddate', 'mdate modifieddate');
        $config['cdate_format'] = $wf->getParam('templatemanager.cdate_format', '%m/%d/%Y : %H:%M:%S', '%m/%d/%Y : %H:%M:%S');
        $config['mdate_format'] = $wf->getParam('templatemanager.mdate_format', '%m/%d/%Y : %H:%M:%S', '%m/%d/%Y : %H:%M:%S');

        $config['content_url'] = $wf->getParam('templatemanager.content_url', '');

        require_once __DIR__ . '/templatemanager.php';

        $plugin = new WFTemplateManagerPlugin();

        $config['replace_values'] = $plugin->replaceValuesToArray();

        $config['list'] = $wf->getParam('templatemanager.template_list', 1);

        if ($plugin->getParam('inline_upload', 1)) {
            $config['upload'] = array(
                'max_size' => $plugin->getParam('max_size', 1024),
                'filetypes' => $plugin->getFileTypes(),
                'inline' => true
            );
        }

        if ($plugin->getParam('text_editor', 0)) {
            $config['text_editor'] = 1;
        }

        $settings['templatemanager'] = $config;
    }
}
