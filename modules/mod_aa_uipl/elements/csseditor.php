<?php
/*------------------------------------------------------------------------
# AA User IP and Location
# ------------------------------------------------------------------------
# author    AA Extensions https://aaextensions.com/
# Copyright (C) 2018 AA Extensions. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: https://aaextensions.com/
-------------------------------------------------------------------------*/
defined('_JEXEC') or die;

jimport('joomla.form.formfield');

class JFormFieldCSSEditor extends JFormField {
	protected $type = 'CSSEditor';

	protected function getInput() {
		if(!JPluginHelper::isEnabled('editors', 'codemirror')) return '<strong>Sorry: this module needs the CodeMirror editor plug-in to be enabled</strong>';
		$app = JFactory::getApplication('administrator');
		$app->setUserState('editor.source.syntax', 'css');
		$version = new JVersion;
		if ($version->isCompatible('4.0')) {
         // Call the editor
         $editor = JEditor::getInstance('codemirror');
        } else {
        	$editor = JFactory::getEditor('codemirror');
        }
		
		$params = array(
				 'linenumbers'=> '1' ,
                 'tabmode'  => 'shift'
                 );
		$html = '<div style="clear: both;"></div>'.$editor->display($this->name, $this->value, '400', '245', '20', '20', false, $this->id, null, null, $params);
		$app->setUserState('editor.source.syntax', null);
		return $html;
	}
}