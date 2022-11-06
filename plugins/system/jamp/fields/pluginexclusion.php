<?php
/** 
 * Form field list
 * @package JAMP::plugins::system
 * @subpackage fields
 * @author Joomla! Extensions Store 
 * @copyright (C)2016 Joomla! Extensions Store
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Plugin\PluginHelper;

/**
 * Renders a list of plugins entries for the type 'content' and 'system', grouped by type with multiple select options 
 *
 * @package JAMP::plugins::system
 * @subpackage fields
 * @since 1.0
 */
class JFormFieldPluginExclusion extends ListField {
	/**
	 * The form field type.
	 *
	 * @var    string
	 *
	 * @since  11.1
	 */
	protected $type = 'PluginExclusion';
	
	/**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribute to enable multiselect.
	 *
	 * @return string The field input markup.
	 *
	 * @since 11.1
	 */
	protected function getInput() {
		// Initialize variables.
		$html = array ();
		$attr = '';
	
		$attr .= 'style="height: 300px;" class="form-select"';
		$attr .= $this->multiple ? ' multiple="multiple"' : '';
	
		// Get the field options.
		$options = ( array ) $this->getOptions ();
	
		$html = HTMLHelper::_ ( 'select.genericlist', $options, $this->name, trim ( $attr ), 'value', 'text', $this->value, $this->id );
	
		return $html;
	}
	
	/**
	 * Build the multiple select list for Menu Links/Pages
	 * 
	 * @access public
	 * @return array
	 */
	protected function getOptions() {
		$pluginItemsSystem = PluginHelper::getPlugin('system');
		$pluginItemsContent = PluginHelper::getPlugin('content');
		$pluginItemsCaptcha = PluginHelper::getPlugin('captcha');
		$lastPluginType = null;
		$tmpPluginType = null;
		
		$allPluginEntries = array_merge($pluginItemsSystem, $pluginItemsContent, $pluginItemsCaptcha);
		
		foreach ( $allPluginEntries as $plugin ) {
			if ($plugin->type != $lastPluginType) {
				if ($tmpPluginType) {
					$pluginItems [] = HTMLHelper::_ ( 'select.option', '</OPTGROUP>' );
				}
				$pluginItems [] = HTMLHelper::_ ( 'select.option', '<OPTGROUP>', $plugin->type );
			}
			$lastPluginType = $plugin->type;
			$tmpPluginType = $plugin->type;
			
			$pluginItems [] = HTMLHelper::_ ( 'select.option', ($plugin->type . '-' . $plugin->name), $plugin->name );
		}
		if ($lastPluginType !== null) {
			$pluginItems [] = HTMLHelper::_ ( 'select.option', '</OPTGROUP>' );
		}
		
		return $pluginItems;
	}
}