<?php
/**
 * @version    CVS: 1.0.0
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * pages Component Message Model
 *
 * @since  1.6
 */
class QuixControllerGet extends JControllerLegacy
{
    
    function disableEditor()
    {
        require_once JPATH_SITE . '/components/com_quix/helpers/editor.php';

        $app = JFactory::getApplication();
        $input = $app->input;
        $id = $input->get('quixEditorMapID', '', 'int');

        $result = QuixFrontendHelperEditor::disableEditor($id);
        
        if ($result) {
            echo new JResponseJson(true);
        } else {
            $e = new Exception(false);
            echo new JResponseJson($e);
        }
        jexit();
    }

    function enableEditor()
    {
        require_once JPATH_SITE . '/components/com_quix/helpers/editor.php';

        $app = JFactory::getApplication();
        $input = $app->input;
        $id = $input->get('quixEditorMapID', '', 'int');

        $result = QuixFrontendHelperEditor::enableEditor($id);
        
        if ($result) {
            echo new JResponseJson(true);
        } else {
            $e = new Exception(false);
            echo new JResponseJson($e);
        }
        jexit();
    }
}
