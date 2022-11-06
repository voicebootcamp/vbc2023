<?php
/*------------------------------------------------------------------------
# employee.php - Ossolution Services Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2022 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die;

/**
 * Enter description here...
 *
 */
class OSappscheduleEmployee
{
	/**
	 * Default static function
	 *
	 * @param unknown_type $option
	 */
	static function display($option,$task)
	{
		global $mainframe,$jinput;
		$mainframe = JFactory::getApplication();
		$cid       = $jinput->get('cid',array(),'ARRAY');
		\Joomla\Utilities\ArrayHelper::toInteger($cid,array(0));
		switch ($task){
			default:
			case "employee_list":
				OSappscheduleEmployee::employee_list($option);
			break;
			case "employee_saveorderAjax":
                OSappscheduleEmployee::saveorderAjax($option);
            break;
			case "employee_unpublish":
				OSappscheduleEmployee::employee_state($option,$cid,0);
			break;
			case "employee_publish":
				OSappscheduleEmployee::employee_state($option,$cid,1);
			break;	
			case "employee_remove":
				OSappscheduleEmployee::employee_remove($option,$cid);
			break;
			case "employee_add":
				OSappscheduleEmployee::employee_modify($option,0);
			break;	
			case "employee_edit":
				OSappscheduleEmployee::employee_modify($option,$cid[0]);
			break;
			case "employee_apply":
				OSappscheduleEmployee::employee_save($option,0);
			break;
			case "employee_save":
				OSappscheduleEmployee::employee_save($option,1);
			break;
			case "employee_orderup":
				OSappscheduleEmployee::employee_order($option,$cid[0],-1);
			break;
			case "employee_orderdown":
				OSappscheduleEmployee::employee_order($option,$cid[0],1);
			break;
			case "employee_saveorder":
				OSappscheduleEmployee::employee_saveorder($option,$cid);
			break;
			case "employee_removeRestday":
				OSappscheduleEmployee::removeRestDay($option);
			break;
            case "employee_removeBusytime":
                OSappscheduleEmployee::removeBusyTime($option);
            break;
			case "employee_availability":
				OSappscheduleEmployee::availCalendar($option);
			break;
			case "employee_gotoemployeelist":
				OSappscheduleEmployee::gotoEmployeeList($option);
			break;
			case "employee_setupbreaktime":
				OSappscheduleEmployee::setupBreaktime($option);
			break;
			case "employee_gotoemployeeedit":
				OSappscheduleEmployee::gotoemployeeedit();
			break;
			case "employee_savebreaktime":
				OSappscheduleEmployee::saveBreakTime(1);
			break;
			case "employee_applybreaktime":
				OSappscheduleEmployee::saveBreakTime(0);
			break;
			case "employee_addcustombreaktime":
				OSappscheduleEmployee::saveCustomerBreakTime();
			break;
			case "employee_removecustombreaktime":
				OSappscheduleEmployee::removeCustomerBreakTime();
			break;
			case "employee_duplicate":
				OSappscheduleEmployee::duplicateEmployee($cid[0]);
			break;
		}
	}
	
	static function saveorderAjax($option)
	{
        global $jinput;
        $db				= JFactory::getDBO();
        $cid 			= $jinput->get( 'cid', array(), 'array' );
        $order			= $jinput->get( 'order', array(), 'array' );
        $row			= JTable::getInstance('Employee','OsAppTable');
        $groupings		= array();
        // update ordering values
        $txt = "";
        for( $i=0; $i < count($cid); $i++ )
        {
            $row->load( $cid[$i] );
            // track parents
            if ($row->ordering != $order[$i])
            {
                $row->ordering = $order[$i];
                $txt .= $cid[$i]." - ".$row->ordering."     ";
                $row->store();
				//$row->reorder();
            } // if
        } // for
        //$myfile = fopen(JPATH_ROOT."/newfile.txt", "w") or die("Unable to open file!");
		//fwrite($myfile, $txt);
		//fclose($myfile);
		for( $i=0; $i < count($cid); $i++ )
        {
			$row->load( $cid[$i] );
			$row->reorder();
		}
    }

	/**
	 * Enter description here...
	 *
	 */
	static function gotoEmployeeList(){
		global $mainframe;
		$mainframe->redirect("index.php?option=com_osservicesbooking&task=employee_list");
	}
	
