<?php
/*------------------------------------------------------------------------
# service.php - Ossolution Services Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2022 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

// No direct access.
defined('_JEXEC') or die;

class OSappscheduleService{
	/**
	 * Osproperty default
	 *
	 * @param unknown_type $option
	 */
	static function display($option,$task){
		switch ($task){
			case "service_listing":
                OSappscheduleService::servicesListing($option);
			break;
            case "service_listallitems":
                OSappscheduleService::itemsListing($option);
            break;
            case "service_calendarview":
                OSappscheduleService::calendarView();
                break;
		}
	}

    /**
     * This static function is used to show Calendar
     */
	static function calendarView()
	{
        global $mainframe,$configClass,$jinput;
        $document = JFactory::getDocument();
        $id = $jinput->getInt('id',0);
		$vid = $jinput->getInt('vid',0);
        if($id > 0)
		{
            $menus = JFactory::getApplication()->getMenu();
            $menu = $menus->getActive();
            $year = $jinput->getInt('year',date("Y",time()));
            $month =  intval($jinput->getInt('month',date("m",time())));
            $params = new JRegistry() ;
            if (is_object($menu)) 
			{
                $params = $menu->getParams();
                if($params->get('page_title') != "")
				{
                    $document->setTitle($params->get('page_title'));
                }
				else
				{
                    $document->setTitle(JText::_('OS_CALENDAR_VIEW'));
                }
            }
			else
			{
                $document->setTitle(JText::_('OS_CALENDAR_VIEW'));
            }
            HTML_OsAppscheduleService::showCalendarView($id,$vid,$year,$month,$params);
        }
        //else do nothing
    }

    /**
     * List Services
     *
     */
    static function servicesListing()
	{
        global $mainframe,$configClass, $jinput;
        $document		= JFactory::getDocument();
        $menus			= JFactory::getApplication()->getMenu();
        $menu			= $menus->getActive();
        $list_type		= 0;
		$category_id	= $jinput->getInt('category_id',0);
		$catSql			= "";
		if($category_id > 0)
		{
			$catSql		= " and category_id = '$category_id'";
		}
		$params			= new JRegistry() ;
        if (is_object($menu)) 
		{
            $params = $menu->getParams();
            if($params->get('page_title') != "")
			{
                $document->setTitle($params->get('page_title'));
            }
			else
			{
                $document->setTitle($configClass['business_name'].' | '.JText::_('OS_LIST_ALL_SERVICES'));
            }
            $list_type = $params->get('list_type',0);
			$introtext = $params->get('introtext','');
        }
		else
		{
            $document->setTitle($configClass['business_name'].' | '.JText::_('OS_LIST_ALL_SERVICES'));
        }
        $db = JFactory::getDbo();
        $db->setQuery("Select * from #__app_sch_services where published = '1' ".HelperOSappscheduleCommon::returnAccessSql('')." $catSql order by ordering");
        $services = $db->loadObjectList();
        HTML_OsAppscheduleService::listServices($services,$params,$list_type,$category_id,$introtext);
    }

    static function itemsListing($option)
	{
        global $mainframe,$configClass;
        $document			= JFactory::getDocument();
        $menus				= JFactory::getApplication()->getMenu();
        $menu				= $menus->getActive();
        $show_category		= 0;
        $show_service		= 0;
        $show_employee		= 0;
        if (is_object($menu)) 
		{
            $params = $menu->getParams();
            if($params->get('page_title') != "")
			{
                $document->setTitle($params->get('page_title'));
            }
            $show_category	= $params->get('show_category',0);
            $show_service	= $params->get('show_service',0);
            $show_employee	= $params->get('show_employee',0);
            $max_category	= $params->get('max_category',0);
            $max_service	= $params->get('max_service',0);
            $max_employee	= $params->get('max_employee',0);
			$introtext		= $params->get('introtext','');

        }
        $db = JFactory::getDbo();
        $services = array();
        $employees = array();
        $categories = array();
        if($show_service == 1) {
            $db->setQuery("Select * from #__app_sch_services where published = '1' " . HelperOSappscheduleCommon::returnAccessSql('') . " order by ordering limit $max_service");
            $services = $db->loadObjectList();
        }

        if($show_category == 1) {
            $db->setQuery("Select * from #__app_sch_categories where published = '1' order by ordering limit $max_category");
            $categories = $db->loadObjectList();
        }

        if($show_employee == 1) {
            $db->setQuery("Select * from #__app_sch_employee where published = '1' order by employee_name limit $max_employee");
            $employees = $db->loadObjectList();
        }

        HTML_OsAppscheduleService::listItems($services,$categories,$employees,$show_category,$show_service,$show_employee,$params,$introtext);
    }
}
?>