<?php
namespace JExtstore\Component\JMap\Administrator\Framework\Helpers;
/**
 * @package JMAP::FRAMEWORK::administrator::components::com_jmap
 * @subpackage framework
 * @subpackage helpers
 * @author Joomla! Extensions Store
 * @copyright (C) 2021 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

/**
 * Override for html utility functions
 *
 * @package JMAP::FRAMEWORK::administrator::components::com_jmap
 * @subpackage framework
 * @subpackage helpers
 * @since 3.5
 */
class Html {
	/**
	 * Generates a yes/no radio list.
	 *
	 * @param   string  $name      The value of the HTML name attribute
	 * @param   array   $attribs   Additional HTML attributes for the `<select>` tag
	 * @param   string  $selected  The key that is selected
	 * @param   string  $yes       Language key for Yes
	 * @param   string  $no        Language key for no
	 * @param   mixed   $id        The id for the field or false for no id
	 *
	 * @return  string  HTML for the radio list
	 *
	 * @since   1.5
	 */
	public static function booleanlist($name, $attribs = array(), $selected = null, $yes = 'JYES', $no = 'JNO', $id = false) {
		$arr = array(HTMLHelper::_('select.option', '1', Text::_($yes)), HTMLHelper::_('select.option', '0', Text::_($no)));
	
		return self::radiolist( $arr, $name, $attribs, 'value', 'text', (int) $selected, $id);
	}
	
	/**
	 * Generates an HTML radio list.
	 *
	 * @param   array    $data       An array of objects
	 * @param   string   $name       The value of the HTML name attribute
	 * @param   string   $attribs    Additional HTML attributes for the `<select>` tag
	 * @param   mixed    $optKey     The key that is selected
	 * @param   string   $optText    The name of the object variable for the option value
	 * @param   string   $selected   The name of the object variable for the option text
	 * @param   boolean  $idtag      Value of the field id or null by default
	 * @param   boolean  $translate  True if options will be translated
	 *
	 * @return  string  HTML for the select list
	 *
	 * @since   1.5
	 */
	public static function radiolist($data, $name, $attribs = null, $optKey = 'value', $optText = 'text', $selected = null, $idtag = false, $translate = false) {
		if (is_array($attribs))
		{
			$attribs = ArrayHelper::toString($attribs);
		}
	
		$id_text = $idtag ?: $name;
	
		$html = '<div class="controls">';
	
		foreach ($data as $obj)
		{
			$k = $obj->$optKey;
			$t = $translate ? Text::_($obj->$optText) : $obj->$optText;
			$id = (isset($obj->id) ? $obj->id : null);
	
			$extra = '';
			$id = $id ? $obj->id : $id_text . $k;
	
			if (is_array($selected))
			{
				foreach ($selected as $val)
				{
					$k2 = is_object($val) ? $val->$optKey : $val;
	
					if ($k == $k2)
					{
						$extra .= ' selected="selected" ';
						break;
					}
				}
			}
			else
			{
				$extra .= ((string) $k === (string) $selected ? ' checked="checked" ' : '');
			}
	
			$html .= "\n\t" . '<label for="' . $id . '" id="' . $id . '-lbl" class="radio">';
			$html .= "\n\t\n\t" . '<input type="radio" name="' . $name . '" id="' . $id . '" value="' . $k . '" ' . $extra
			. $attribs . '>' . $t;
			$html .= "\n\t" . '</label>';
		}
	
		$html .= "\n";
		$html .= '</div>';
		$html .= "\n";
	
		return $html;
	}
	
	/**
	 * Method to create an icon for saving a new ordering in a grid
	 *
	 * @param   array   $rows   The array of rows of rows
	 * @param   string  $image  The image [UNUSED]
	 * @param   string  $task   The task to use, defaults to save order
	 *
	 * @return  string
	 *
	 * @since   1.5
	 */
	public static function order($rows, $image = 'filesave.png', $task = 'saveorder') {
		return '<a href="javascript:JMapSaveOrder('
				. (count($rows) - 1) . ', \'' . $task . '\')" rel="tooltip" class="saveorder btn btn-sm btn-secondary float-end" title="'
				. Text::_('JLIB_HTML_SAVE_ORDER') . '"><span class="icon-menu-2"></span></a>';
	}
}

