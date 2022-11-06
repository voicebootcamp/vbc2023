<?php
/*------------------------------------------------------------------------
# cpanel.html.php - Ossolution Services Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2022 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die;

class HTML_OSappscheduleCpanel
{
	static function statisticArea()
	{
		?>
		<table width="100%" >
			<tr>
				<td width="100%" colspan="2" style="text-align:center;">
					<img src="<?php echo JUri::root()?>administrator/components/com_osservicesbooking/asset/images/osb_jed_small.png" width="100%" />
				</td>
			</tr>
			<tr>
				<td width="50%" style="text-align:left;font-size:11px;">
					<svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="#0a8cbc" class="bi bi-power" viewBox="0 0 16 16">
					  <path d="M7.5 1v7h1V1h-1z"/>
					  <path d="M3 8.812a4.999 4.999 0 0 1 2.578-4.375l-.485-.874A6 6 0 1 0 11 3.616l-.501.865A5 5 0 1 1 3 8.812z"/>
					</svg>
					&nbsp;
					INSTALLED VERSION.
				</td>
				<td width="50%" style="text-align:left;font-weight:bold;padding-left:10px;vertical-align:top;padding-bottom:10px;">
					<?php echo OSBHelper::getInstalledVersion();?>
				</td>
			</tr>
			<tr>
				<td width="50%" style="text-align:left;font-size:11px;">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#0a8cbc" class="bi bi-building" viewBox="0 0 16 16">
					  <path fill-rule="evenodd" d="M14.763.075A.5.5 0 0 1 15 .5v15a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5V14h-1v1.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V10a.5.5 0 0 1 .342-.474L6 7.64V4.5a.5.5 0 0 1 .276-.447l8-4a.5.5 0 0 1 .487.022zM6 8.694 1 10.36V15h5V8.694zM7 15h2v-1.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 .5.5V15h2V1.309l-7 3.5V15z"/>
					  <path d="M2 11h1v1H2v-1zm2 0h1v1H4v-1zm-2 2h1v1H2v-1zm2 0h1v1H4v-1zm4-4h1v1H8V9zm2 0h1v1h-1V9zm-2 2h1v1H8v-1zm2 0h1v1h-1v-1zm2-2h1v1h-1V9zm0 2h1v1h-1v-1zM8 7h1v1H8V7zm2 0h1v1h-1V7zm2 0h1v1h-1V7zM8 5h1v1H8V5zm2 0h1v1h-1V5zm2 0h1v1h-1V5zm0-2h1v1h-1V3z"/>
					</svg>
					&nbsp;
					AUTHOR.
				</td>
				<td width="50%" style="text-align:left;font-weight:bold;padding-left:10px;padding-bottom:10px;">
					OSSOLUTION
				</td>
			</tr>
			<tr>
				<td width="100%" colspan="2" style="padding-bottom:10px;text-align:left">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#0a8cbc" class="bi bi-globe" viewBox="0 0 16 16">
					  <path d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm7.5-6.923c-.67.204-1.335.82-1.887 1.855A7.97 7.97 0 0 0 5.145 4H7.5V1.077zM4.09 4a9.267 9.267 0 0 1 .64-1.539 6.7 6.7 0 0 1 .597-.933A7.025 7.025 0 0 0 2.255 4H4.09zm-.582 3.5c.03-.877.138-1.718.312-2.5H1.674a6.958 6.958 0 0 0-.656 2.5h2.49zM4.847 5a12.5 12.5 0 0 0-.338 2.5H7.5V5H4.847zM8.5 5v2.5h2.99a12.495 12.495 0 0 0-.337-2.5H8.5zM4.51 8.5a12.5 12.5 0 0 0 .337 2.5H7.5V8.5H4.51zm3.99 0V11h2.653c.187-.765.306-1.608.338-2.5H8.5zM5.145 12c.138.386.295.744.468 1.068.552 1.035 1.218 1.65 1.887 1.855V12H5.145zm.182 2.472a6.696 6.696 0 0 1-.597-.933A9.268 9.268 0 0 1 4.09 12H2.255a7.024 7.024 0 0 0 3.072 2.472zM3.82 11a13.652 13.652 0 0 1-.312-2.5h-2.49c.062.89.291 1.733.656 2.5H3.82zm6.853 3.472A7.024 7.024 0 0 0 13.745 12H11.91a9.27 9.27 0 0 1-.64 1.539 6.688 6.688 0 0 1-.597.933zM8.5 12v2.923c.67-.204 1.335-.82 1.887-1.855.173-.324.33-.682.468-1.068H8.5zm3.68-1h2.146c.365-.767.594-1.61.656-2.5h-2.49a13.65 13.65 0 0 1-.312 2.5zm2.802-3.5a6.959 6.959 0 0 0-.656-2.5H12.18c.174.782.282 1.623.312 2.5h2.49zM11.27 2.461c.247.464.462.98.64 1.539h1.835a7.024 7.024 0 0 0-3.072-2.472c.218.284.418.598.597.933zM10.855 4a7.966 7.966 0 0 0-.468-1.068C9.835 1.897 9.17 1.282 8.5 1.077V4h2.355z"/>
					</svg>
					&nbsp;
					<a href="http://joomdonation.com/joomla-extensions/joomla-services-appointment-booking.html" target="_blank" title="OS Services Booking official page">OSB OFFICIAL PAGE.</a>
				</td>
			</tr>
			<tr>
				<td width="100%" colspan="2" style="padding-bottom:10px;text-align:left">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#0a8cbc" class="bi bi-people-fill" viewBox="0 0 16 16">
					  <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
					  <path fill-rule="evenodd" d="M5.216 14A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216z"/>
					  <path d="M4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z"/>
					</svg>&nbsp;
					<a href="http://joomdonation.com/forum/os-services-booking.html" target="_blank" title="OS Services Booking forum">FORUM SUPPORT.</a>
				</td>
			</tr>
			<tr>
				<td width="100%" colspan="2" style="padding-bottom:10px;text-align:left">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#0a8cbc" class="bi bi-question-circle" viewBox="0 0 16 16">
					  <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
					  <path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
					</svg>&nbsp;
					<a href="http://joomdonation.com/support-tickets.html" target="_blank" title="OS Services Booking support ticket">SUPPORT TICKET.</a>
				</td>
			</tr>
			<tr>
				<td width="100%"  colspan="2" style="padding-bottom:10px;text-align:left">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#0a8cbc" class="bi bi-book" viewBox="0 0 16 16">
					  <path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811V2.828zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z"/>
					</svg>
					&nbsp;
					<a href="http://joomdonationdemo.com/osservicesbooking/documentation/" target="_blank" title="OS Services Booking documentation">DOCUMENTATION.</a>
				</td>
			</tr>
		</table>
		<?php
	}

	static function quickLinks()
	{
		if(OSBHelper::isJoomla4())
		{
			$J4 = "Joomla4";
		}
		else
		{
			$J4 = "";
		}
		?>
		<table class="adminlist">
			<tr>
				<td>
					<div id="cpanel<?php echo $J4;?>">
						<?php							
						OSappscheduleCpanel::quickiconButton('index.php?option=com_osservicesbooking&amp;task=category_list', 'icon-48-categories.png', JText::_('OS_MANAGE_CATEGORIES'));
						OSappscheduleCpanel::quickiconButton('index.php?option=com_osservicesbooking&amp;task=category_add', 'icon-48-category-add.png', JText::_('OS_NEW_CATEGORY'));
						OSappscheduleCpanel::quickiconButton('index.php?option=com_osservicesbooking&amp;task=venue_list', 'icon-48-marker.png', JText::_('OS_MANAGE_VENUES'));
						OSappscheduleCpanel::quickiconButton('index.php?option=com_osservicesbooking&amp;task=venue_add', 'icon-48-newvenue.png', JText::_('OS_NEW_VENUE'));
						OSappscheduleCpanel::quickiconButton('index.php?option=com_osservicesbooking&amp;task=service_list', 'icon-48-calendar.png', JText::_('OS_MANAGE_SERVICES'));
						OSappscheduleCpanel::quickiconButton('index.php?option=com_osservicesbooking&amp;task=service_add', 'icon-48-calendar-add.png', JText::_('OS_NEW_SERVICE'));
						OSappscheduleCpanel::quickiconButton('index.php?option=com_osservicesbooking&amp;task=employee_list', 'icon-48-employee.png', JText::_('OS_EMPLOYEE_MANAGE'));
						OSappscheduleCpanel::quickiconButton('index.php?option=com_osservicesbooking&amp;task=employee_add', 'icon-48-document-add.png', JText::_('OS_NEW_EMPLOYEE'));
						OSappscheduleCpanel::quickiconButton('index.php?option=com_osservicesbooking&amp;task=orders_list', 'icon-48-orders.png', JText::_('OS_MANAGE_ORDERS'));
						OSappscheduleCpanel::quickiconButton('index.php?option=com_osservicesbooking&amp;task=plugin_list', 'icon-48-payments.png', JText::_('OS_MANAGE_PAYMENT_PLUGINS'));
						OSappscheduleCpanel::quickiconButton('index.php?option=com_osservicesbooking&amp;task=coupon_list', 'icon-48-coupon.png', JText::_('OS_MANAGE_COUPONS'));
						//OSappscheduleCpanel::quickiconButton('index.php?option=com_osservicesbooking&amp;task=coupon_add', 'icon-48-add-coupon.png', JText::_('OS_ADD_COUPON'));
						OSappscheduleCpanel::quickiconButton('index.php?option=com_osservicesbooking&amp;task=balance_list', 'icon-48-user-balance.png', JText::_('OS_USER_BALANCE'));
						OSappscheduleCpanel::quickiconButton('index.php?option=com_osservicesbooking&amp;task=worktime_list', 'icon-48-worktime.png', JText::_('OS_WORKING_TIME'));
						OSappscheduleCpanel::quickiconButton('index.php?option=com_osservicesbooking&amp;task=worktimecustom_list', 'icon-48-worktimecustom.png', JText::_('Custom time'));
						OSappscheduleCpanel::quickiconButton('index.php?option=com_osservicesbooking&amp;task=fields_list', 'icon-48-customfield.png', JText::_('OS_CUSTOM_FIELD'));
						OSappscheduleCpanel::quickiconButton('index.php?option=com_osservicesbooking&amp;task=fields_add', 'icon-48-levels-add.png', JText::_('OS_ADD_CUSTOM_FIELD'));
						OSappscheduleCpanel::quickiconButton('index.php?option=com_osservicesbooking&amp;task=translation_list', 'icon-48-languages.png', JText::_('OS_TRANSLATION'));
						OSappscheduleCpanel::quickiconButton('index.php?option=com_osservicesbooking&amp;task=emails_list', 'icon-48-emails.png', JText::_('OS_EMAIL_TEMPLATES'));
						OSappscheduleCpanel::quickiconButton('index.php?option=com_osservicesbooking&amp;task=configuration_list', 'icon-48-config.png', JText::_('OS_CONFIGURATION_CONFIGURATION'));
						OSappscheduleCpanel::quickiconButton('', 'updated_failure.png', JText::_('OS_CHECKING_VERSION'));
						?>
						<script language="javascript">
							window.onload = function() {
							   checkingVersion('<?php echo OSBHelper::getInstalledVersion();?>');
							};
						</script>
					</div>
				</td>
			</tr>
		</table>
		<?php
	}
	static function showControlpanel($lists)
	{
		global $mainframe,$configClass,$jinput, $mapClass;
		//JHtml::_('formbehavior.chosen', 'select');
		JToolBarHelper::title(JText::_('OS_DASHBOARD'), 'home');
		JToolbarHelper::preferences('com_osservicesbooking');
		$document = JFactory::getDocument();

		if(OSBHelper::isJoomla4())
		{
			$span1 = $mapClass['span4'];
			$span2 = $mapClass['span8'];
			$span3 = '';
		}
		else
		{
			$span1 = $mapClass['span3'];
			$span2 = $mapClass['span5'];
			$span3 = $mapClass['span4'];
		}
		?>
		<div class="<?php echo $mapClass['row-fluid'];?>">
			<div class="<?php echo $span1;?>" style="background:#F4F4F4;text-align:center;">
				<?php
				self::statisticArea();
				?>
			</div>
			<div class="<?php echo $span2;?>">
				<?php
				self::quickLinks();
				?>
			</div>
			<?php
		if(OSBHelper::isJoomla4())
		{
			?>
			</div>
			<div class="<?php echo $mapClass['row-fluid'];?>">
				<div class="<?php echo $mapClass['span12'];?>" style="text-align:center;margin:10px 0px;">
					<h3>	
						<?php echo JText::_('OS_GENERAL_STATITICS');?>
					</h3>
				</div>
			</div>
			<div class="<?php echo $mapClass['row-fluid'];?>">
				<div class="<?php echo $mapClass['span6'];?>">
			<?php
		}
		else
		{
			?>
			<div class="<?php echo $span3;?>">
			<?php

		}
			?>
				<table class="table dashboard-table" style="width:100%;">
					<tbody>
					<tr>
						<td class="dashboard-table-header" style="width:100%;">
							<?php echo Jtext::_('OS_MONTHLY_REPORT'); ?>
						</td>
					</tr>
					<tr>
						<td style="width:100%;">
							 <?php
							 OSBHelper::loadChartJS();
							 ?>
							 <canvas id="myChart" style="width:100%; height:400px;"></canvas>
						     <script type="text/javascript">
								jQuery(document).ready(function () {
									showGraph(0);
								});

								function showGraph(range_option)
								{
									{
										jQuery.post("<?php echo JUri::root().'administrator/index.php?option=com_osservicesbooking&task=cpanel_revenue&tmpl=component&format=json'; ?>&range_option=" + range_option,
										function (data)
										{
											data = JSON.parse(data);
											var name = [];
											var orders = [];
											var income = [];
											console.log(data);


											for (var i in data) {
												name.push(data[i].month);
												orders.push(data[i].orders);
												income.push(data[i].income);
											}

											var chartdata = {
												labels: name,
												datasets: [
													{
														label: '<?php echo JText::_("OS_NUMBER_ORDERS");?>',
														borderColor: '#46d5f1',
														data: orders,
														fill: false
													},
													{
														label: '<?php echo JText::_("OS_TOTAL_INCOME");?>',
														borderColor: '#f3350d',
														data: income,
														fill: false
													}
												]
											};

											var graphTarget = jQuery("#myChart");

											var barGraph = new Chart(graphTarget, {
												type: 'line',
												data: chartdata
											});
										});
									}
								}
							 </script>
							 <strong>
								<?php echo JText::_('OS_INITIAL_RANGE');?>: <?php echo $lists['initial_range'];?>
							 </strong>
							</td>
						</tr>
					</tbody>
				</table>
			<?php
			if(OSBHelper::isJoomla4())
			{
				?>
				</div>
				<div class="<?php echo $mapClass['span6'];?>">
				<?php
			}
			?>
					<div class="<?php echo $mapClass['row-fluid'];?>">
						<div class="<?php echo $mapClass['span12'];?>">
							<div class="tabs clearfix">
								<?php echo JHtml::_('bootstrap.startTabSet', 'dashboardTab', array('active' => 'generalstatitics')); ?>
									<?php echo JHtml::_('bootstrap.addTab', 'dashboardTab', 'generalstatitics', JText::_('OS_GENERAL_STATITICS')); ?>
										<div class="tab-pane active" style="text-align:left;">
											<table width="100%"	class="table table-striped">
												<thead>
													<tr>
														<th style="text-align:left;">
															<?php echo JText::_('OS_TIME');?>
														</th>
														<th style="text-align:left;">
															<?php echo JText::_('OS_ORDER_INCOME');?>
														</th>
													</tr>
												</thead>
												<tbody>
													<tr class="row0">
														<td align="left">
															<?php
															echo JText::_("OS_TODAY");
															?>
														</td>
														<td align="left">
															<?php
															echo $configClass['currency_symbol'];
															echo " ";
															echo $lists['today'];
															?>
														</td>
													</tr>
													<tr class="row1">
														<td align="left">
															<?php
															echo JText::_("OS_YESTERDAY");
															?>
														</td>
														<td align="left">
															<?php
															echo $configClass['currency_symbol'];
															echo " ";
															echo $lists['yesterday'];
															?>
														</td>
													</tr>
													<tr class="row0">
														<td align="left">
															<?php
															echo JText::_("OS_CURRENT_MONTH");
															?>
														</td>
														<td align="left">
															<?php
															echo $configClass['currency_symbol'];
															echo " ";
															echo $lists['current_month'];
															?>
														</td>
													</tr>
													<tr class="row1">
														<td align="left">
															<?php
															echo JText::_("OS_LAST_MONTH");
															?>
														</td>
														<td align="left">
															<?php
															echo $configClass['currency_symbol'];
															echo " ";
															echo $lists['last_month'];
															?>
														</td>
													</tr>
													<tr class="row0">
														<td align="left">
															<?php
															echo JText::_("OS_CURRENT_YEAR");
															?>
														</td>
														<td align="left">
															<?php
															echo $configClass['currency_symbol'];
															echo " ";
															echo $lists['current_year'];
															?>
														</td>
													</tr>
													<tr class="row1">
														<td align="left">
															<?php
															echo JText::_("OS_LAST_YEAR");
															?>
														</td>
														<td align="left">
															<?php
															echo $configClass['currency_symbol'];
															echo " ";
															echo $lists['last_year'];
															?>
														</td>
													</tr>
												</tbody>
											</table>
										</div>
									<?php echo JHtml::_('bootstrap.endTab') ?>
									<?php echo JHtml::_('bootstrap.addTab', 'dashboardTab', 'report', JText::_('OS_GENERAL_REPORT')); ?>

										<div class="tab-pane" style="text-align:left;">
											<form class="form-horizontal" action="index.php?option=com_osservicesbooking&task=orders_exportreport" method="POST" target="_blank" id="reportForm" name="reportForm">
												<div class="control-group" style="text-align:center;padding:20px;"> 
													<label>
														<?php echo JText::_('OS_PLEASE_SELECT_FILTER_PARAMETERS_TO_EXPORT_REPORT');?>
													</label>
												</div>
												<div class="control-group">
													<label class="control-label" style="width:135px;"><?php echo JText::_('OS_DATE_FROM')?>: </label>
													<div class="controls">
														<?php 
														echo JHtml::_('calendar','','date_from','date_from','%Y-%m-%d',array('class' => 'input-small'));
														?>
													</div>
												</div>
												<div class="control-group">
													<label class="control-label" style="width:135px;"><?php echo JText::_('OS_DATE_TO')?>: </label>
													<div class="controls">
														<?php 
														echo JHtml::_('calendar','','date_to','date-to','%Y-%m-%d',array('class' => 'input-small'));
														?>
													</div>
												</div>
												<div class="control-group">
													<label class="control-label" style="width:135px;"><?php echo JText::_('OS_SERVICE')?>: </label>
													<div class="controls">
														<?php 
														echo $lists['services'];
														?>
													</div>
												</div>
												<div class="control-group">
													<label class="control-label" style="width:135px;"><?php echo JText::_('OS_EMPLOYEE')?>: </label>
													<div class="controls">
														<?php 
														echo $lists['employee'];
														?>
													</div>
												</div>
												<div class="control-group">
													<label class="control-label" style="width:135px;"><?php echo JText::_('OS_STATUS')?>: </label>
													<div class="controls">
														<?php 
														echo $lists['order_status'];
														?>
													</div>
												</div>
												<div class="control-group" style="text-align:center;padding:20px;">
													<input type="button" class="btn btn-info" value="<?php echo JText::_('OS_EXPORT_REPORT');?>" onClick="javascript:exportReport();"/>
													<input type="button" class="btn btn-danger" value="<?php echo JText::_('OS_EXPORT_CSV');?>" onClick="javascript:exportReportCSV();" />
												</div>
												<input type="hidden" name="option" value="com_osservicesbooking" />
												<input type="hidden" name="task" value="orders_exportreport" />
												<input type="hidden" name="tmpl" value="component" />
											</form>
										</div>
									<?php echo JHtml::_('bootstrap.endTab') ?>
									<?php echo JHtml::_('bootstrap.addTab', 'dashboardTab', 'optimize', JText::_('OS_OPTIMIZE_DATABASE')); ?>
										<div class="tab-pane" style="text-align:left;">
											<div class="<?php echo $mapClass['row-fluid'];?>">
												<div class="<?php echo $mapClass['span12'];?>" style="text-align:center;padding:20px;">
													<?php echo JText::_('OS_OPTIMIZE_DATABASE_EXPLAIN');?>
													<div class="clearfix"></div>
													<input type="button" class="btn btn-warning" value="<?php echo JText::_('OS_OPTIMIZE_DATABASE');?>" onclick="javascript:optimizedatabase();"/>
												</div>
											</div>
										</div> <!-- end tab -->
									<?php echo JHtml::_('bootstrap.endTab') ?>
					    		<!--</div> -->
								<?php echo JHtml::_('bootstrap.endTabSet'); ?>
					    	</div>
						</div>
					</div>
				</div>
			</div>
		<input type="hidden" name="live_site" id="live_site" value="<?php echo JUri::root();?>" />
		<script type="text/javascript">
		function exportReportCSV()
		{
			document.reportForm.task.value = "orders_csvexportreport";
			document.reportForm.submit();
		}
		function exportReport()
		{
			document.reportForm.task.value = "orders_exportreport";
			document.reportForm.submit();
		}
		function optimizedatabase(){
			var answer = confirm("<?php echo JText::_('OS_ARE_YOU_SURE_YOU_WANT_TO_OPTIMIZE_OSB_DATABASE')?>");
			if(answer == 1){
				location.href = "index.php?option=com_osservicesbooking&task=cpanel_optimizedatabase";
			}
		}
		</script>
		<?php
	}

}

?>
