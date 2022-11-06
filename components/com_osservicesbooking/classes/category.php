<?php
/*------------------------------------------------------------------------
# category.php - Ossolution Services Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2022 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

// No direct access.
defined('_JEXEC') or die;

class OSappscheduleCategory{
	/**
	 * Default function
	 *
	 * @param unknown_type $option
	 */
	static function display($option,$task){
		global $mainframe,$mapClass;
		$mainframe = JFactory::getApplication();
		switch ($task){
			default:
			case "category_listing":
				OSappscheduleCategory::listCategories();
			break;
		}
	}
	
	/**
	 * List Categories
	 *
	 */
	static function listCategories(){
		global $mainframe,$configClass,$mapClass, $jinput;
		$document       = JFactory::getDocument();
		$id             = $jinput->getInt('id',0);
        $list_type      = 0;
		$menu           = JFactory::getApplication()->getMenu()->getActive();
		if (is_object($menu))
		{
	        $params = $menu->getParams();
            if($params->get('page_title') != "")
            {
                $document->setTitle($params->get('page_title'));
            }
            else
            {
                $document->setTitle($configClass['business_name'].' | '.JText::_('OS_LIST_ALL_CATEGORIES'));
            }
            $list_type = $params->get('list_type',0);
			$introtext = $params->get('introtext','');
		}
		else
		{
            $document->setTitle($configClass['business_name'].' | '.JText::_('OS_LIST_ALL_CATEGORIES'));
        }

		$db         = JFactory::getDbo();
		$db->setQuery("Select * from #__app_sch_categories where parent_id = '$id' and published = '1' order by ordering");
		$categories = $db->loadObjectList();
		if(count($categories))
        {
            foreach($categories as $category)
            {
                $db->setQuery("Select count(id) from #__app_sch_categories where parent_id = '$category->id' and published = '1'");
                $count = $db->loadResult();
                if($count > 0)
                {
                    $category->link = JRoute::_('index.php?option=com_osservicesbooking&view=category&id='.$category->id.'&Itemid='.$jinput->getInt('Itemid',0));
                }
                else
                {
                    $category->link = JRoute::_('index.php?option=com_osservicesbooking&task=default_layout&category_id='.$category->id.'&Itemid='.$jinput->getInt('Itemid',0));
                }
            }
        }
		HTML_OSappscheduleCategory::listCategories($categories,$params,$list_type,$introtext);
	}
}
?>