	/**
	 * Remove rest day
	 *
	 * @param unknown_type $option
	 */
	static function removeRestDay($option){
		global $mainframe,$jinput;
		$rid = $jinput->getInt('rid',0);
		$db  = JFactory::getDbo();
		$db->setQuery("Select eid from #__app_sch_employee_rest_days where id = '$rid'");
		$eid = $db->loadResult();
		$db->setQuery("DELETE FROM #__app_sch_employee_rest_days WHERE id = '$rid'");
		$db->execute();		
		$db->setQuery("Select * from #__app_sch_employee_rest_days where eid = '$eid' order by rest_date");
		$rests = $db->loadObjectList();
		if(count($rests) > 0){
			?>
			<table width="80%" style="border:1px solid #CCC;">
				<tr>
					<td width="30%" style="text-align:center;font-weight:bold;border-bottom:1px solid #CCC;">
						<?php echo JText::_('OS_DATE')?>
					</td>
					<td width="20%" style="text-align:center;font-weight:bold;border-bottom:1px solid #CCC;">
						<?php echo JText::_('OS_REMOVE')?>
					</td>
				</tr>
				<?php
				for($i=0;$i<count($rests);$i++){
					$rest = $rests[$i];
					?>
					<tr>
						<td width="30%" align="left" style="padding-left:10px;">
							<?php
							$timestemp = strtotime($rest->rest_date);
							echo date("D, jS M Y",  $timestemp);
							$timestemp = strtotime($rest->rest_date_to);
							echo " - ";
							echo date("D, jS M Y",  $timestemp)
							?>
						</td>
						<td width="30%" align="center">
							<a href="javascript:removeBreakDate(<?php echo $rest->id?>)">
								<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-square-fill" viewBox="0 0 16 16">
								  <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm3.354 4.646L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 1 1 .708-.708z"/>
								</svg>
							</a>
						</td>
					</tr>
					<?php
				}
				?>
			</table>
			<?php
		}
		exit();
	}

    static function removeBusyTime($option)
    {
        global $mainframe,$jinput;
        $rid = $jinput->getInt('rid',0);
        $db  = JFactory::getDbo();
        $db->setQuery("Select eid from #__app_sch_employee_busy_time where id = '$rid'");
        $eid = $db->loadResult();
        $db->setQuery("DELETE FROM #__app_sch_employee_busy_time WHERE id = '$rid'");
        $db->execute();
        $db->setQuery("Select * from #__app_sch_employee_busy_time where eid = '$eid' order by busy_date, busy_from");
        $busy = $db->loadObjectList();
        if(count($busy) > 0){
            ?>
            <table width="80%" style="border:1px solid #CCC;">
                <tr>
                    <td width="30%" style="text-align:center;font-weight:bold;border-bottom:1px solid #CCC;">
                        <?php echo JText::_('OS_DATE')?>
                    </td>
                    <td width="20%" style="text-align:center;font-weight:bold;border-bottom:1px solid #CCC;">
                        <?php echo JText::_('OS_REMOVE')?>
                    </td>
                </tr>
                <?php
                for($i=0;$i<count($busy);$i++)
                {
                    $b = $busy[$i];
                    ?>
                    <tr>
                        <td width="30%" align="left" style="padding-left:10px;">
                            <?php
                            $timestemp = strtotime($b->busy_date);
                            echo date("D, jS M Y",  $timestemp);
                            echo " - ".JText::_('OS_FROM').": ".$b->busy_from.". ".JText::_('OS_TO').": ".$b->busy_to;
                            ?>
                        </td>
                        <td width="30%" align="center">
                            <a href="javascript:removeBusyTime(<?php echo $b->id?>)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-square-fill" viewBox="0 0 16 16">
								  <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm3.354 4.646L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 1 1 .708-.708z"/>
								</svg>
                            </a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <?php
        }
        exit();
    }
	
	/**
	 * agent list
	 *
	 * @param unknown_type $option
	 */
	static function employee_list($option)
	{
		global $mainframe,$mapClass;
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDBO();
		$lists = array();
		$condition = '';
		$add_query = '';
		$select_qurey = '';
		
		// filte sort
        $filter_order 				= $mainframe->getUserStateFromRequest($option.'.employee.filter_order','filter_order','a.ordering','string');
        $filter_order_Dir 			= $mainframe->getUserStateFromRequest($option.'.employee.filter_order_Dir','filter_order_Dir','asc','string');
        $lists['order'] 			= $filter_order;
        $lists['order_Dir'] 		= $filter_order_Dir;
        $order_by 					= " ORDER BY $filter_order $filter_order_Dir";
		
		// Get the pagination request variables
        $limit						= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
        $limitstart					= $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );
		
		// search 
        $keyword 					= $db->escape(trim($mainframe->getUserStateFromRequest($option.'.employee.keyword','keyword','','string')));
        $lists['keyword']  			= $keyword;
        if($keyword != "")
		{
            $condition 				.= " AND (";
            $condition 				.= " a.employee_name LIKE '%$keyword%'";
            $condition 				.= " OR 	a.employee_email LIKE '%$keyword%'";
            $condition 				.= " OR 	a.employee_notes LIKE '%$keyword%'";
            $condition 				.= " OR 	a.employee_phone LIKE '%$keyword%'";
            $condition 				.= " )";
        }
		// filter state
        $filter_state 				= $mainframe->getUserStateFromRequest($option.'.employee.filter_state','filter_state','','string');
        $lists['filter_state'] 		= JHtml::_('grid.state',$filter_state);
        $condition 					.= ($filter_state == 'P')? " AND `published` = 1":(($filter_state == 'U')? " AND `published` = 0":"");
			
		// filter service
        $filter_service 			= $mainframe->getUserStateFromRequest($option.'.employee.filter_service','filter_service',0,'int');
        $db->setQuery("SELECT id AS value, service_name AS text FROM #__app_sch_services ORDER BY service_name, ordering");
        $option_s 					= $db->loadObjectList();
        array_unshift($option_s,JHtml::_('select.option',0,JText::_('OS_FILTER_EMPLOYEE_FOR_SERVICE')));
        $lists['filter_service']	= JHtml::_('select.genericlist',$option_s,'filter_service','class="'.$mapClass['input-medium'].' form-select ilarge" onchange="this.form.submit();"','value','text',$filter_service,'filter_service',true);
        $lists['have_order'] 		= $filter_service;
        if ($filter_service)
		{
            $order_by 				= " ORDER BY $filter_order $filter_order_Dir, b.ordering";
            $add_query	 			= " INNER JOIN #__app_sch_employee_service AS b ON (a.id = b.employee_id AND b.service_id = '$filter_service')";
            $select_qurey 			= ", b.id AS eid, b.ordering ";
        }
		elseif ($filter_order == 'a.ordering')
		{
            $lists['order']			= 'a.ordering';
            $order_by 				= " ORDER BY a.ordering";
        }
			
		// get data	
        $count 						= " SELECT count(a.id) FROM #__app_sch_employee AS a "
                                        .$add_query
                                        ." WHERE 1=1 ".$condition;
        $db->setQuery($count);
        $total 						= $db->loadResult();
        jimport('joomla.html.pagination');
        $pageNav 					= new JPagination($total,$limitstart,$limit);
        $list  						= "SELECT a.* $select_qurey FROM #__app_sch_employee AS a "
                                        .$add_query
                                        ."\n WHERE 1=1 ";
        $list 						.= $condition;
        $list 						.= $order_by;
        $db->setQuery($list,$pageNav->limitstart,$pageNav->limit);
        $rows 						= $db->loadObjectList();

        foreach ($rows as $row) 
		{
            $serviceids = array();
            $db->setQuery("SELECT `service_name` FROM #__app_sch_services WHERE `id` IN (SELECT `service_id` FROM #__app_sch_employee_service WHERE `employee_id`=$row->id)");
            //$serviceids = $db->loadResultArray();
            $results = $db->loadObjectList();
            if(count($results) > 0)
			{
                for($i=0;$i<count($results);$i++)
				{
                    $serviceids[$i] = $results[$i]->service_name;
                }
            }
            $row->service_name 		= implode(', ',$serviceids);
        }
			
		HTML_OSappscheduleEmployee::employee_list($option,$rows,$pageNav,$lists);
	}
	
