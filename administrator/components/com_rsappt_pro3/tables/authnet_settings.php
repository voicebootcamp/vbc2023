<?php
/*
 ****************************************************************
 Copyright (C) 2008-2020 Soft Ventures, Inc. All rights reserved.
 ****************************************************************
 * @package	Appointment Booking Pro - ABPro
 * @copyright	Copyright (C) 2008-2020 Soft Ventures, Inc. All rights reserved.
 * @license	GNU/GPL, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 * ABPro is distributed WITHOUT ANY WARRANTY, or implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This header must not be removed. Additional contributions/changes
 * may be added to this header as long as no information is deleted.
 *
 ************************************************************
 The latest version of ABPro is available to subscribers at:
 http://www.appointmentbookingpro.com/
 ************************************************************
 */


// no direct access
defined('_JEXEC') or die('Restricted access');

/**
* config Table class
*/
class Tableauthnet_settings extends JTable
{
	protected $_supportNullValue = true; 
	
	var $id_authnet_settings = null;
	var $authnet_enable = null;
	var $authnet_server = null;
	var $authnet_api_login_id = null;
	var $authnet_transaction_key = null;
	var $authnet_header_text = null;
	var $authnet_footer_text = null;
	var $authnet_button_url = null;
	var $_table_prefix = null;

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 * @since 1.0
	 */
	function __construct($db) {
	
	  //initialize class property
	  $this->_table_prefix = '#__sv_apptpro3_';
	  
		parent::__construct($this->_table_prefix.'authnet_settings', 'id_authnet_settings', $db);
	}

	/**
	* Overloaded bind function
	*
	* @acces public
	* @param array $hash named array
	* @return null|string	null is operation was satisfactory, otherwise returns an error
	* @see JTable:bind
	* @since 1.5
	*/

	function bind($array, $ignore = '')
	{
		if (key_exists( 'params', $array ) && is_array( $array['params'] )) {
			$registry = new JRegistry();
			$registry->loadArray($array['params']);
			$array['params'] = $registry->toString();
		}

		return parent::bind($array, $ignore);
	}

	/**
	 * Overloaded check method to ensure data integrity
	 *
	 * @access public
	 * @return boolean True on success
	 * @since 1.0
	 */
//	function check()
//	{
//		/** check for valid name */
//		if (trim($this->name) == '') {
//			$this->_error = JText::_('Your resource MUST CONTAIN an Name.');
//			return false;
//		}
//
//
//		/** check for existing name */
//		$query = 'SELECT id FROM '.$this->_table_prefix.'config  WHERE name = "'.$this->name;
//		$this->_db->setQuery($query);
//
//		$xid = intval($this->_db->loadResult());
//		if ($xid && $xid != intval($this->id)) {
//			$this->_error = JText::sprintf('WARNNAMETRYAGAIN', JText::_('resource'));
//			return false;
//		}
//		return true;
//	}
}
?>

