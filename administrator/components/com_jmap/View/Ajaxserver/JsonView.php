<?php 
namespace JExtstore\Component\JMap\Administrator\View\Ajaxserver;

/**
 * @author Joomla! Extensions Store
 * @package JMAP::AJAXSERVER::administrator::components::com_jmap
 * @subpackage views
 * @subpackage ajaxserver
 * @copyright (C) 2021 Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html  
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
use JExtstore\Component\JMap\Administrator\Framework\View as JMapView;

/**
 * Config view
 *
 * @package JMAP::AJAXSERVER::administrator::components::com_jmap
 * @subpackage views
 * @subpackage ajaxserver
 * @since 1.0
 */
class JsonView extends JMapView {
	/**
	 * Return application/json response to JS client APP
	 * Replace $tpl optional param with $userData contents to inject
	 *        	
	 * @access public
	 * @param string $tpl
	 * @return void
	 */
	public function display($userData = null) {
		echo json_encode($userData);  
	}
}