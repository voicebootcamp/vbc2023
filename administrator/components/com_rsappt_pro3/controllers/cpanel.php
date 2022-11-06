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

//DEVNOTE: import CONTROLLER object class
jimport( 'joomla.application.component.controller' );
jimport( 'joomla.application.component.controlleradmin' );


/**
 * rsappt_pro3  Controller
 */
 
class cpanelController extends JControllerForm
//class rsappt_pro3ControllerCpanel extends JControllerForm
{
    protected $view_list = "cpanel";
	
	/**
	 * Custom Constructor
	 */
	function __construct( $default = array())
	{
		parent::__construct( $default );
	}
	/**
	 * Cancel operation
	 * redirect the application to the begining - index.php  	 
	 */
	function cancel($key=null)
	{
		$this->setRedirect( 'index.php' );
	}

	/**
	 * Method display
	 * 
	 * 1) create a classVIEWclass(VIEW) and a classMODELclass(Model)
	 * 2) pass MODEL into VIEW
	 * 3)	load template and render it  	  	 	 
	 */

	function display($cachable=false, $urlparams=false) {
		parent::display();			
	}
}	
?>

