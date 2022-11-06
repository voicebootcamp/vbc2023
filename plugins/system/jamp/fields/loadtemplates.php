<?php
/**
 * Form field list
 * @package JAMP::plugins::system
 * @subpackage fields
 * @author Joomla! Extensions Store
 * @copyright (C)2016 Joomla! Extensions Store
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die();
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;

/**
 * Renders a configurable filelist element
 *
 * @package JAMP::plugins::system
 * @subpackage fields
 * @since 2.0
 */
 
class JFormFieldLoadTemplates extends ListField {
	/**
	 * The form field type.
	 *
	 * @var    string
	 *
	 * @since  11.1
	 */
	protected $type = 'Loadtemplates';
	
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
	
		// Initialize some field attributes.
		$attr .= $this->element ['class'] ? ' class="' . ( string ) $this->element ['class'] . ' form-select"' : 'class="form-select"';
	
		// To avoid user's confusion, readonly="true" should imply
		// disabled="true".
		if (( string ) $this->element ['readonly'] == 'true' || ( string ) $this->element ['disabled'] == 'true') {
			$attr .= ' disabled="disabled"';
		}
	
		$attr .= $this->element ['size'] ? ' size="' . ( int ) $this->element ['size'] . '"' : '';
		$attr .= $this->multiple ? ' multiple="multiple"' : '';
	
		// Initialize JavaScript field attributes.
		$attr .= $this->element ['onchange'] ? ' onchange="' . ( string ) $this->element ['onchange'] . '"' : '';
		$attr .= $this->element ['style'] ? ' style="' . ( string ) $this->element ['style'] . '"' : '';
		
		// Get the field options.
		$options = ( array ) $this->getOptions ();
	
		$html = HTMLHelper::_ ( 'select.genericlist', $options, $this->name, trim ( $attr ), 'value', 'text', $this->value, $this->id );
	
		return $html;
	}
		
	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   11.1
	 */
	protected function getOptions() {
		$db = Factory::getDbo ();
		$styles = "SELECT DISTINCT " . $db->quoteName('template') . " AS " . $db->quoteName('value') . "," .
				  "\n " .$db->quoteName('template') . " AS " . $db->quoteName('text') .
				  "\n FROM " . $db->quoteName('#__template_styles') .
				  "\n WHERE " . $db->quoteName('client_id') . " = 0";
		$options = $db->setQuery($styles)->loadObjectList();
	
		$defaultOption = HTMLHelper::_('select.option', '', Text::_('PLG_JAMP_DEFAULT_TEMPLATE'));
		array_unshift($options, $defaultOption);
	
		return $options;
	}

}
