<?php
/**
 * @version		1.0.0
 * @package		Joomla
 * @subpackage	OS Services Booking
 * @author  	Dang Thuc Dam
 * @copyright	Copyright (C) 2022 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */
// no direct access
defined( '_JEXEC' ) or die();
error_reporting(0);
/**
 * 
 * Build the route for the com_osproperty component
 * @param	array	An array of URL arguments
 * @return	array	The URL arguments to use to assemble the subsequent URL.
 * @since	1.5
 */
class OsservicesBookingRouter extends JComponentRouterBase
{
	public function build(&$query)
	{
		$db = JFactory::getDbo();
		$segments = array();
		require_once JPATH_ROOT . '/administrator/components/com_osservicesbooking/helpers/helper.php';
		$db = JFactory::getDbo();
		$queryArr = $query;
		if (isset($queryArr['option']))
			unset($queryArr['option']);
		if (isset($queryArr['Itemid']))
			unset($queryArr['Itemid']);
		//Store the query string to use in the parseRouter method
		$queryString = http_build_query($queryArr);
		
		$app		= JFactory::getApplication();
		$menu		= $app->getMenu();
		
		//We need a menu item.  Either the one specified in the query, or the current active one if none specified
		if (empty($query['Itemid']))
			$menuItem = $menu->getActive();
		else
			$menuItem = $menu->getItem($query['Itemid']);
		
		if (empty($menuItem->query['view']))
		{
			$menuItem = new StdClass;
			$menuItem->query['view'] = '';
		}
			
		$view = isset($query['view']) ? $query['view'] : '';
		$id = 	isset($query['id']) ? (int) $query['id'] : 0;
		$task = isset($query['task']) ? $query['task'] : '';
		
		if($task == ""){
			switch ($view)
			{
				case "category":
					$task = "category_listing";
				break;
				case "employee":
					$task = "default_employeeworks";
				break;
				case "customer":
					$task = "default_customer";
				break;
				case "employeesetting":
					$task = "default_employeesetting";
				break;
				default:
					$task = "default_layout";
				break;
			}
		}
		switch ($task){
			case "default_layout":
				if(isset($query['category_id']) && ($query['category_id'] > 0))
				{
					$category_id = (int)$query['category_id'];
					$db->setQuery("Select * from #__app_sch_categories where id = '".$category_id."'");
					$category = $db->loadObject();
					$segments[] = OSBHelper::getLanguageFieldValue($category,'category_name'). " ".$category_id;
				}
				if(isset($query['vid']) && ($query['vid'] > 0))
				{
					$vid = (int)$query['vid'];
					$db->setQuery("Select * from #__app_sch_venues where id = '".$vid."'");
					$venue = $db->loadObject();
					$segments[] = OSBHelper::getLanguageFieldValue($venue,'venue_name')." -".OSBHelper::getLanguageFieldValue($venue,'address'). " ".$vid;
				}
				if(isset($query['employee_id']) && ($query['employee_id'] > 0))
				{
					$employee_id = (int)$query['employee_id'];
					$db->setQuery("Select id, employee_name from #__app_sch_employee where id = '".$employee_id."'");
					$employee = $db->loadObject();
					$segments[] = $employee->employee_name. " ".$employee_id;
				}
				if(isset($query['sid']) && ($query['sid'] > 0))
				{
					$sid = (int)$query['sid'];
					$db->setQuery("Select id, service_name from #__app_sch_services where id = '".$sid."'");
					$service = $db->loadObject();
					$segments[] = $service->service_name. " ".$sid;
				}
			break;
			case "category_listing":
				if(isset($query['id']) and ($query['id'] > 0))
				{
					$category_id    = (int)$query['id'];
					$db->setQuery("Select * from #__app_sch_categories where id = '".$category_id."'");
					$category       = $db->loadObject();
					$segments[]     = OSBHelper::getLanguageFieldValue($category,'category_name');
				}
			break;
			case "default_payment":
				$segments[] = JText::_('Processing payment');
				$segments[] = "Order ".$query['order_id'];
				unset($query['order_id']);
			break;
			case "default_paymentreturn":
				$segments[] = JText::_('OS_PAYMENT_COMPLETED');
				$segments[] = $query['id'];
			break;
			case "default_paymentfailure":
				$segments[] = JText::_('OS_FAILURE_PAYMENT');
				$segments[] = $query['id'];
			break;
			case "default_orderDetailsForm":
				$segments[] = JText::_('OS_ORDER_DETAILS');
				if(isset($query['id']))
				{
					$segments[] = $query['id'];
					unset($query['id']);
				}
				elseif(isset($query['order_id']))
				{
					$segments[] = $query['order_id'];
					unset($query['order_id']);
				}
				if (!isset($query['Itemid']) or ($query['Itemid'] == 0) or ($query['Itemid'] == 99999) or ($query['Itemid'] == 9999)){
					unset($query['Itemid']);
				}
			break;
			case "default_errorform":
				$segments[] = JText::_('OS_ERROR');
			break;
			case "default_employeesetting":
				$segments[] = JText::_('OS_EMPLOYEE_SETTING');
			break;
			case "form_step1":
				$segments[] = JText::_('OS_CHECKOUT');
				if(isset($query['category_id']) and ($query['category_id'] > 0)){
					$category_id = (int)$query['category_id'];
					$db->setQuery("Select * from #__app_sch_categories where id = '".$category_id."'");
					$category = $db->loadObject();
					$segments[] = JText::_('OS_CATEGORY').": ".OSBHelper::getLanguageFieldValue($category,'category_name');
					unset($query['category_id']);
				}
				if(isset($query['vid']) and ($query['vid'] > 0)){
					$vid = (int)$query['vid'];
					$db->setQuery("Select * from #__app_sch_venues where id = '".$vid."'");
					$venue = $db->loadObject();
					$segments[] = JText::_('OS_VENUE').": ".OSBHelper::getLanguageFieldValue($venue,'venue');
					unset($query['vid']);
				}
				if(isset($query['sid']) and ($query['sid'] > 0)){
					$sid = (int)$query['sid'];
					$db->setQuery("Select * from #__app_sch_services where id = '".$sid."'");
					$service = $db->loadObject();
					$segments[] = JText::_('OS_SERVICE').": ".OSBHelper::getLanguageFieldValue($service,'service_name');
					unset($query['sid']);
				}
				if(isset($query['employee_id']) and ($query['employee_id'] > 0)){
					$employee_id = (int)$query['employee_id'];
					$db->setQuery("Select id, employee_name from #__app_sch_employee where id = '".$employee_id."'");
					$employee = $db->loadObject();
					$segments[] = JText::_('OS_EMPLOYEE').": ".$employee->employee_name;
					unset($query['employee_id']);
				}
				if(isset($query['date_from']) && $query['date_from'] != "" && $query['date_from'] != "0000-00-00 00:00:00"){
					$segments[] = $query['date_from'];
					unset($query['date_from']);
				}
				if(isset($query['date_to']) && $query['date_to'] != "" && $query['date_to'] != "0000-00-00 00:00:00"){
					$segments[] = $query['date_to'];
					unset($query['date_to']);
				}
				if (!isset($query['Itemid']) or ($query['Itemid'] == 0) or ($query['Itemid'] == 99999) or ($query['Itemid'] == 9999)){
					unset($query['Itemid']);
				}
				unset($query['date_from']);
				unset($query['date_to']);
			break;
			case "form_step2":
				$segments[] = JText::_('OS_CONFIRM');
				if(isset($query['category_id']) and ($query['category_id'] > 0)){
					$category_id = (int)$query['category_id'];
					$db->setQuery("Select * from #__app_sch_categories where id = '".$category_id."'");
					$category = $db->loadObject();
					$segments[] = JText::_('OS_CATEGORY').": ".OSBHelper::getLanguageFieldValue($category,'category_name');
					unset($query['category_id']);
				}
				if(isset($query['vid']) and ($query['vid'] > 0)){
					$vid = (int)$query['vid'];
					$db->setQuery("Select * from #__app_sch_venues where id = '".$vid."'");
					$venue = $db->loadObject();
					$segments[] = JText::_('OS_VENUE').": ".OSBHelper::getLanguageFieldValue($venue,'venue');
					unset($query['vid']);
				}
				if(isset($query['sid']) and ($query['sid'] > 0)){
					$sid = (int)$query['sid'];
					$db->setQuery("Select * from #__app_sch_services where id = '".$sid."'");
					$service = $db->loadObject();
					$segments[] = JText::_('OS_SERVICE').": ".OSBHelper::getLanguageFieldValue($service,'service_name');
					unset($query['sid']);
				}
				if(isset($query['employee_id']) and ($query['employee_id'] > 0)){
					$employee_id = (int)$query['employee_id'];
					$db->setQuery("Select id, employee_name from #__app_sch_employee where id = '".$employee_id."'");
					$employee = $db->loadObject();
					$segments[] = JText::_('OS_EMPLOYEE').": ".$employee->employee_name;
					unset($query['employee_id']);
				}
				if(isset($query['date_from']) and ($query['date_from'] > 0)){
					$segments[] = $query['date_from'];
					unset($query['date_from']);
				}
				if(isset($query['date_to']) and ($query['date_to'] > 0)){
					$segments[] = $query['date_to'];
					unset($query['date_to']);
				}
				if (!isset($query['Itemid']) or ($query['Itemid'] == 0) or ($query['Itemid'] == 99999) or ($query['Itemid'] == 9999)){
					unset($query['Itemid']);
				}
			break;
			case "manage_orders":
				if (!isset($query['Itemid']) or ($query['Itemid'] == 0) or ($query['Itemid'] == 99999) or ($query['Itemid'] == 9999)){
					$segments[] = JText::_('OS_MANAGE_ORDERS');
					unset($query['Itemid']);
				}
			break;
			case "default_customer":
				if (!isset($query['Itemid']) or ($query['Itemid'] == 0) or ($query['Itemid'] == 99999) or ($query['Itemid'] == 9999)){
					$segments[] = JText::_('OS_MY_ORDERS_HISTORY');
					unset($query['Itemid']);
				}
			break;
			case "default_employeeworks":
				if (!isset($query['Itemid']) or ($query['Itemid'] == 0) or ($query['Itemid'] == 99999) or ($query['Itemid'] == 9999)){
					$segments[] = JText::_('OS_MY_WORKKING_LIST');
					unset($query['Itemid']);
				}
			break;
			case "calendar_employee":
				//if (!isset($query['Itemid']) or ($query['Itemid'] == 0) or ($query['Itemid'] == 99999) or ($query['Itemid'] == 9999)){
					$segments[] = JText::_('OS_MY_WORKING_CALENDAR');
					unset($query['Itemid']);
				//}
			break;
			case "ajax_showEmployeeTimeslots":
					$segments[] = JText::_('OS_SHOW_EMPLOYEE_TIMESLOTS');
			break;
            case "manage_userinfo":
                $segments[] = JText::_('OS_CUSTOMER_INFORMATION');
                if(isset($query['userId']))
                {
                    $user = JFactory::getUser($query['userId']);
                    $segments[] = $user->id." ". $user->name;
                    unset($query['userId']);
                }
            break;
		}
		
		if (isset($query['start']) || isset($query['limitstart']))
		{
			$limit = $app->getUserState('limit');
			if((int) $limit == 0)
			{
				$limit = 20;
			}
			$limitStart = isset($query['limitstart']) ? (int)$query['limitstart'] : (int)$query['start'];
			$page = ceil(($limitStart + 1) / $limit);
			$segments[] = JText::_('OS_PAGE').'-'.$page;
		}

		if (isset($query['task']))
			unset($query['task']);
		
		if (isset($query['view']))
			unset($query['view']);
		
		if (isset($query['id']))
			unset($query['id']);
		
		if (isset($query['category_id']))
			unset($query['category_id']);
			
		if (isset($query['employee_id']))
			unset($query['employee_id']);
			
		if (isset($query['sid']))
			unset($query['sid']);
			
		if (isset($query['vid']))
			unset($query['vid']);
		
		if (isset($query['layout']))
			unset($query['layout']);
		
		if (count($segments))
		{
			$segments = array_map('JApplicationHelper::stringURLSafe', $segments);
			$key = md5(implode('/', $segments));
			$q = $db->getQuery(true);
			$q->select('COUNT(*)')
				->from('#__app_sch_urls')
				->where('md5_key="'.$key.'"');
			$db->setQuery($q);
			$total = $db->loadResult();
			if (!$total)
			{
				$q->clear();
				$q->insert('#__app_sch_urls')
					->columns('md5_key, `query`')
					->values("'$key', '$queryString'");
				$db->setQuery($q);
				$db->execute();
			}
		}
			
		return $segments;
	}

