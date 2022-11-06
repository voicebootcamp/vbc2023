<?php
/*------------------------------------------------------------------------
# analytics.php - Ossolution Services Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2022 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die;

class OSappscheduleAnalytics{
	/**
	 * Default static function
	 *
	 * @param unknown_type $option
	 */
	static function display($option,$task)
	{
		global $jinput;
		$type = $jinput->getInt('type', 0);
		switch ($task)
		{
			case "analytics_serviceRevenue":
				OSappscheduleAnalytics::serviceRevenue($type);
			break;
			case "analytics_services":
				OSappscheduleAnalytics::serviceGraph(0);
			break;
			case "analytics_employees":
				OSappscheduleAnalytics::serviceGraph(1);
			break;
			case "analytics_venues":
				OSappscheduleAnalytics::serviceGraph(2);
			break;
		}
	}

	static function serviceRevenue($type)
	{
		global $jinput;

		$data = [];

		$db = JFactory::getDbo();
		$config = JFactory::getConfig();
		
		$range_option = $jinput->getInt('range_option', 0 );

		if($type == 0)
		{
			$db->setQuery("Select * from #__app_sch_services where published = '1'");
			$services = $db->loadObjectList();
		}
		elseif($type == 1)
		{
			$db->setQuery("Select *, employee_name as service_name from #__app_sch_employee where published = '1'");
			$services = $db->loadObjectList();
		}
		else
		{
			$db->setQuery("Select *, venue_name as service_name from #__app_sch_venues where published = '1'");
			$services = $db->loadObjectList();
		}

		$randomColor = ['#f20e34','#d715c2', '#1a15d7','#15d5d7','#15d723','#c2c814','#f27007','#241a1a','#bc9d0a','#780e6d','#0e2a78','#82b1a9','#8682b1','#07fa12','#06d7fd','#f89176','#90ddfa','#c405cb','#b2e111','#fef507'];

		
		foreach($services as $service)
		{
			if($type == 0)
			{
				$extraSql       = " b.sid = '$service->id' ";
			}
			elseif($type == 1)
			{
				$extraSql       = " b.eid = '$service->id' ";
			}
			else
			{
				$extraSql       = " b.vid = '$service->id' ";
			}
			$tmp				= new stdClass();
			if($service->service_color != '')
			{
				$service_color	= $service->service_color;
			}
			else
			{
				$service_color	= $randomColor[array_rand($randomColor)];
			}

			$tmp->color			= $service_color;
			$tmp->service_name  = $service->service_name;
			switch ($range_option)
			{
				case '0':
					//12 months
					$current_month = date("n");
					$current_year  = date("Y");
					$j			   = 0;
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
						$toDate   = $date->toSql(true);

						$db->setQuery("Select sum(a.order_final_cost) as income from #__app_sch_orders as a left join #__app_sch_order_items as b on a.id = b.order_id where $extraSql and a.order_status = 'S' and a.order_date >= ".$db->quote($fromDate) . " and a.order_date <= ".$db->quote($toDate));
						$row = $db->loadObject();

						
						$tmp->month[$j]	 = $date->format('F Y');
						$tmp->income[$j] = (float)$row->income;

						$j++;
					}
					$data[] = $tmp;
				break;
				case '1':
					//9 months
					$current_month = date("n");
					$current_year  = date("Y");
					$j			   = 0;
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

						$db->setQuery("Select sum(a.order_final_cost) as income from #__app_sch_orders as a left join #__app_sch_order_items as b on a.id = b.order_id where $extraSql and a.order_status = 'S' and a.order_date >= ".$db->quote($fromDate) . " and a.order_date <= ".$db->quote($toDate));
						$row = $db->loadObject();

						
						$tmp->month[$j]	 = $date->format('F Y');
						$tmp->income[$j] = (float)$row->income;

						$j++;
					}
					$data[] = $tmp;
				break;
				case '2':
					//6 months
					$current_month = date("n");
					$current_year  = date("Y");
					$j			   = 0;
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

						$db->setQuery("Select sum(a.order_final_cost) as income from #__app_sch_orders as a left join #__app_sch_order_items as b on a.id = b.order_id where $extraSql and a.order_status = 'S' and a.order_date >= ".$db->quote($fromDate) . " and a.order_date <= ".$db->quote($toDate));
						$row = $db->loadObject();

						
						$tmp->month[$j]	 = $date->format('F Y');
						$tmp->income[$j] = (float)$row->income;

						$j++;
					}
					$data[] = $tmp;
				break;
				case '3':
					//3 months
					$current_month = date("n");
					$current_year  = date("Y");
					$j			   = 0;
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

						$db->setQuery("Select sum(a.order_final_cost) as income from #__app_sch_orders as a left join #__app_sch_order_items as b on a.id = b.order_id where $extraSql and a.order_status = 'S' and a.order_date >= ".$db->quote($fromDate) . " and a.order_date <= ".$db->quote($toDate));
						$row = $db->loadObject();

						
						$tmp->month[$j]	 = $date->format('F Y');
						$tmp->income[$j] = (float)$row->income;

						$j++;
					}
					$data[] = $tmp;
				break;
				case '4':
					$current_month = date("n");
					$current_year  = date("Y");
					$j			   = 0;
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

						$db->setQuery("Select sum(a.order_final_cost) as income from #__app_sch_orders as a left join #__app_sch_order_items as b on a.id = b.order_id where $extraSql and a.order_status = 'S' and a.order_date >= ".$db->quote($fromDate) . " and a.order_date <= ".$db->quote($toDate));
						$row = $db->loadObject();

						
						$tmp->month[$j]	 = $date->format('jS F Y');
						$tmp->income[$j] = (float)$row->income;

						$j++;
					}
					$data[] = $tmp;
				break;
				case '5':
					$current_month = date("n");
					$current_year  = date("Y");
					$j			   = 0;
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

						$db->setQuery("Select sum(a.order_final_cost) as income from #__app_sch_orders as a left join #__app_sch_order_items as b on a.id = b.order_id where $extraSql and a.order_status = 'S' and a.order_date >= ".$db->quote($fromDate) . " and a.order_date <= ".$db->quote($toDate));
						$row = $db->loadObject();

						
						$tmp->month[$j]	 = $date->format('jS F Y');
						$tmp->income[$j] = (float)$row->income;

						$j++;
					}
					$data[] = $tmp;
				break;		
			}
		}

		echo json_encode($data);
		JFactory::getApplication()->close();
	}

	static function serviceGraph($type)
	{
		global $mainframe,$configClass,$jinput;
		
		$rangeOption				= [];
		$rangeOption[]				= JHtml::_('select.option','0',JText::_('OS_LAST_12_MONTHS'));
		$rangeOption[]				= JHtml::_('select.option','1',JText::_('OS_LAST_9_MONTHS'));
		$rangeOption[]				= JHtml::_('select.option','2',JText::_('OS_LAST_6_MONTHS'));
		$rangeOption[]				= JHtml::_('select.option','3',JText::_('OS_LAST_3_MONTHS'));
		$rangeOption[]				= JHtml::_('select.option','4',JText::_('OS_LAST_MONTH'));
		$rangeOption[]				= JHtml::_('select.option','5',JText::_('OS_THIS_MONTH'));
		$lists['initial_range']     = JHtml::_('select.genericlist',$rangeOption,'range_option','onChange="javascript:showGraph(this.value)" class="input-large form-select imedium"','value','text', $jinput->getInt('range_option', 0));
		HTML_OSappscheduleAnalytics::serviceGraph($type, $lists);
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