	/**
	 * publish or unpublish agent
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 * @param unknown_type $state
	 */
	static function employee_state($option,$cid,$state){
		global $mainframe;
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDBO();
		if(count($cid)>0)	{
			$cids = implode(",",$cid);
			$db->setQuery("UPDATE #__app_sch_employee SET `published` = '$state' WHERE id IN ($cids)");
			$db->execute();
		}
		$mainframe->enqueueMessage(JText::_("OS_ITEMS_STATUS_HAS_BEEN_CHANGED"),'message');
		OSappscheduleEmployee::employee_list($option);
	}
	
	/**
	 * remove agent
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 */
	static function employee_remove($option,$cid){
		global $mainframe,$configClass;
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDBO();
		if(count($cid)>0)	{
			for($i=0;$i<count($cid);$i++){
				$id = $cid[$i];
				$db->setQuery("Select user_id from #__app_sch_employee where id = '$id'");
				$user_id = $db->loadResult();
				//add employee to user group
				if(($configClass['employee_acl_group'] != "") and ($user_id > 0)){
					$db->setQuery("SELECT COUNT(user_id) FROM #__user_usergroup_map WHERE user_id = '$user_id' AND group_id = '".$configClass['employee_acl_group']."'");
					$count = $db->loadResult();
					if($count == 0){
						$db->setQuery("INSERT INTO #__user_usergroup_map (user_id,group_id) VALUES ('$user_id','".$configClass['employee_acl_group']."')");
						$db->execute();
					}
				}
			}
			$cids = implode(",",$cid);
			$db->setQuery("DELETE FROM #__app_sch_employee_service WHERE `employee_id` IN ($cids)");$db->execute();
			$db->setQuery("DELETE FROM #__app_sch_employee WHERE id IN ($cids)");$db->execute();
			$db->execute();
		}
		
		$mainframe->enqueueMessage(JText::_("OS_ITEMS_HAS_BEEN_DELETED"),'message');
		OSappscheduleEmployee::employee_list($option);
	}
	
	/**
	 * change order price group
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 * @param unknown_type $direction
	 */
	static function employee_order($option,$id,$direction){
		global $mainframe;
		$mainframe = JFactory::getApplication();
		$row = &JTable::getInstance('Employee','OsAppTable');
		$row->load($id);
		$row->move( $direction);
		$row->reorder();
		$mainframe->enqueueMessage(JText::_("OS_NEW_ORDERING_SAVED"),'message');
		OSappscheduleEmployee::employee_list($option);
	}
	
