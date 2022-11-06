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
defined( '_JEXEC' ) or die( 'Restricted access' );

//DEVNOTE: import MODEL object class
jimport('joomla.application.component.model');


class google_wallet_transactions_detailModelgoogle_wallet_transactions_detail extends JModelLegacy
{
		var $_id_google_wallet_transactions = null;
		var $_data = null;
		var $_table_prefix = null;

	function __construct()
	{
		parent::__construct();
		$jinput = JFactory::getApplication()->input;
		
		//initialize class property
	  	$this->_table_prefix = '#__sv_apptpro3_';			

		$array = $jinput->get( 'cid', array(0), 'post', 'array' );
		
		$this->setId((int)$array[0]);

	}

	/**
	 * Method to set the google_wallet_transactions identifier
	 *
	 * @access	public
	 * @param	int google_wallet_transactions identifier
	 */
	function setId($id_google_wallet_transactions)
	{
		// Set google_wallet_transactions id and wipe data
		$this->_id_google_wallet_transactions		= $id_google_wallet_transactions;
		$this->_data	= null;
	}

	/**
	 * Method to get a google_wallet_transactions
	 *
	 * @since 1.5
	 */
	function &getData()
	{
		// Load the google_wallet_transactions data
		if ($this->_loadData())
		{
		//load the data nothing else	  
		}
		else  $this->_initData();
		//print_r($this->_data);	
		
   	return $this->_data;
	}
	
	/**
	 * Method to checkout/lock the google_wallet_transactions
	 *
	 * @access	public
	 * @param	int	$uid	User ID of the user checking the article out
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function checkout($uid = null)
	{
		if ($this->_id_google_wallet_transactions)
		{
			// Make sure we have a user id to checkout the article with
			if (is_null($uid)) {
				$user	= JFactory::getUser();
				$uid	= $user->get('id');
			}
			// Lets get to it and checkout the thing...
			$google_wallet_transactions = $this->getTable();
			
			
			if(!$google_wallet_transactions->checkout($uid, $this->_id_google_wallet_transactions)) {
				$this->setError($row->getError());
				return false;
			}

			return true;
		}
		return false;
	}
	/**
	 * Method to checkin/unlock the google_wallet_transactions
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function checkin()
	{
		if ($this->_id_google_wallet_transactions)
		{
			$google_wallet_transactions = $this->getTable();
			if(! $google_wallet_transactions->checkin($this->_id_google_wallet_transactions)) {
				$this->setError($row->getError());
				return false;
			}
		}
		return false;
	}	
	/**
	 * Tests if google_wallet_transactions is checked out
	 *
	 * @access	public
	 * @param	int	A user id
	 * @return	boolean	True if checked out
	 * @since	1.5
	 */
	function isCheckedOut( $uid=0 )
	{
		if ($this->_loadData())
		{
			if ($uid) {
				return ($this->_data->checked_out && $this->_data->checked_out != $uid);
			} else {
				return $this->_data->checked_out;
			}
		}
	}	
		
	/**
	 * Method to load content google_wallet_transactions data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function _loadData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = 'SELECT * FROM '.$this->_table_prefix.'google_wallet_transactions WHERE id_google_wallet_transactions = '. $this->_id_google_wallet_transactions;
			$this->_db->setQuery($query);
			$this->_data = $this->_db->loadObject();
			//print_r($this->_data);
			return (boolean) $this->_data;
		}
		return true;
	}

	/**
	 * Method to initialise the google_wallet_transactions data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function _initData()
	{
		return true;
	}
  	

	/**
	 * Method to store the google_wallet_transactions text
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function store($data)
	{
		//DEVNOTE: Load table class from com_rsappt_pro3/tables/google_wallet_transactions_detail.php	
		$row = $this->getTable();

		// Bind the form fields to the google_wallet_transactions table
		if (!$row->bind($data)) {
			$this->setError($row->getError());
			return false;
		}

		// if new item, order last in appropriate group
		if (!$row->id_google_wallet_transactions) {
			$where = 'id_google_wallet_transactions = ' . $row->id_google_wallet_transactions ;
			$row->ordering = $row->getNextOrder ( $where );
		}

		//DEVNOTE: Make sure the google_wallet_transactions table is valid
		//JTable return always true but there is space to put
		//our custom check method
/*		if (!$row->check()) {
			$this->setError($row->getError());
			return false;
		}*/

		// Store the google_wallet_transactions table to the database
		if (!$row->store()) {
			$this->setError($row->getError());
			return false;
		}

		return true;
	}
	

	function delete($cid = array())
	{
		$result = false;


		if (sv_count_($cid ))
		{
			$cids = implode( ',', $cid );
			$query = 'DELETE FROM '.$this->_table_prefix.'google_wallet_transactions WHERE id_google_wallet_transactions IN ( '.$cids.' )';
			$this->_db->setQuery( $query );
			if(!$this->_db->execute()) {
				$this->setError($row->getError());
				return false;
			}
		}

		return true;
	}
	

}

?>
