<?php
/**
* @version		1.0.0
 * @package		Joomla
 * @subpackage	OS Services Booking
 * @author  	Dang Thuc Dam
 * @copyright	Copyright (C) 2016 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die();

class JFormFieldOSMCurrency extends JFormField
{

	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	var $_name = 'ebcurrency';

	function getInput()
	{
		$db = JFactory::getDBO();
		$sql = "SELECT currency_code, currency_name  FROM #__app_sch_currencies ORDER BY currency_name ";
		$db->setQuery($sql);
		$options = array();
		$options[] = JHTML::_('select.option', '', JText::_('Select Currency'), 'currency_code', 'currency_name');
		$options = array_merge($options, $db->loadObjectList());
		return JHTML::_('select.genericlist', $options, $this->name, ' class="input-large form-select" ', 'currency_code', 'currency_name', $this->value);
	}
}