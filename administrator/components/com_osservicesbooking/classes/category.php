<?php
/*------------------------------------------------------------------------
# category.php - Ossolution emailss Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2016 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die;

class OSappscheduleCategory{
	static function display($option,$task){
		global $mainframe,$jinput;
		$mainframe = JFactory::getApplication();
        $cid       = $jinput->get('cid',array(),'ARRAY');
        \Joomla\Utilities\ArrayHelper::toInteger($cid,array(0));
		switch ($task){
			default:
			case "category_list":
				OSappscheduleCategory::category_list($option);
			break;
			case "category_unpublish":
				OSappscheduleCategory::category_state($option,$cid,0);
			break;
			case "category_publish":
				OSappscheduleCategory::category_state($option,$cid,1);
			break;	
			case "category_remove":
				OSappscheduleCategory::category_remove($option,$cid);
			break;
			case "category_add":
				OSappscheduleCategory::category_modify($option,0);
			break;	
			case "category_edit":
				OSappscheduleCategory::category_modify($option,$cid[0]);
			break;
			case "category_apply":
				OSappscheduleCategory::category_save($option,0);
			break;
			case "category_save":
				OSappscheduleCategory::category_save($option,1);
			break;
            case "category_saveorderAjax":
                OSappscheduleCategory::saveorderAjax($option);
                break;
			case "goto_index":
				$mainframe = JFactory::getApplication();
				$mainframe->redirect("index.php");
			break;
		}
	}
	
	/**
	 * Category list
	 *
	 * @param unknown_type $option
	 */
	static function category_list($option)
    {
		global $mainframe,$configClass,$jinput;
		$db				                = JFactory::getDbo();
		$config							= new JConfig();
		$list_limit						= $config->list_limit;
		$limit			                = $jinput->getInt('limit',$list_limit);
		$limitstart		                = $jinput->getInt('limitstart',0);
		$keyword		                = $db->escape(trim($jinput->getString('keyword','')));

        $filter_order 	 				= $jinput->getString('filter_order','ordering');
        $filter_order_Dir 				= $jinput->getString('filter_order_Dir','');
        $filter_full_ordering			= $jinput->getString('filter_full_ordering','ordering asc');
		if(trim($filter_full_ordering) == "")
		{
			$filter_full_ordering = 'ordering asc';
		}
        $filter_Arr						= explode(" ",$filter_full_ordering);
        $filter_order					= $filter_Arr[0];
        $filter_order_Dir				= $filter_Arr[1];
        if($filter_order == ""){
            $filter_order				= 'ordering';
        }
        $lists['filter_order'] 			= $filter_order;
        $lists['filter_order_Dir']		= $filter_order_Dir;

        $levellimit 					= 10;

		$query			                = "Select count(id) from #__app_sch_categories where 1=1 ";
		if($keyword != "")
		{
			$query                      .= " and (category_name like '%".$keyword."%' or category_description like '%".$keyword."%')";
		}
		$db->setQuery($query);
		$count                          = $db->loadResult();
		jimport('joomla.html.pagination');
		$pageNav                        = new JPagination($count,$limitstart,$limit);
		$query                          = "Select *, category_name as title from #__app_sch_categories where 1=1 ";
		if($keyword != "")
		{
			$query                      .= " and (category_name like '%".$keyword."%' or category_description like '%".$keyword."%')";
		}
		$query                          .= " order by ".$filter_full_ordering;
		$db->setQuery($query, $pageNav->limitstart,$pageNav->limit);
		$rows                           = $db->loadObjectList();
        // establish the hierarchy of the menu
        $children = array();
        // first pass - collect children
        foreach ($rows as $v )
        {
            $pt                         = $v->parent_id;
            $list                       = @$children[$pt] ? $children[$pt] : array();
            array_push( $list, $v );
            $children[$pt]              = $list;
        }

        // second pass - get an indent list of the items
        $list                           = JHTML::_('menu.treerecurse', 0, '', array(), $children, max( 0, $levellimit-1 ) );
        $total                          = count( $list );
        jimport('joomla.html.pagination');
        $pageNav                        = new JPagination( $total, $limitstart, $limit );

        // slice out elements based on limits
        $list                           = array_slice( $list, $pageNav->limitstart, $pageNav->limit);
        $rows                           = $list;
		HTML_OsAppscheduleCategory::listCategories($option,$rows,$pageNav,$keyword, $lists, $children);
	}
	
	/**
	 * Category modification/add new
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	static function category_modify($option,$id){
		global $mainframe,$languages;
		OSBHelper::loadTooltip();
		$db = JFactory::getDbo();
		$row = &JTable::getInstance('Category','OsAppTable');
		if($id > 0){
			$row->load((int)$id);
		}else{
			$row->published = 1;
		}
        $lists['parent'] = self::listParentCategories($row);
		// creat published
		
		$translatable = JLanguageMultilang::isEnabled() && count($languages);
		HTML_OSappscheduleCategory::editCategory($option,$row,$lists,$translatable);
	}
	
	/**
	 * Category save
	 *
	 * @param unknown_type $option
	 * @param unknown_type $save
	 */
	static function category_save($option,$save)
	{
		global $mainframe,$configClass,$languages,$jinput;
		$db   = JFactory::getDbo();
		$id	  = $jinput->getInt('id',0);
		$post = $jinput->post->getArray();
		$row  = &JTable::getInstance('Category','OsAppTable');
		
		$remove_image = $jinput->getInt('remove_photo',0);
		$row->bind($post);
		if(is_uploaded_file($_FILES['image']['tmp_name']))
		{
			$photo_name = time()."_".str_replace(" ","_",$_FILES['image']['name']);
			move_uploaded_file($_FILES['image']['tmp_name'],JPATH_ROOT."/images/osservicesbooking/category/".$photo_name);
			$row->category_photo = $photo_name;
		}
		elseif($remove_image == 1)
		{
			$row->category_photo = "";
		}
		elseif($id == 0)
		{
			$row->category_photo = "";
		}
		
        $row->category_description = $_POST['category_description'];
		$msg = JText::_('OS_ITEM_HAS_BEEN_SAVED'); 
	 	if (!$row->store())
		{
		 	$msg = JText::_('OS_ERROR_SAVING')." - ".$row->getError();		 			 	
		}
		
		$translatable = JLanguageMultilang::isEnabled() && count($languages);
		if($translatable){
			foreach ($languages as $language){	
				$sef = $language->sef;
				$category_name = $jinput->get('category_name_'.$sef,'','string');
				if($category_name == ""){
					$category_name = $row->category_name;
				}
				if($category_name != ""){
					$category = &JTable::getInstance('Category','OsAppTable');
					$category->id = $row->id;
					$category->{'category_name_'.$sef} = $category_name;
					$category->store();
				}
				
				$category_description_language = $_POST['category_description_'.$sef];
				if($category_description_language == ""){
					$category_description_language = $row->category_description;
				}
				if($category_description_language != ""){
					$category = &JTable::getInstance('Category','OsAppTable');
					$category->id = $row->id;
					$category->{'category_description_'.$sef} = $category_description_language;
					$category->store();
				}
			}
		}
		$mainframe->enqueueMessage($msg);
		if($save == 1){
			$mainframe->redirect("index.php?option=com_osservicesbooking&task=category_list");
		}else{
			$mainframe->redirect("index.php?option=com_osservicesbooking&task=category_edit&cid[]=$row->id");
		}
	}
	
	/**
	 * publish or unpublish agent
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 * @param unknown_type $state
	 */
	static function category_state($option,$cid,$state){
		global $mainframe;
		$db 		= JFactory::getDBO();
		if(count($cid)>0)	{
			$cids 	= implode(",",$cid);
			$db->setQuery("UPDATE #__app_sch_categories SET `published` = '$state' WHERE id IN ($cids)");
			$db->execute();
		}
		$mainframe->enqueueMessage(JText::_("OS_ITEMS_STATUS_HAS_BEEN_CHANGED"),'message');
		OSappscheduleCategory::category_list($option);
	}
	
	/**
	 * remove agent
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 */
	static function category_remove($option,$cid){
		global $mainframe;
		$db = JFactory::getDBO();
		if(count($cid)>0)	{
			$cids = implode(",",$cid);
			$db->setQuery("DELETE FROM #__app_sch_categories WHERE id IN ($cids)");
			$db->execute();
			
		}
		$mainframe->enqueueMessage(JText::_("OS_ITEMS_HAS_BEEN_DELETED"),'message');
		OSappscheduleCategory::category_list($option);
	}

    /**
     * Build the select list for parent menu item
     */
    public static function listParentCategories( $row )
	{
		global $mapClass;
        $db = JFactory::getDBO();

        // If a not a new item, lets set the menu item id
        if ( $row->id ) {
            $id = ' AND id != '.(int) $row->id;
        } else {
            $id = null;
        }

        // In case the parent was null
        if (!$row->parent_id) {
            $row->parent_id = 0;
        }

        // get a list of the menu items
        // excluding the current cat item and its child elements
        $query = 'SELECT *, category_name AS title ' .
            ' FROM #__app_sch_categories ' .
            ' WHERE published = 1' .
            $id .
            ' ORDER BY parent_id, ordering';
        $db->setQuery( $query );
        $mitems = $db->loadObjectList();

        // establish the hierarchy of the menu
        $children = array();

        if ( $mitems )
        {
            // first pass - collect children
            foreach ( $mitems as $v )
            {
                $pt 	= $v->parent_id;
                $list 	= @$children[$pt] ? $children[$pt] : array();
                array_push( $list, $v );
                $children[$pt] = $list;
            }
        }

        // second pass - get an indent list of the items
        $list = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0 );

        // assemble menu items to the array
        $parentArr 	= array();
        $parentArr[] 	= JHTML::_('select.option',  '0', JText::_( 'Top' ) );

        foreach ( $list as $item ) {
            if($item->treename != ""){
                $item->treename = str_replace("&nbsp;","",$item->treename);
            }
            $var = explode("-",$item->treename);
            $treename = "";
            for($i=0;$i<count($var)-1;$i++){
                $treename .= " - ";
            }
            $text = $item->treename;
            $parentArr[] = JHTML::_('select.option',  $item->id,$text);
        }
        $output = JHTML::_('select.genericlist', $parentArr, 'parent_id', 'class="'.$mapClass['input-large'].' form-select ilarge"', 'value', 'text', $row->parent_id );
        return $output;
    }

    static function saveorderAjax($option){
        global $jinput;
        $db				= JFactory::getDBO();
        $cid 			= $jinput->get( 'cid', array(), 'array' );
        $order			= $jinput->get( 'order', array(), 'array' );
        $row			= JTable::getInstance('Category','OsAppTable');
        $groupings		= array();
        // update ordering values
        $txt = "";
        for( $i=0; $i < count($cid); $i++ )
        {
            $row->load( $cid[$i] );
            // track parents
            $groupings[] = $row->parent_id;
            if ($row->ordering != $order[$i])
            {
                $row->ordering = $order[$i];
                $txt .= $cid[$i]." ".$row->ordering."/n";
                $row->store();
            } // if
        } // for
        // execute updateOrder for each parent group
        $groupings = array_unique( $groupings );
        foreach ($groupings as $group)
        {
            $row->reorder(' parent_id = '.(int) $group.' AND published = 1');
        }
    }
}
?>