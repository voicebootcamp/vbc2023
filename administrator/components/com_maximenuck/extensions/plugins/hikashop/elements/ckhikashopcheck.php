<?php
/**
 * @copyright	Copyright (C) 2020 Cedric KEIFLIN alias ced1870
 * https://www.joomlack.fr
 * @license		GNU/GPL
 * */
 
defined('JPATH_BASE') or die;
jimport('joomla.form.formfield');

class JFormFieldCkhikashopcheck extends JFormField {

	protected $type = 'ckhikashopcheck';

	protected function getLabel()
	{
		return '';
	}

	protected function getInput()
	{
		$html = '';
		if (! file_exists(JPATH_ROOT . '/administrator/components/com_maximenuckhikashop/maximenuckhikashop.php')) {
			$html .= '<div class="ckinfo"><i class="fas fa-exclamation-triangle" style="color:red;"></i><a href="https://www.joomlack.fr/telecharger-extensions-joomla/view_document/76-patch-maximenu-ck-hikashop-joomla-3-x" target="_blank">' . JText::_('MAXIMENUCK_HIKASHOP_COMPONENT_MISSING') . '</a></div>';
		}
		return $html;
	}
}
