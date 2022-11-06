<?php
/*------------------------------------------------------------------------
# analytics.html.php - Ossolution Services Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2022 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die;

class HTML_OSappscheduleAnalytics
{
	static function serviceGraph($type, $lists)
	{
		global $mapClass, $configClass;
		switch($type)
		{
			case "0":
				JToolBarHelper::title(JText::_('OS_SERVICE_ANALYTICS'), 'chart');
			break;
			case "1":
				JToolBarHelper::title(JText::_('OS_EMPLOYEE_ANALYTICS'), 'chart');
			break;
			case "2":
				JToolBarHelper::title(JText::_('OS_VENUE_ANALYTICS'), 'chart');
			break;
		}
		JToolbarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',JText::_('OS_DASHBOARD'),false);
		$document = JFactory::getDocument();
		?>
		<?php
		 OSBHelper::loadChartJS();
		 ?>
		<div class="<?php echo $mapClass['row-fluid'];?>">
			<div class="<?php echo $mapClass['span12'];?>">
				<canvas id="serviceChart" style="width:100%; height:400px;"></canvas>
				 <script type="text/javascript">
					jQuery(document).ready(function () {
						showGraph(0);
					});

					function showGraph(range_option)
					{
						{
							jQuery.post("<?php echo JUri::root().'administrator/index.php?option=com_osservicesbooking&task=analytics_serviceRevenue&tmpl=component&format=json&type='.$type; ?>&range_option=" + range_option,
							function (data)
							{
								data = JSON.parse(data);
								/*
								var name = [];
								var income = [];
								var color = [];
								console.log(data);


								for (var i in data) {
									name.push(data[i].month);
									income.push(data[i].income);
									color.push(data[i].color);
								}

								var chartdata = {
									labels: name,
									datasets: [
										{
											label: '<?php echo JText::_("OS_TOTAL_INCOME");?>',
											borderColor: color,
											data: income,
											fill: false
										}
									]
								};
								*/
								const labels = data[0].month;
								const dataSets = [];
										data.forEach(o => dataSets.push({
											label: o.service_name,
											data: o.income,
											borderColor: o.color,
											borderWidth: 1,
											fill: false
										  })
										);
								var chartdata = {
									labels: labels,
									datasets:dataSets
								};

								var graphTarget = jQuery("#serviceChart");

								var barGraph = new Chart(graphTarget, {
									type: 'line',
									data: chartdata,
									options: {
										scales: {
											yAxes: [{
												ticks: {
													// Include a dollar sign in the ticks
													callback: function(value, index, ticks) {
														return value + ' <?php echo $configClass["currency_format"];?>';
													}
												}
											}]
										}
									}
								});
							});
						}
					}
				 </script>
				 <strong>
					<?php echo JText::_('OS_INITIAL_RANGE');?>: <?php echo $lists['initial_range'];?>
				 </strong>
			</div>
		</div>
		<?php
	}
}

?>
