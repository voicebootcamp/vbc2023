<?php
/**
 * @package    Quix
 * @author     ThemeXpert http://www.themexpert.com
 * @copyright  Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 * @since      1.0.0
 */

defined('_JEXEC') or die;

use Joomla\CMS\Object\CMSObject;

JHtml::_('jquery.framework');

require_once JPATH_SITE.'/administrator/components/com_quix/helpers/quix.php';

class PlgButtonQuix extends JPlugin
{
    /**
     * Load the language file on instantiation.
     *
     * @var    boolean
     * @since  3.1
     */
    protected $autoloadLanguage = true;

    public function onDisplay($name, $asset, $author)
    {
        require_once JPATH_SITE.'/components/com_quix/helpers/editor.php';

        $app   = JFactory::getApplication();
        $input = $app->input;
        $user  = JFactory::getUser();

        $canMove = false;
        if ($user->authorise('core.create', 'com_quix')
            || $user->authorise('core.edit', 'com_quix')
            || $user->authorise('core.edit.own', 'com_quix')
            || $user->authorise('core.manage', 'com_quix')
        ) {
            $canMove = true;
        }

        if ( ! $canMove) {
            return null;
        }

        // view=collections
        if ($app->isClient('administrator')) {
            $link = 'index.php?option=com_quix&amp;view=collections&amp;layout=modal&amp;tmpl=component';
            $link .= '&amp;function=jSelectQuixShortcode';
        } else {
            $link = 'index.php?option=com_quix&amp;view=collections&amp;layout=modal&amp;tmpl=component';
            $link .= '&amp;function=jSelectQuixShortcode&amp;'.JSession::getFormToken().'=1';
        }

        $doc = JFactory::getDocument();
        $doc->addScript(JUri::root(false).'administrator/components/com_quix/assets/editor.js');

        $doc->addScriptDeclaration("window.quixEditorID = '$name';");

        $a_id = $input->get('a_id', 0);
        $sid  = $a_id ?: $input->get('id', $a_id);
        $sid  = $sid ?: $input->get('cid', $sid);
        $sid  = $sid ?: $input->get('virtuemart_product_id', $sid);
        $doc->addScriptDeclaration("window.quixEditorItemID = '".$sid."';");
        $source     = $input->get('option').'.'.$input->get('view');
        $getQEditor = QuixFrontendHelperEditor::getInfo($source, $sid);
        $editorId   = $getQEditor->id ?? 0;

        if ($editorId && $getQEditor->status) {
            $doc->addScriptDeclaration('window.builtWithQuixEditor = true;');
            $doc->addScriptDeclaration("window.quixEditorMapID = '".$getQEditor->id."';");
            $url = JUri::root()
                   .'index.php?option=com_quix&task=collection.edit&id='
                   .$getQEditor->collection_id.'&quixlogin=true';
        } else {
            $doc->addScriptDeclaration('window.quixEditorMapID = '.$editorId.';');
            $doc->addScriptDeclaration('window.builtWithQuixEditor = false;');
            $url = JUri::root()
                   .'index.php?option=com_quix&task=api.getEditor&source='
                   .$source.'&sid='
                   .$sid.'&quixlogin=true';
        }
        $doc->addScriptDeclaration("window.quixEditorUrl = '".$url."';");

        // view=collections
        // if ($app->isClient('site')) {
        //     return null;
        // }


        // lets implement joomla4 fix
        if (JVERSION < 4) {
            $button          = new JObject();
            $button->class   = 'btn qx-btn';
            $button->link    = $link;
            $button->text    = JText::_('PLG_EDITORS-XTD_QUIX_QUIX_TITLE');
            $button->name    = 'cube quix-icon qx-btn';
            $button->modal   = true;
            $button->options = "{handler: 'iframe', size: {x: 900, y: 500}}";

        } else {

            $button          = new CMSObject;
            $button->modal   = true;
            $button->link    = $link;
            $button->text    = JText::_('PLG_EDITORS-XTD_QUIX_QUIX_TITLE');
            $button->name    = $this->_type.'_'.$this->_name;
            $button->icon    = 'file-add';
            $button->iconSVG = '<svg viewBox="0 0 32 32" width="24" height="24"><path d="M28 24v-4h-4v4h-4v4h4v4h4v-4h4v-4zM2 2h18v6h6v10h2v-10l-8-'
                               .'8h-20v32h18v-2h-16z"></path></svg>';
            $button->options = [
                'height'     => '300px',
                'width'      => '800px',
                'bodyHeight' => '70',
                'modalWidth' => '80',
            ];
        }

        return $button;
    }
}
