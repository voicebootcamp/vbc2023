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
class JFormFieldLoadModulesTitles extends ListField {
	function getOptions() {
		$options = array ();
		$db = Factory::getContainer()->get('DatabaseDriver');
		
		
		$queryModulePositions = "SELECT" .
								"\n DISTINCT " . $db->quoteName('title') . "," .
								"\n " . $db->quoteName('module') .
								"\n FROM " . $db->quoteName('#__modules') .
								"\n WHERE " . $db->quoteName('client_id') . " = 0" .
								"\n AND " . $db->quoteName('position') . " != ''" .
								"\n AND " . $db->quoteName('published') . " > 0" .
								"\n ORDER BY " . $db->quoteName('module') . " ASC, " . 
												 $db->quoteName('title') . " ASC";
		$modules = $db->setQuery($queryModulePositions)->loadObjectList();
		if(!empty($modules)) {
			$lastMenuType = null;
			$tmpMenuType = null;
			foreach ($modules as $module) {
				
				if ($module->module != $lastMenuType) {
					if ($tmpMenuType) {
						$options [] = HTMLHelper::_ ( 'select.option', '</OPTGROUP>' );
					}
					$moduleOptionName = str_replace('mod_', '', $module->module);
					$moduleOptionName = ucfirst (preg_replace('/[-_]/i', ' ', $moduleOptionName));
					
					$options [] = HTMLHelper::_ ( 'select.option', '<OPTGROUP>', $moduleOptionName );
					$lastMenuType = $module->module;
					$tmpMenuType = $module->module;
				}
				
				$options[] = HTMLHelper::_('select.option', htmlspecialchars($module->title, ENT_COMPAT, 'UTF-8', false), $module->title);
			}
		}
		
		
		array_unshift($options, HTMLHelper::_ ( 'select.option', null, Text::_('PLG_JAMP_SELECT_AN_OPTION')));
		
		return $options;
	}
}