	/**
	 * 
	 * Parse the segments of a URL.
	 * @param	array	The segments of the URL to parse.
	 * @return	array	The URL attributes to be used by the application.
	 * @since	1.5
	 */
	function parse(& $segments)
	{		
		$vars = array();
		if (count($segments))
		{
			$db = JFactory::getDbo();
			$key = md5(str_replace(':', '-', implode('/', $segments)));
			$query = $db->getQuery(true);
			$query->select('`query`')
				->from('#__app_sch_urls')
				->where('md5_key="'.$key.'"');
			$db->setQuery($query);
			$queryString = $db->loadResult();
			if ($queryString)
			{
				parse_str($queryString, $vars);
			}
			else
			{
				$method = strtoupper(JFactory::getApplication()->input->getMethod());

				if ($method == 'GET')
				{
					throw new Exception('Page not found', 404);
				}
			}

            if (version_compare(JVERSION, '4.0.0-dev', 'ge')) {
                $segments = [];
            }
		}
		
		$app		= JFactory::getApplication();
		$menu		= $app->getMenu();
		if ($item = $menu->getActive())
		{
			foreach ($item->query as $key=>$value)
			{
				if ($key != 'option' && $key != 'Itemid' && !isset($vars[$key]))
					$vars[$key] = $value;
			}
		}
		return $vars;
	}
}

function OsservicesbookingBuildRoute(&$query)
{
    $router = new OsservicesbookingRouter();
    return $router->build($query);
}

function OsservicesbookingParseRoute($segments)
{
    $router = new OsservicesbookingRouter();
    return $router->parse($segments);
}