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
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;

/**
 * Renders a list of plugins entries for the type 'content' and 'system', grouped by type with multiple select options 
 *
 * @package JAMP::plugins::system
 * @subpackage fields
 * @since 1.4.2
 */
class JFormFieldLoadModulesPositions extends ListField {
	function getOptions() {
		$options = array ();
		$db = Factory::getContainer()->get('DatabaseDriver');
		
		$options [] = HTMLHelper::_ ( 'select.option', '<OPTGROUP>', Text::_('PLG_JAMP_MODULES') );
		
		$path = JPATH_SITE . '/modules';
		$iterator = new \DirectoryIterator ( $path );
		foreach ( $iterator as $fileEntity ) {
			$fileName = $fileEntity->getFilename ();
			if (! $fileEntity->isDot () && $fileEntity->isDir () && $fileName !== 'index.html') {
				$value = str_replace('mod_', '', $fileEntity->getFilename());
				$name = ucfirst (preg_replace('/[-_]/i', ' ', $value));
				$modulesOptions [] = HTMLHelper::_ ( 'select.option', $value, $name );
			}
		}
		
		sort($modulesOptions);
		$options = array_merge($options, $modulesOptions);
		$options [] = HTMLHelper::_ ( 'select.option', '</OPTGROUP>' );
		
		// Translate findInPositions pre populated by getData into JOption object
		if((int)$this->positions == 1) {
			$options [] = HTMLHelper::_ ( 'select.option', '<OPTGROUP>', Text::_('PLG_JAMP_MODULES_POSITIONS') );
			$queryModulePositions = "SELECT DISTINCT " . $db->quoteName('position') .
									"\n FROM " . $db->quoteName('#__modules') .
									"\n WHERE " . $db->quoteName('client_id') . " = 0" .
									"\n AND " . $db->quoteName('position') . " != ''" .
									"\n AND " . $db->quoteName('published') . " > 0" .
									"\n ORDER BY " . $db->quoteName('position') . " ASC";
			$currentUsedPositions = $db->setQuery($queryModulePositions)->loadColumn();
			if(!empty($currentUsedPositions)) {
				foreach ($currentUsedPositions as $position) {
					$options[] = HTMLHelper::_('select.option', $position, $position);
				}
			}
			
			$options [] = HTMLHelper::_ ( 'select.option', '</OPTGROUP>' );
		}
		
		array_unshift($options, HTMLHelper::_ ( 'select.option', null, Text::_('PLG_JAMP_SELECT_AN_OPTION')));
		
		return $options;
	}
	
	/**
	 * Method to attach a JForm object to the field.
	 *
	 * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the `<field>` tag for the form field object.
	 * @param   mixed             $value    The form field value to validate.
	 * @param   string            $group    The field name group control value. This acts as as an array container for the field.
	 *                                      For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                      full field name would end up being "bar[foo]".
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   11.1
	 */
	public function setup(SimpleXMLElement $element, $value, $group = null) {
		parent::setup($element, $value, $group);
	
		$attributes = array( 'positions' );
	
		foreach ($attributes as $attributeName) {
			$this->{$attributeName} = $element[$attributeName];
		}
		
		return true;
	}
}
