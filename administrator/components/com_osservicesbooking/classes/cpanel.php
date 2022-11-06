<?php
/*------------------------------------------------------------------------
# cpanel.php - Ossolution Services Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2022 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die;

class OSappscheduleCpanel
{
	/**
	 * Default static function
	 *
	 * @param unknown_type $option
	 */
	static function display($option,$task)
	{
		switch ($task)
		{
			case "cpanel_optimizedatabase":
				OSappscheduleCpanel::optimizeDatabase();
			break;
			case "cpanel_revenue":
				OSappscheduleCpanel::revenue();
			break;
			case "cpanel_services":
				OSappscheduleCpanel::serviceGraph();
			break;
			case "cpanel_list":
			default:
				OSappscheduleCpanel::cpanel_list($option);
			break;
			case "cpanel_updaterestdays":
				OSappscheduleCpanel::updaterestdays();
			break;
		}
	}

	static function updaterestdays()
	{
		$db = JFactory::getDbo();
		$db->setQuery("Select * from #__app_sch_employee");
		$employees = $db->loadObjectList();
		foreach($employees as $employee)
		{
			for($j = 2022; $j <= 2025; $j++)
			{
				$first_date = $j."-01-01";
				$first_date_int = strtotime($first_date);
				for($i = 0; $i<365; $i++)
				{
					$date = $first_date_int + 24*3600*$i;
					$date = date("Y-m-d", $date);
					$db->setQuery("Select count(id) from #__app_sch_employee_rest_days where eid = '$employee->id' and rest_date = '$date' and rest_date_to = '$date'");
					$count = $db->loadResult();
					if($count == 0)
					{
						$db->setQuery("Insert into #__app_sch_employee_rest_days (id, eid, rest_date, rest_date_to) values (NULL, '$employee->id','$date','$date')");
						$db->execute();
					}
				}
			}
		}
	}

	static function revenue()
	{
		global $jinput;

		$data = [];

		$db = JFactory::getDbo();
		$config = JFactory::getConfig();
		
		$range_option = $jinput->getInt('range_option', 0 );

		switch ($range_option)
		{
			case '0':
				//12 months
				$current_month = date("n");
				$current_year  = date("Y");
				for($i = 11; $i >= 0; $i--)
				{
				
					$date = JFactory::getDate('first day of now -'.$i.' month', $config->get('offset'));
					$date->setTime(0, 0, 0);
					$date->setTimezone(new DateTimeZone('UCT'));
					$fromDate = $date->toSql(true);
					//echo $fromDate;
					//echo "<BR />";
					$date     = JFactory::getDate('last day of now -'.$i.' month', $config->get('offset'));
					$date->setTime(23, 59, 59);
					$date->setTimezone(new DateTimeZone('UCT'));
					$toDate = $date->toSql(true);

					$db->setQuery("Select count(id) as orders, sum(order_final_cost) as income from #__app_sch_orders where order_status = 'S' and order_date >= ".$db->quote($fromDate) . " and order_date <= ".$db->quote($toDate));
					$row = $db->loadObject();

					$tmp = new stdClass();
					$tmp->month = $date->format('F Y');
					$tmp->orders = (int)$row->orders;
					$tmp->income = (float)$row->income;

					$data[] = $tmp;
				}
			break;
			case '1':
				//9 months
				$current_month = date("n");
				$current_year  = date("Y");
				for($i = 8; $i >= 0; $i--)
				{
					$date = JFactory::getDate('first day of now -'.$i.' month', $config->get('offset'));
					$date->setTime(0, 0, 0);
					$date->setTimezone(new DateTimeZone('UCT'));
					$fromDate = $date->toSql(true);
					//echo $fromDate;
					//echo "<BR />";
					$date     = JFactory::getDate('last day of now -'.$i.' month', $config->get('offset'));
					$date->setTime(23, 59, 59);
					$date->setTimezone(new DateTimeZone('UCT'));
					$toDate = $date->toSql(true);

					$db->setQuery("Select count(id) as orders, sum(order_final_cost) as income from #__app_sch_orders where order_status = 'S' and order_date >= ".$db->quote($fromDate) . " and order_date <= ".$db->quote($toDate));
					$row = $db->loadObject();

					$tmp = new stdClass();
					$tmp->month = $date->format('F Y');
					$tmp->orders = (int)$row->orders;
					$tmp->income = (float)$row->income;

					$data[] = $tmp;
				}
			break;
			case '2':
				//6 months
				$current_month = date("n");
				$current_year  = date("Y");
				for($i = 5; $i >= 0; $i--)
				{
				
					$date = JFactory::getDate('first day of now -'.$i.' month', $config->get('offset'));
					$date->setTime(0, 0, 0);
					$date->setTimezone(new DateTimeZone('UCT'));
					$fromDate = $date->toSql(true);
					//echo $fromDate;
					//echo "<BR />";
					$date     = JFactory::getDate('last day of now -'.$i.' month', $config->get('offset'));
					$date->setTime(23, 59, 59);
					$date->setTimezone(new DateTimeZone('UCT'));
					$toDate = $date->toSql(true);

					$db->setQuery("Select count(id) as orders, sum(order_final_cost) as income from #__app_sch_orders where order_status = 'S' and order_date >= ".$db->quote($fromDate) . " and order_date <= ".$db->quote($toDate));
					$row = $db->loadObject();

					$tmp = new stdClass();
					$tmp->month = $date->format('F Y');
					$tmp->orders = (int)$row->orders;
					$tmp->income = (float)$row->income;

					$data[] = $tmp;
				}
			break;
			case '3':
				//3 months
				$current_month = date("n");
				$current_year  = date("Y");
				for($i = 2; $i >= 0; $i--)
				{
				
					$date = JFactory::getDate('first day of now -'.$i.' month', $config->get('offset'));
					$date->setTime(0, 0, 0);
					$date->setTimezone(new DateTimeZone('UCT'));
					$fromDate = $date->toSql(true);
					//echo $fromDate;
					//echo "<BR />";
					$date     = JFactory::getDate('last day of now -'.$i.' month', $config->get('offset'));
					$date->setTime(23, 59, 59);
					$date->setTimezone(new DateTimeZone('UCT'));
					$toDate = $date->toSql(true);

					$db->setQuery("Select count(id) as orders, sum(order_final_cost) as income from #__app_sch_orders where order_status = 'S' and order_date >= ".$db->quote($fromDate) . " and order_date <= ".$db->quote($toDate));
					$row = $db->loadObject();

					$tmp = new stdClass();
					$tmp->month = $date->format('F Y');
					$tmp->orders = (int)$row->orders;
					$tmp->income = (float)$row->income;

					$data[] = $tmp;
				}
			break;
			case '4':
				$current_month = date("n");
				$current_year  = date("Y");
				$date1 = JFactory::getDate('first day of last month', $config->get('offset'));
				$first_date = $date1->format('j');
				$date2 = JFactory::getDate('last day of last month', $config->get('offset'));
				$last_date = $date2->format('j');

				$lastMonth = $date1->format('m');
				$lastYear  = $date1->format('Y');

				for($i = $first_date; $i <= $last_date; $i++)
				{
				
					$date = JFactory::getDate($i.'-'.$lastMonth.'-'.$lastYear, $config->get('offset'));
					$date->setTime(0, 0, 0);
					$date->setTimezone(new DateTimeZone('UCT'));
					$fromDate = $date->toSql(true);
					$date->setTime(23, 59, 59);
					$toDate = $date->toSql(true);

					$db->setQuery("Select count(id) as orders, sum(order_final_cost) as income from #__app_sch_orders where order_status = 'S' and order_date >= ".$db->quote($fromDate) . " and order_date <= ".$db->quote($toDate));
					$row = $db->loadObject();

					$tmp = new stdClass();
					$tmp->month = $date->format('jS F Y');
					$tmp->orders = (int)$row->orders;
					$tmp->income = (float)$row->income;

					$data[] = $tmp;
				}
			break;
			case '5':
				$current_month = date("n");
				$current_year  = date("Y");
				$date = JFactory::getDate('now', $config->get('offset'));
				$currentDate = $date->format('j');
				$currentMonth = $date->format('m');
				$currentYear  = $date->format('Y');

				for($i = 1; $i <= $currentMonth; $i++)
				{
					$date = JFactory::getDate($i.'-'.$currentMonth.'-'.$currentYear, $config->get('offset'));
					$date->setTime(0, 0, 0);
					$date->setTimezone(new DateTimeZone('UCT'));
					$fromDate = $date->toSql(true);
					$date->setTime(23, 59, 59);
					$toDate = $date->toSql(true);

					$db->setQuery("Select count(id) as orders, sum(order_final_cost) as income from #__app_sch_orders where order_status = 'S' and order_date >= ".$db->quote($fromDate) . " and order_date <= ".$db->quote($toDate));
					$row = $db->loadObject();

					$tmp = new stdClass();
					$tmp->month = $date->format('jS F Y');
					$tmp->orders = (int)$row->orders;
					$tmp->income = (float)$row->income;

					$data[] = $tmp;
				}
			break;
			
		}

		echo json_encode($data);
		JFactory::getApplication()->close();
	}

	static function serviceGraph()
	{
		global $mainframe,$configClass;
		HTML_OSappscheduleCpanel::serviceGraph();
	}
	
	/**
	 * Zend lib checking
	 *
	 */
	static function zendChecking(){
		global $mainframe,$configClass;
		jimport('joomla.filesystem.folder');
		$error = "";
		if($configClass['integrate_gcalendar'] == 1){
			if(!JFolder::exists(JPATH_ROOT."/libraries/osgcalendar"))
			{
				$error = "Please install Google API library.";
			}
		}
		
		if($error != ""){
			?>
			<div class="row-fluid">
				<div class="span12 label label-important" style="padding-top:5px;">
					<?php echo $error?>
				</div>
			</div>
			<?php
		}
	}
	
	/**
	 * Database optimization
	 *
	 */
	static function optimizeDatabase(){
		global $mainframe;
		$db = JFactory::getDbo();
		$db->setQuery("ALTER TABLE `#__app_sch_services` CHANGE `max_seats` `max_seats` TINYINT(3) NOT NULL DEFAULT '0'; ");
		$db->execute();
		
		$db->setQuery("ALTER TABLE `#__app_sch_services` CHANGE `early_bird_type` `early_bird_type` tinyint(1) NOT NULL DEFAULT '0'; ");
		$db->execute();

		$db->setQuery("ALTER TABLE `#__app_sch_services` CHANGE `early_bird_amount` `early_bird_amount` decimal(5,2) NOT NULL DEFAULT '0'; ");
		$db->execute();

		$db->setQuery("ALTER TABLE `#__app_sch_services` CHANGE `early_bird_days` `early_bird_days` tinyint(3) NOT NULL DEFAULT '0'; ");
		$db->execute();

		$db->setQuery("ALTER TABLE `#__app_sch_services` CHANGE `discount_amount` `discount_amount` decimal(5,2) NOT NULL DEFAULT '0'; ");
		$db->execute();

		$db->setQuery("ALTER TABLE `#__app_sch_services` CHANGE `payment_plugins` `payment_plugins` varchar(50) NOT NULL DEFAULT ''; ");
		$db->execute();

		$db->setQuery("ALTER TABLE `#__app_sch_services` CHANGE `paypal_id` `paypal_id` varchar(100) NOT NULL DEFAULT ''; ");
		$db->execute();

		$db->setQuery("ALTER TABLE `#__app_sch_services` CHANGE `authorize_api_login` `authorize_api_login` varchar(100) NOT NULL DEFAULT ''; ");
		$db->execute();

		$db->setQuery("ALTER TABLE `#__app_sch_services` CHANGE `authorize_transaction_key` `authorize_transaction_key` varchar(100) NOT NULL  DEFAULT ''");
		$db->execute();

		$db->setQuery("ALTER TABLE `#__app_sch_services` CHANGE `service_color` `service_color` varchar(255) NOT NULL DEFAULT ''");
		$db->execute();

		$db->setQuery("ALTER TABLE `#__app_sch_venues` CHANGE `image` `image` VARCHAR(100) NOT NULL DEFAULT '', CHANGE `number_hour_before` `number_hour_before` INT(11) NOT NULL DEFAULT '0'; ");
		$db->execute();

		$db->setQuery("ALTER TABLE `#__app_sch_categories` CHANGE `category_photo` `category_photo` VARCHAR(255) NOT NULL DEFAULT ''; ");
		$db->execute();

		$db->setQuery("ALTER TABLE `#__app_sch_orders` CHANGE `order_discount` `order_discount` DECIMAL(12,2) NOT NULL DEFAULT '0', CHANGE `order_lang` `order_lang` VARCHAR(20)  NOT NULL DEFAULT '', CHANGE `order_card_number` `order_card_number` VARCHAR(50)  NOT NULL DEFAULT '', CHANGE `order_card_type` `order_card_type` VARCHAR(50)  NOT NULL DEFAULT '', CHANGE `order_card_expiry_month` `order_card_expiry_month` INT(2) NOT NULL DEFAULT '0', CHANGE `order_card_expiry_year` `order_card_expiry_year` INT(4) NOT NULL DEFAULT '0', CHANGE `order_card_holder` `order_card_holder` VARCHAR(100)  NOT NULL DEFAULT '', CHANGE `order_cvv_code` `order_cvv_code` VARCHAR(4)  NOT NULL DEFAULT '', CHANGE `bank_id` `bank_id` VARCHAR(255)  NOT NULL DEFAULT ''; ");
		$db->execute();

		$db->setQuery("ALTER TABLE `#__app_sch_order_items` CHANGE `gcalendar_event_id` `gcalendar_event_id` VARCHAR(255) NOT NULL DEFAULT '', CHANGE `total_cost` `total_cost` DECIMAL(10,2) NOT NULL DEFAULT '0', CHANGE `vid` `vid` INT(11) NOT NULL DEFAULT '0';  ");
		$db->execute();

		$db->setQuery("ALTER TABLE `#__app_sch_temp_temp_order_items` CHANGE `params` `params` VARCHAR(100) NULL, CHANGE `vid` `vid` INT(11) NOT NULL DEFAULT '0';");
		$db->execute();



		$dbtable = array('#__app_sch_temp_orders','#__app_sch_temp_order_field_options','#__app_sch_temp_order_items','#__app_sch_temp_temp_order_field_options','#__app_sch_temp_temp_order_items');
		for($i=0;$i<count($dbtable);$i++){
			$table = $dbtable[$i];
			$db->setQuery("Delete from `".$table."`");
			$db->execute();
		}
		$msg = JText::_('OS_DATABASE_OPTIMIZATION_SUCESSFULLY');
		$mainframe->enqueueMessage($msg);
		$mainframe->redirect("index.php?option=com_osservicesbooking&task=cpanel_list");
	}
	
	/**
	 * Control panel
	 *
	 * @param unknown_type $option
	 */
	static function cpanel_list($option)
	{
		global $mainframe,$configClass,$jinput;
		
		$db 			= JFactory::getDbo();
		$config 		= new JConfig();
		$offset 		= $config->offset;
		$current_date 	= JFactory::getDate('now',$offset);
		$cdate_int		= strtotime($current_date);
		
		//today
		$return			= OSBHelper::checkDate('today');
		$start_time		= date("Y-m-d H:i:s",$return[0]);
		$end_time		= date("Y-m-d H:i:s",$return[1]);
		$db->setQuery("SELECT SUM(order_total) FROM #__app_sch_orders WHERE order_status in ('S') AND order_date > '$start_time' AND order_date < '$end_time'");
		$today			= $db->loadResult();
		$lists['today'] = ($today > 0 ? $today:0);
		//yesterday
		$return			= OSBHelper::checkDate('yesterday');
		$start_time		= date("Y-m-d H:i:s",$return[0]);
		$end_time		= date("Y-m-d H:i:s",$return[1]);
		$db->setQuery("SELECT SUM(order_total) FROM #__app_sch_orders WHERE order_status in ('S') AND order_date > '$start_time' AND order_date < '$end_time'");
		$yesterday		= $db->loadResult();
		$lists['yesterday'] = ($yesterday > 0 ? $yesterday:0);
		//this month
		$return			= OSBHelper::checkDate('current_month');
		$start_time		= date("Y-m-d H:i:s",$return[0]);
		$end_time		= date("Y-m-d H:i:s",$return[1]);
		$db->setQuery("SELECT SUM(order_total) FROM #__app_sch_orders WHERE order_status in ('S') AND order_date > '$start_time' AND order_date < '$end_time'");
		$current_month	= $db->loadResult();
		$lists['current_month'] = ($current_month > 0 ? $current_month:0);
		//last month
		$return			= OSBHelper::checkDate('last_month');
		$start_time		= date("Y-m-d H:i:s",$return[0]);
		$end_time		= date("Y-m-d H:i:s",$return[1]);
		$db->setQuery("SELECT SUM(order_total) FROM #__app_sch_orders WHERE order_status in ('S') AND order_date > '$start_time' AND order_date < '$end_time'");
		$last_month     = $db->loadResult();
		$lists['last_month'] = ($last_month > 0 ? $last_month:0);
		//current year
		$return			= OSBHelper::checkDate('current_year');
		$start_time		= date("Y-m-d H:i:s",$return[0]);
		$end_time		= date("Y-m-d H:i:s",$return[1]);
		$db->setQuery("SELECT SUM(order_total) FROM #__app_sch_orders WHERE order_status in ('S') AND order_date > '$start_time' AND order_date < '$end_time'");
		$current_year	= $db->loadResult();
		$lists['current_year'] = ($current_year > 0 ? $current_year:0);
		//last year
		$return			= OSBHelper::checkDate('last_year');
		$start_time		= date("Y-m-d H:i:s",$return[0]);
		$end_time		= date("Y-m-d H:i:s",$return[1]);
		$db->setQuery("SELECT SUM(order_total) FROM #__app_sch_orders WHERE order_status in ('S') AND order_date > '$start_time' AND order_date < '$end_time'");
		$last_year		= $db->loadResult();
		$lists['last_year'] = ($last_year > 0 ? $last_year:0);
		
		
		$db->setQuery("Select id as value, service_name as text from #__app_sch_services where published = '1' order by service_name");
		$services = $db->loadObjectList();
		$serviceArr = array();
		//$serviceArr[] = JHTML::_('select.option','',JText::_('OS_SELECT_SERVICE'));
		//$serviceArr   = array_merge($serviceArr,$services);
		$lists['services'] = JHTML::_('select.genericlist',$services,'sid[]','multiple class="input-large form-select"','value','text');
		
		$db->setQuery("Select id as value, employee_name as text from #__app_sch_employee where published = '1' order by employee_name");
		$employees = $db->loadObjectList();
		$employeeArr = array();
		//$employeeArr[] = JHTML::_('select.option','',JText::_('OS_SELECT_EMPLOYEES'));
		$employeeArr = array_merge($employeeArr,$employees);
		$lists['employee'] = JHTML::_('select.genericlist',$employees,'eid[]','multiple class="input-large form-select "','value','text');
		
		$options = array();
		$options[]					= JHtml::_('select.option','',JText::_('OS_FILTER_STATUS'));
		$options[]					= JHtml::_('select.option','P',JText::_('OS_PENDING'));
		$options[]					= JHtml::_('select.option','S',JText::_('OS_COMPLETE'));
		$options[]					= JHtml::_('select.option','C',JText::_('OS_CANCEL'));
		//$lists['order_status']		= JHtml::_('select.genericlist',$options,'order_status','class="input-small"','value','text');
		$lists['order_status']		= OSBHelper::buildOrderStaticDropdownList('','',JText::_('OS_FILTER_STATUS'),'order_status');


		$rangeOption				= [];
		$rangeOption[]				= JHtml::_('select.option','0',JText::_('OS_LAST_12_MONTHS'));
		$rangeOption[]				= JHtml::_('select.option','1',JText::_('OS_LAST_9_MONTHS'));
		$rangeOption[]				= JHtml::_('select.option','2',JText::_('OS_LAST_6_MONTHS'));
		$rangeOption[]				= JHtml::_('select.option','3',JText::_('OS_LAST_3_MONTHS'));
		$rangeOption[]				= JHtml::_('select.option','4',JText::_('OS_LAST_MONTH'));
		$rangeOption[]				= JHtml::_('select.option','5',JText::_('OS_THIS_MONTH'));
		$lists['initial_range']     = JHtml::_('select.genericlist',$rangeOption,'range_option','onChange="javascript:showGraph(this.value)" class="input-large form-select imedium"','value','text', $jinput->getInt('range_option', 0));
		HTML_OSappscheduleCpanel::showControlpanel($lists);
	}
	
	/**
	 * Creates the buttons view.
	 * @param string $link targeturl
	 * @param string $image path to image
	 * @param string $text image description
	 * @param boolean $modal 1 for loading in modal
	 */
	static function quickiconButton($link, $image, $text, $modal = 0)
	{
		//initialise variables
		$lang 		= &JFactory::getLanguage();

		if($link == ""){
			$div_id = "id = 'oschecking_div'";
		}
  		?>

		<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;" <?php echo $div_id; ?>>
			<div class="icon">
				<?php
				if ($modal == 1) {
					JHTML::_('behavior.modal');
				?>
					<a href="<?php echo $link.'&amp;tmpl=component'; ?>" style="cursor:pointer" class="modal" rel="{handler: 'iframe', size: {x: 650, y: 400}}">
				<?php
				} else {
				?>
					<a href="<?php echo $link; ?>">
				<?php
				}
					echo JHTML::_('image', 'administrator/components/com_osservicesbooking/asset/images/' . $image, $text);
				?>
					<span><?php echo $text; ?></span>
				</a>
			</div>
		</div>
		<?php
	}

    /**
     * Get month Report
     * @param $current_month_offset
     * @param $before
     * @param $after
     */
    public static function getMonthlyReport($current_month_offset, $before, $after){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*')
            ->from('#__app_sch_orders')
            ->where('order_status = "S"')
            ->where('order_date <= "'.$before.'"')
            ->where('order_date >= "'.$after.'"')
            ->order('order_date DESC');
        $db->setQuery($query);
        $data = $db->loadObjectList();
        return $data;
    }
}
?>