	/**
	 * save new order
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 */
	static function employee_saveorder($option,$cid){
		global $mainframe,$jinput;
		$mainframe = JFactory::getApplication();
		$msg = JText::_("OS_NEW_ORDERING_SAVED");
		$order      = $jinput->get('order',array(),'ARRAY');
		\Joomla\Utilities\ArrayHelper::toInteger($order);
		$row = &JTable::getInstance('Employee','OsAppTable');
		
		// update ordering values
		for( $i=0; $i < count($cid); $i++ )
		{
			$row->load( (int) $cid[$i] );
			if ($row->ordering != $order[$i]){
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$msg = JText::_("OS_ERROR_SAVING_ORDERING");
					break;
				}
			}
		}
		// execute updateOrder
		$row->reorder();
		$mainframe->enqueueMessage($msg,'message');
		OSappscheduleEmployee::employee_list($option);
	}
	
	
	/**
	 * Service modify
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	static function employee_modify($option,$id){
		global $mainframe,$mapClass;
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDbo();
		$row = &JTable::getInstance('Employee','OsAppTable');
		if($id > 0){
			$row->load((int)$id);
		}else{
			$row->published = 1;
			$row->employee_before=0;
			$row->employee_after=0;
			$row->employee_total=0;
		}
		
		// creat published
		$lists['published'] = JHtml::_('select.booleanlist','published','class="'.$mapClass['input-medium'].' form-select"',$row->published);
			
		// creat service
		//$lists['service_id'] = ServiceCheckbox::create_checkbox($row->id);
		$db->setQuery("Select * from #__app_sch_services");
		$services = $db->loadObjectList();
		
		$lists['hours'] = OSappscheduleEmployee::generateHours();

		$optionArr = array();
		$optionArr[] = JHtml::_('select.option','0',JText::_('All dates'));
		$optionArr[] = JHtml::_('select.option','1',JText::_('OS_MON'));
		$optionArr[] = JHtml::_('select.option','2',JText::_('OS_TUE'));
		$optionArr[] = JHtml::_('select.option','3',JText::_('OS_WED'));
		$optionArr[] = JHtml::_('select.option','4',JText::_('OS_THU'));
		$optionArr[] = JHtml::_('select.option','5',JText::_('OS_FRI'));
		$optionArr[] = JHtml::_('select.option','6',JText::_('OS_SAT'));
		$optionArr[] = JHtml::_('select.option','7',JText::_('OS_SUN'));
		$lists['week_day'] = $optionArr;
		
		if($id > 0){
			$db->setQuery("Select * from #__app_sch_employee_extra_cost where eid = '$id'");
			$extra_costs = $db->loadObjectList();
			$lists['extra'] = $extra_costs;
		}else{
			$lists['extra']	= array();
		}
		
		$db->setQuery("Select * from #__app_sch_employee_rest_days where eid = '$id' order by rest_date");
		$rests = $db->loadObjectList();

        $db->setQuery("Select * from #__app_sch_employee_busy_time where eid = '$id' order by busy_date, busy_from");
        $busy = $db->loadObjectList();
		
		HTML_OSappscheduleEmployee::employee_modify($option,$row,$lists,$rests,$services,$busy);
	}
	
	static function getUserInput($user_id,$employee_id = 0)
	{
		if (version_compare(JVERSION, '3.5', 'le'))
		{
			// Initialize variables.
			$html = array();
			$link = 'index.php?option=com_users&amp;view=users&amp;layout=modal&amp;tmpl=component&amp;field=user_id';

			// Initialize some field attributes.
			$attr = ' class="inputbox"';

			// Initialize JavaScript field attributes.
			if (version_compare(JVERSION, '3.0', 'le')){
				// Load the modal behavior script.
				JHtml::_('behavior.modal', 'a.modal_user_id');
			}else{
				JHtml::_('behavior.modal', 'a.modal');
			}

			// Build the script.
			$script = array();
			$script[] = '	static function jSelectUser_user_id(id, title) {';
			$script[] = '		var old_id = document.getElementById("user_id").value;';
			$script[] = '		if (old_id != id) {';
			$script[] = '			document.getElementById("user_id").value = id;';
			$script[] = '			document.getElementById("user_id_id").value = id;';
			$script[] = '			document.getElementById("employee_name").value = title;';
			$script[] = '			populateUserData();';
			$script[] = '			' . $onchange;
			$script[] = '		}';
			$script[] = '		SqueezeBox.close();';
			$script[] = '	}';

			// Add the script to the document head.
			JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

			// Load the current username if available.
			$table = JTable::getInstance('user');
			
			if ($user_id)
			{
				$table->load($user_id);
			}
			else
			{
				$table->username = JText::_('OS_SELECT_AGENT');
			}

			// Create a dummy text field with the user name.
			
			$html[] = '<div class="fltlft">';
			if (version_compare(JVERSION, '3.0', 'le')){
				$html[] = '	<input type="text" id="user_id_name"' . ' value="' . htmlspecialchars($table->name, ENT_COMPAT, 'UTF-8') . '"'
					. ' disabled="disabled"' . $attr . ' />';
				$html[] = '</div>';
				// Create the user select button.
				$html[] = '<div class="button2-left">';
				$html[] = '  <div class="blank">';
				$html[] = '		<a class="modal_user_id" title="' . JText::_('JLIB_FORM_CHANGE_USER') . '"' . ' href="' . $link . '"'
					. ' rel="{handler: \'iframe\', size: {x: 800, y: 500}}">';
				$html[] = '			' . JText::_('JLIB_FORM_CHANGE_USER') . '</a>';
				$html[] = '  </div>';
				$html[] = '</div>';
			}else{
				$html[] = '<span class="input-append">';
				$html[] = '<input type="text" class="input-medium" id="user_id_name" value="'.htmlspecialchars($table->name, ENT_COMPAT, 'UTF-8') .'" disabled="disabled" size="35" /><a class="modal btn" title="'.JText::_('JLIB_FORM_CHANGE_USER').'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 800, y: 450}}"><i class="icon-file"></i> '.JText::_('JLIB_FORM_CHANGE_USER').'</a>';
				$html[] = '</span>';
			}

			// Create the real field, hidden, that stored the user id.
			$html[] = '<input type="hidden" id="user_id" name="user_id" value="'.$user_id.'" />';
			$html[] = '<input type="hidden" id="user_id_id" name="user_id_id" value="'.$user_id.'" />';

			return implode("\n", $html);
		}
		else
		{
			$field = JFormHelper::loadFieldType('User');
			$element = new SimpleXMLElement('<field />');
			$element->addAttribute('name', 'user_id');
			$element->addAttribute('class', 'readonly');
			//if ($employee_id == 0)
			//{
				$element->addAttribute('onchange', 'populateUserData();');
			//}
			$field->setup($element, $user_id);

			return $field->input;
		}
	}
	
	/**
	 * Generate hours
	 *
	 */
	static function generateHours()
	{
		$start = 0;
		$end = 24;
		$returnArr = array();

		$tmp = new \stdClass();
		$tmp->value = "";
		$tmp->text = "";
		$returnArr[0] = $tmp;
		for($i=$start;$i<=$end;$i++){
			for($j=0;$j<4;$j++){
				if($i<10){
					$time = "0".$i;
				}else{
					$time = $i;
				}
				$time .= ":";
				if($j<10){
					$time .= "0".$j;
				}else{
					$time .= $j;
				}
				$j += 15;
				
				$count = count($returnArr);
				$tmp = new \stdClass();
				$tmp->value = $time;
				$tmp->text = $time;
				$returnArr[$count] = $tmp;
			}
		}
		return $returnArr;
	}
	
	/**
	 * Generate hours
	 *
	 */
	static function generateHoursIncludeSecond(){
		$start = 0;
		$end = 23;
		$returnArr = array();
		$tmp = new \stdClass();
		$tmp->value = "";
		$tmp->text = "";
		$returnArr[0] = $tmp;
		for($i=$start;$i<=$end;$i++){
			for($j=0;$j<60;$j++){
				if($i<10){
					$time = "0".$i;
				}else{
					$time = $i;
				}
				$time .= ":";
				if($j<10){
					$time .= "0".$j;
				}else{
					$time .= $j;
				}
				$j += 4;
				
				$count = count($returnArr);
				$tmp = new \stdClass();
				$tmp->value = $time.":00";
				$tmp->text = $time.":00";
				$returnArr[$count] = $tmp;
			}
		}
		return $returnArr;
	}
	
	/**
	 * save service
	 *
	 * @param unknown_type $option
	 */
	static function employee_save($option,$save)
	{
		global $mainframe,$configClass,$jinput;
		$mainframe	= JFactory::getApplication();
		$db			= JFactory::getDbo();
		$user_id	= $jinput->getInt('user_id',0);
		$id			= $jinput->getInt('id',0);
		if($user_id > 0)
		{
			$id		= $jinput->getInt('id',0);
			if($id == 0)
			{
				$db->setQuery("UPDATE #__app_sch_employee SET user_id = '0' where user_id = '$user_id'");
				$db->execute();
			}
			else
			{
				$db->setQuery("UPDATE #__app_sch_employee SET user_id = '0' where user_id = '$user_id' AND id <> '$id'");
				$db->execute();
			}
		}
		
		$post	= $jinput->post->getArray();
		$row	= &JTable::getInstance('Employee','OsAppTable');
		$row->bind($post);
		$row->employee_notes		= $_POST['employee_notes'];
		$row->employee_name			= str_replace("-","", $row->employee_name);
		$row->employee_name			= str_replace("|","", $row->employee_name);
		$row->employee_name			= str_replace("&","", $row->employee_name);
		$row->employee_send_email = $jinput->getInt('employee_send_email',0);
		$remove_image = $jinput->getInt('remove_image',0);

		if(is_uploaded_file($_FILES['image']['tmp_name']))
		{
			$photo_name = time()."_".str_replace(" ","_",$_FILES['image']['name']);
			move_uploaded_file($_FILES['image']['tmp_name'],JPATH_ROOT."/images/osservicesbooking/employee/".$photo_name);
			$row->employee_photo = $photo_name;
		}
		elseif($remove_image == 1)
		{
			$row->employee_photo = "";
		}
		$row->check();
		$msg = JText::_('OS_ITEM_HAS_BEEN_SAVED'); 

	 	if (!$row->store())
		{
		 	//$msg = JText::_('OS_ERROR_SAVING'). " - ".$row->getError() ;	 	
			throw new Exception ($row->getError());
		}

		if($id == 0)
		{
			$id = $db->insertID();
		}

		// save employee service
		for($i=1;$i<=5;$i++)
		{
			$rest_day = $jinput->get('date'.$i,'','string');
			$rest_day_to = $jinput->get('date_to_'.$i,'','string');
			if($rest_day != "" && $rest_day_to != "")
			{
				$db->setQuery("INSERT INTO #__app_sch_employee_rest_days (id,eid,rest_date,rest_date_to) VALUES (NULL,'$id','$rest_day','$rest_day_to')");
				$db->execute();
			}

			$busy_date = $jinput->get('busy_date'.$i,'','string');
			$busy_from = $jinput->get('busy_from'.$i,'','string');
            $busy_to = $jinput->get('busy_to'.$i,'','string');
            if($busy_date != '' && $busy_from != '' && $busy_to != '')
            {
                $db->setQuery("INSERT INTO #__app_sch_employee_busy_time (id,eid,busy_date,busy_from,busy_to) VALUES (NULL,'$id','$busy_date','$busy_from','$busy_to')");
                $db->execute();
            }
		}
		
		//add employee to user group
		if($configClass['employee_acl_group'] != ""){
			$db->setQuery("SELECT COUNT(user_id) FROM #__user_usergroup_map WHERE user_id = '$user_id' AND group_id = '".$configClass['employee_acl_group']."'");
			$count = $db->loadResult();
			if($count == 0){
				$db->setQuery("INSERT INTO #__user_usergroup_map (user_id,group_id) VALUES ('$user_id','".$configClass['employee_acl_group']."')");
				$db->execute();
			}
		}
		
		//save the additional cost
		$db->setQuery("Delete from #__app_sch_employee_extra_cost where eid = '$id'");
		$db->execute();
		for($i=0;$i<=15;$i++){
			$start_time = $jinput->get('start_time'.$i,'','string');
			$end_time   = $jinput->get('end_time'.$i,'','string');
			$extra_cost = $jinput->get('extra_cost'.$i,'','string');
			$week_day   = $jinput->get('week_day'.$i,'','string');
			if(($start_time != "") and ($end_time != "") and ($extra_cost != "")){
				$db->setQuery("Insert into #__app_sch_employee_extra_cost (id,eid,start_time,end_time,extra_cost,week_date) values (NULL,'$id','$start_time','$end_time','$extra_cost','$week_day')");
				$db->execute();
			}
		}
		
		if($save)
		{
			$mainframe->enqueueMessage($msg);
			$mainframe->redirect("index.php?option=com_osservicesbooking&task=employee_list");
		}
		else
		{
			$mainframe->enqueueMessage($msg);
			$mainframe->redirect("index.php?option=com_osservicesbooking&task=employee_edit&cid[]=".$id);
		}
	}
	
	/**
	 * Show availability of one calendar
	 *
	 * @param unknown_type $option
	 */
	static function availCalendar($option){
		global $mainframe,$jinput;
		$db = JFactory::getDbo();
		$eid = $jinput->getInt('eid',0);
		$employee = Jtable::getInstance('Employee','OsAppTable');
		$employee->load((int)$eid);
		HTML_OSappscheduleEmployee::calendarManage($employee);
	}
	
	/**
	 * Setup break-time
	 *
	 * @param unknown_type $option
	 */
	static function setupBreaktime($option){
		global $mainframe,$jinput;
		$db  = JFactory::getDbo();
		$sid = $jinput->getInt('sid',0);
		$eid = $jinput->getInt('eid',0);
		$db->setQuery("Select * from #__app_sch_employee where id = '$eid'");
		$employee = $db->loadObject();
		$db->setQuery("Select * from #__app_sch_services where id = '$sid'");
		$service = $db->loadObject();
		$lists['services'] = ServiceCheckbox::checkingBreaktime($sid,$eid);
		$db->setQuery("Select * from #__app_sch_custom_breaktime where eid = '$eid' and sid = '$sid' order by bdate,bstart,bend");
		$customs = $db->loadObjectList();
		
		HTML_OSappscheduleEmployee::breaktimeForm($service,$employee,$lists,$customs);
	}
	
	static function gotoemployeeedit(){
		global $mainframe,$jinput;
		$eid = $jinput->getInt('eid',0);
		$mainframe->redirect("index.php?option=com_osservicesbooking&task=employee_edit&cid[]=".$eid);
	}
	
	static function saveBreakTime($save)
	{
		global $mainframe,$jinput;
		$db = JFactory::getDbo();
		$serviceid = $jinput->getInt('sid',0);
		// save employee service
		$db = JFactory::getDbo();
		$employee_id = $jinput->getInt('eid',0);
		if ($employee_id){
			$db->setQuery("DELETE FROM #__app_sch_employee_service WHERE `employee_id` = '$employee_id' and service_id = '$serviceid'");
			$db->execute();
		}
		if ($employee_id)
		{
			$row = &JTable::getInstance('Empser','OsAppTable');
			$row->employee_id = $employee_id;
			$serviceids = $jinput->get('service_id',array(),'array');//JRequest::getVar('service_id',array(),'default','array');
			foreach ($serviceids as $serviceid) {
				$row->id = null;
				$row->service_id = $serviceid;
				$additional_cost = $jinput->get('add_'.$serviceid,0,'string');
				$row->additional_price = (float)$additional_cost;
				$venue = $jinput->getInt('vid_'.$serviceid,0);
				$row->vid = $venue;
				$row->mo = $jinput->getInt('mo_'.$serviceid,0);
				$row->tu = $jinput->getInt('tu_'.$serviceid,0);
				$row->we = $jinput->getInt('we_'.$serviceid,0);
				$row->th = $jinput->getInt('th_'.$serviceid,0);
				$row->fr = $jinput->getInt('fr_'.$serviceid,0);
				$row->sa = $jinput->getInt('sa_'.$serviceid,0);
				$row->su = $jinput->getInt('su_'.$serviceid,0);
				$row->ordering = $row->getNextOrder(" `service_id` = '$serviceid'");
				if(!$row->store())
				{
					throw new Exception($row->getError(), 500);
				}
				$row->reorder(" `service_id` = '$serviceid'");
								
				$db->setQuery("Delete from #__app_sch_employee_service_breaktime where eid = '$employee_id' and sid = '$serviceid'");
				$db->execute();
				for($i=1;$i<=7;$i++){
					for($j=0;$j<4;$j++){
						$startname  = "start_from".$serviceid.$j."_".$i;
						$endname    = "end_to".$serviceid.$j."_".$i;
						$start_from = $jinput->get($startname,'','string');
						$end_to		= $jinput->get($endname,'','string');
						if(($start_from != "") and ($end_to != "")){
							$db->setQuery("Insert into #__app_sch_employee_service_breaktime (id,sid,eid,date_in_week,break_from,break_to) values (NULL,'$serviceid','$employee_id','$i','$start_from','$end_to')");
							$db->execute();
						}
					}
				}
			}
		}
		
		$db->setQuery("DELETE FROM #__app_sch_employee_extra_cost WHERE eid = '$employee_id'");
		$db->execute();
		for($i=0;$i<10;$i++){
			$start_time      = $jinput->get("start_time".$i,"","string");
			$end_time        = $jinput->get("end_time".$i,"","string");
			$extra_cost		 = $jinput->get("extra_cost".$i,"","string");
			if(($start_time != "") and ($end_time != "") and ($extra_cost != "")){
				$db->setQuery("INSERT INTO #__app_sch_employee_extra_cost (id, eid, start_time, end_time, extra_cost) VALUES (NULL,'$employee_id','$start_time','$end_time',$extra_cost)");
				$db->execute();
			}
		}
		
		if($save == 1)
		{
			$mainframe->enqueueMessage(JText::_('OS_WORKING_TIME_HAVE_BEEN_SAVED'));
			$mainframe->redirect("index.php?option=com_osservicesbooking&task=employee_edit&cid[]=".$employee_id);
		}
		else 
		{
			$mainframe->enqueueMessage(JText::_('OS_WORKING_TIME_HAVE_BEEN_SAVED'));
			$mainframe->redirect("index.php?option=com_osservicesbooking&task=employee_setupbreaktime&eid=".$employee_id."&sid=".$serviceid);
		}
	}
	
	static function saveCustomerBreakTime(){
		global $jinput;
		$db			= JFactory::getDbo();
		$eid		= $jinput->getInt('eid',0);
		$sid		= $jinput->getInt('sid',0);
		$bdate		= $jinput->get('bdate','','string');
		$bstart		= $jinput->get('bstart','','string');
		$bend		= $jinput->get('bend','','string');
		$db->setQuery("Insert into #__app_sch_custom_breaktime (id,eid,sid,bdate,bstart,bend) values (NULL,'$eid','$sid','$bdate','$bstart','$bend')");
		$db->execute();
		
		self::getCustomBreakTime($eid,$sid);
		exit();
	}
	
	static function removeCustomerBreakTime(){
		global $jinput;
		$db = JFactory::getDbo();
		$eid = $jinput->getInt('eid',0);
		$sid = $jinput->getInt('sid',0);
		$id = $jinput->getInt('id',0);
		$db->setQuery("Delete from #__app_sch_custom_breaktime where id = '$id'");
		$db->execute();
		
		self::getCustomBreakTime($eid,$sid);
		exit();
	}
	
	public static function getCustomBreakTime($eid,$sid){
		$db = JFactory::getDbo();
		$db->setQuery("Select * from #__app_sch_custom_breaktime where eid = '$eid' and sid = '$sid' order by bdate,bstart,bend");
		$customs = $db->loadObjectList();
		
		if(count($customs) > 0){
			?>
			<table width="80%" style="border:1px solid #CCC;">
				<tr>
					<td width="30%" class="headerajaxtd">
						<?php echo JText::_('OS_DATE')?>
					</td>
					<td width="20%" class="headerajaxtd">
						<?php echo JText::_('OS_REMOVE')?>
					</td>
				</tr>
				<?php
				for($i=0;$i<count($customs);$i++){
					$rest = $customs[$i];
					?>
					<tr>
						<td width="30%" align="left" style="text-align:center;">
							<?php
							$timestemp = strtotime($rest->bdate);
							echo date("D, jS M Y",  $timestemp);
							echo "&nbsp;&nbsp;";
							echo $rest->bstart." - ".$rest->bend;
							?>
						</td>
						<td width="30%" align="center">
							<a href="javascript:removeCustomBreakDate(<?php echo $rest->id?>,'<?php echo JUri::root();?>')">
								<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-square-fill" viewBox="0 0 16 16">
								  <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm3.354 4.646L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 1 1 .708-.708z"/>
								</svg>
							</a>
						</td>
					</tr>
					<?php
				}
				?>
			</table>
			<?php
			echo "<BR /><BR />";
		}
	}
	
	/**
	 * Duplicate Employee information
	 *
	 * @param unknown_type $id
	 */
	public static function duplicateEmployee($id){
		global $mainframe;
		$db = JFactory::getDbo();
		$db->setQuery("Select * from #__app_sch_employee where id = '$id'");
		$employee = $db->loadObject();
		$row = &JTable::getInstance('Employee','OsAppTable');
		$row->id = 0;
		$row->user_id = 0;
		$row->employee_name = JText::_('OS_COPIED')." ".$employee->employee_name;
		$row->employee_email = $employee->employee_email;
		$row->employee_send_email = $employee->employee_send_email;
		$row->employee_phone = $employee->employee_phone;
		$row->employee_notes = $employee->employee_notes;
		$row->employee_photo = $employee->employee_photo;
		$row->gusername = $employee->gusername;
		$row->gcalendarid = $employee->gcalendarid;
		$row->gpassword = $employee->gpassword;
		$row->client_id = $employee->client_id;
		$row->app_name = $employee->app_name;
		$row->app_email_address = $employee->app_email_address;
		$row->p12_key_filename = $employee->p12_key_filename;
		$row->published = 0;
		$row->store();
		$eid = $db->insertid();
		
		#__app_sch_employee_extra_cost
		$db->setQuery("Select * from #__app_sch_employee_extra_cost where eid = '$id'");
		$extra_costs = $db->loadObjectList();
		
		if(count($extra_costs) > 0){
			foreach ($extra_costs as $extra_cost){
				$db->setQuery("Insert into #__app_sch_employee_extra_cost (id,eid,start_time,end_time,extra_cost) values (NULL,'$eid','$extra_cost->start_time','$extra_cost->end_time','$extra_cost->extra_cost')");
				$db->execute();
			}
		}
		
		#__app_sch_employee_rest_days
		$db->setQuery("Select * from #__app_sch_employee_rest_days where eid = '$id'");
		$rests = $db->loadObjectList();
		
		if(count($rests) > 0){
			foreach ($rests as $rest){
				$db->setQuery("Insert into #__app_sch_employee_rest_days (id,eid,rest_date,rest_date_to) values (NULL,'$eid','$rest->rest_date','$rest->rest_date_to')");
				$db->execute();
			}
		}
		
		#__app_sch_employee_service
		$db->setQuery("Select * from #__app_sch_employee_service where employee_id = '$id'");
		$employee_services = $db->loadObjectList();
		
		if(count($employee_services) > 0){
			foreach ($employee_services as $employee_service){
				$db->setQuery("Insert into #__app_sch_employee_service (id,employee_id,service_id,vid,ordering,additional_price,mo,tu,we,th,fr,sa,su) values (NULL,'$eid','$employee_service->service_id','$employee_service->vid','$employee_service->ordering','$employee_service->additional_price','$employee_service->mo','$employee_service->tu','$employee_service->we','$employee_service->th','$employee_service->fr','$employee_service->sa','$employee_service->su')");
				$db->execute();
			}
		}
		
		#__app_sch_employee_service_breaktime
		$db->setQuery("Select * from #__app_sch_employee_service_breaktime where eid = '$id'");
		$breaktimes = $db->loadObjectList();
		
		if(count($breaktimes) > 0){
			foreach ($breaktimes as $breaktime){
				$db->setQuery("Insert into #__app_sch_employee_service_breaktime (id,eid,sid,date_in_week,break_from,break_to) values (NULL,'$eid','$breaktime->sid','$breaktime->date_in_week','$breaktime->break_from','$breaktime->break_to')");
				$db->execute();
			}
		}
		
		$mainframe->enqueueMessage(JText::_('OS_EMPLOYEE_HAS_BEEN_DUPLICATED'));
		$mainframe->redirect("index.php?option=com_osservicesbooking&task=employee_list");
	}
}
?>