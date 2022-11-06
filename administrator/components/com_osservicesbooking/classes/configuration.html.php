<?php
/*------------------------------------------------------------------------
# configuration.html.php - Ossolution Services Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2022 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
class HTML_OSappscheduleConfiguration
{
	/**
	 * Extra field list HTML
	 *
	 * @param unknown_type $option
	 * @param unknown_type $rows
	 * @param unknown_type $pageNav
	 * @param unknown_type $lists
	 */
	static function configuration_list($option,$configs,$lists)
	{
		global $mainframe,$_jversion,$configClass,$mapClass, $languages, $jinput;
		JHtml::_('behavior.multiselect');
		$jinput->set( 'hidemainmenu', 1 );
		$controlGroupClass  = $mapClass['control-group'];
		$controlLabelClass  = $mapClass['control-label'];
		$controlsClass		= $mapClass['controls'];
		$editor = JEditor::getInstance(JFactory::getConfig()->get('editor'));
		OSBHelper::loadTooltip();
		JToolBarHelper::title(JText::_('OS_CONFIGURATION_CONFIGURATION'),'cog');
		JToolBarHelper::save('configuration_save');
		JToolBarHelper::apply('configuration_apply');
		JToolBarHelper::cancel('configuration_cancel');
		JToolbarHelper::preferences('com_osservicesbooking');
        $editorPlugin = null;
        if (JPluginHelper::isEnabled('editors', 'codemirror'))
        {
            $editorPlugin = 'codemirror';
        }
        elseif(JPluginHelper::isEnabled('editor', 'none'))
        {
            $editorPlugin = 'none';
        }
        if ($editorPlugin)
        {
            $showCustomCss = 1;
        }else{
            $showCustomCss = 0;
        }

		if (OSBHelper::isJoomla4())
		{
			$tabApiPrefix = 'uitab.';

			Factory::getDocument()->getWebAssetManager()->useScript('showon');
		}
		else
		{
			$tabApiPrefix = 'bootstrap.';

			HTMLHelper::_('script', 'jui/cms.js', array('version' => 'auto', 'relative' => true));
		}
	?>
		<form method="POST" action="index.php?option=<?php echo $option; ?>&task=configuration_list" name="adminForm" id="adminForm">
		<div class="<?php echo $mapClass['row-fluid'];?>">
		<div class="tab-content">
		<?php echo JHtml::_($tabApiPrefix.'startTabSet', 'configTab', array('active' => 'general-page')); ?>
			<?php echo JHtml::_($tabApiPrefix.'addTab', 'configTab', 'general-page', JText::_('OS_CONFIGURATION_GENERAL')); ?>
				<div class="<?php echo $mapClass['row-fluid'];?>">
					<div class="<?php echo $mapClass['span6'];?>">
						<fieldset class="form-horizontal options-form">
							<legend><?php echo JText::_('OS_BUSINESS_INFORMATION')?></legend>
						
							<div class="<?php echo $controlGroupClass; ?>">
								<div class="<?php echo $controlLabelClass?>">
									<?php echo JText::_('OS_CONFIGURATION_BUSINESS_NAME'); ?>
									<?php
									OSBHelper::generateTip(JText::_('OS_CONFIGURATION_BUSINESS_NAME_DESC'));
									?>
								</div>
								<div class="<?php echo $controlsClass;?>">
									<input type="text" class="<?php echo $mapClass['input-large']; ?> ilarge" name="business_name" id="business_name" value="<?php echo $configs->business_name;?>" />
								</div>
							</div>

							<div class="<?php echo $controlGroupClass; ?>">
								<div class="<?php echo $controlLabelClass?>">
									<?php echo JText::_('OS_CONFIGURATION_EMAIL_NOTIFICATION'); ?>
									<?php
									OSBHelper::generateTip(JText::_('OS_CONFIGURATION_EMAIL_NOTIFICATION_DESC'));
									?>
								</div>
								<div class="<?php echo $controlsClass;?>">
									<input class="<?php echo $mapClass['input-large']; ?> ilarge" type="text" name="value_string_email_address" value="<?php echo $configs->value_string_email_address?>" />
								</div>
							</div>

							<div class="<?php echo $controlGroupClass; ?>">
								<div class="<?php echo $controlLabelClass?>">
									<?php echo JText::_('OS_CONFIGURATION_MOBILE_NOTIFICATION'); ?>
									<?php
									OSBHelper::generateTip(JText::_('OS_CONFIGURATION_MOBILE_NOTIFICATION_DESC'));
									?>
								</div>
								<div class="<?php echo $controlsClass;?>">
									<input class="<?php echo $mapClass['input-small']; ?> ishort" type="text" name="mobile_notification" value="<?php echo $configs->mobile_notification?>" />
								</div>
							</div>

							<div class="<?php echo $controlGroupClass; ?>">
								<div class="<?php echo $controlLabelClass?>">
									<?php echo JText::_('OS_CONFIGURATION_FOOTER_CONTENT'); ?>
									
								</div>
								<div class="<?php echo $controlsClass;?>">
									<textarea name="footer_content" id="footer_content" cols="40" rows="5" class="form-control"><?php echo $configs->footer_content?></textarea>
								</div>
							</div>
							<div class="<?php echo $controlGroupClass; ?>">
								<div class="<?php echo $controlLabelClass?>">
									<?php echo JText::_('OS_CONFIGURATION_META_KEYWORDS'); ?>
									
								</div>
								<div class="<?php echo $controlsClass;?>">
									<textarea name="meta_keyword" id="meta_keyword" cols="40" rows="2" class="form-control"><?php echo $configs->meta_keyword?></textarea>
								</div>
							</div>
							<div class="<?php echo $controlGroupClass; ?>">
								<div class="<?php echo $controlLabelClass?>">
									<?php echo JText::_('OS_CONFIGURATION_META_DESC'); ?>
									
								</div>
								<div class="<?php echo $controlsClass;?>">
									<textarea name="meta_desc" id="meta_desc" cols="40" rows="4" class="form-control"><?php echo $configs->meta_desc?></textarea>
								</div>
							</div>
						</fieldset>
						<fieldset class="form-horizontal options-form">
							<legend><?php echo JText::_('Map Setting')?></legend>
						
							<div class="<?php echo $controlGroupClass; ?>">
								<div class="<?php echo $controlLabelClass?>">
									<?php echo JText::_('Map Type'); ?>
									<?php
									OSBHelper::generateTip(JText::_('Select Map Type: Google Map or OpenStreetMap'));
									?>
								</div>
								<div class="<?php echo $controlsClass;?>">
									<?php OSappscheduleConfiguration::showCheckboxfield('map_type',(int)$configs->map_type,'Google Map', 'Open Street Map');?>
								</div>
							</div>
							<div class="<?php echo $controlGroupClass; ?>">
								<div class="<?php echo $controlLabelClass?>">
									<?php echo JText::_('Google Map API Key'); ?>
									<?php
									OSBHelper::generateTip(JText::_('In case you select to show Google Map. Please enter your Google Map API'));
									?>
								</div>
								<div class="<?php echo $controlsClass;?>">
									<input type="text" class="<?php echo $mapClass['input-large']; ?> ilarge" name="google_key" id="google_key" value="<?php echo $configs->google_key;?>" />
								</div>
							</div>
						</fieldset>
					</div>
					<div class="<?php echo $mapClass['span6'];?>">
						<fieldset class="form-horizontal options-form">
							<legend><?php echo JText::_('Download ID')?></legend>
						
							<div class="<?php echo $controlGroupClass; ?>">
								<div class="<?php echo $controlLabelClass?>">
									<?php echo JText::_('Download ID'); ?>
									<?php
									OSBHelper::generateTip(JText::_('Enter your Download ID into this config option to be able to use Joomla Update to update your site to latest version of OS Services Booking whenever there is new version available'));
									?>
								</div>
								<div class="<?php echo $controlsClass;?>">
									<input type="text" class="<?php echo $mapClass['input-large']; ?> ilarge" name="download_id" id="download_id" value="<?php echo $configs->download_id;?>" />
								</div>
							</div>
						</fieldset>
						<fieldset class="form-horizontal options-form">
							<legend><?php echo JText::_('OS_GENERAL_INFORMATION')?></legend>
							<div class="<?php echo $controlGroupClass; ?>">
								<div class="<?php echo $controlLabelClass?>">
									<?php echo JText::_('Bootstrap version'); ?>
								</div>
								<div class="<?php echo $controlsClass;?>">
									<?php echo $lists['bootstrap_version'];?>
								</div>
							</div>
							<?php
							if (version_compare(JVERSION, '3.0', 'ge'))
							{
							?>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('Load Bootstrap Twitter'); ?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php OSappscheduleConfiguration::showCheckboxfield('load_bootstrap',(int)$configs->load_bootstrap);?>
									</div>
								</div>
							<?php
							}
							?>
							<div class="<?php echo $controlGroupClass; ?>">
								<div class="<?php echo $controlLabelClass?>">
									<?php echo JText::_('OS_CONFIGURATION_DATE_TIME_FORMAT'); ?>
								</div>
								<div class="<?php echo $controlsClass;?>">
									<?php echo $lists['date_time_format']?>
								</div>
							</div>
							<div class="<?php echo $controlGroupClass; ?>">
								<div class="<?php echo $controlLabelClass?>">
									<?php echo JText::_('OS_CONFIGURATION_DATE_FORMAT'); ?>
								</div>
								<div class="<?php echo $controlsClass;?>">
									<?php echo $lists['date_format'] ?>
								</div>
							</div>
							<div class="<?php echo $controlGroupClass; ?>">
								<div class="<?php echo $controlLabelClass?>">
									<?php echo JText::_('OS_CONFIGURATION_TIME_FORMAT'); ?>
								</div>
								<div class="<?php echo $controlsClass;?>">
									<?php echo $lists['time_format'] ?>
								</div>
							</div>
							<div class="<?php echo $controlGroupClass; ?>">
								<div class="<?php echo $controlLabelClass?>">
									<?php echo JText::_('OS_CONFIGURATION_CURRENCY'); ?>
								</div>
								<div class="<?php echo $controlsClass;?>">
									<?php echo $lists['currency_format'];?>
								</div>
							</div>
							<div class="<?php echo $controlGroupClass; ?>">
								<div class="<?php echo $controlLabelClass?>">
									<?php echo JText::_('OS_DECIMAL_SEPARATOR'); ?>
								</div>
								<div class="<?php echo $controlsClass;?>">
									<?php echo $lists['decimal_separator'];?>
								</div>
							</div>
							<div class="<?php echo $controlGroupClass; ?>">
								<div class="<?php echo $controlLabelClass?>">
									<?php echo JText::_('OS_THOUSANDS_SEPARATOR'); ?>
								</div>
								<div class="<?php echo $controlsClass;?>">
									<?php echo $lists['thousands_separator'];?>
								</div>
							</div>
							<div class="<?php echo $controlGroupClass; ?>">
								<div class="<?php echo $controlLabelClass?>">
									<?php echo JText::_('OS_NUMBER_OF_DECIMAL_DIGITS'); ?>
								</div>
								<div class="<?php echo $controlsClass;?>">
									<input type="number" class="<?php echo $mapClass['input-mini']; ?> imini form-control" name="number_of_decimal_digits" id="number_of_decimal_digits" value="<?php echo (int)$configs->number_of_decimal_digits;?>" />
								</div>
							</div>
							<div class="<?php echo $controlGroupClass; ?>">
								<div class="<?php echo $controlLabelClass?>">
									<?php echo JText::_('OS_CURRENCY_SYMBOL_POSITION'); ?>
								</div>
								<div class="<?php echo $controlsClass;?>">
									<?php echo $lists['currency_symbol_position'];?>
								</div>
							</div>
							<div class="<?php echo $controlGroupClass; ?>">
								<div class="<?php echo $controlLabelClass?>">
									<?php echo JText::_('OS_CSV_SEPARATER'); ?>
								</div>
								<div class="<?php echo $controlsClass;?>">
									<?php echo $lists['csv_separator'] ?>
								</div>
							</div>
						</fieldset>
						<fieldset class="form-horizontal options-form">
							<legend><?php echo JText::_('GCalendar Integration Setting')?></legend>
							<div class="<?php echo $controlGroupClass; ?>">
								<div class="<?php echo $controlLabelClass?>">
									<?php echo JText::_('OS_INTEGRATE_GOOGLE_CALENDAR'); ?>
									<?php OSBHelper::generateTip(JText::_('OS_INTEGRATE_GOOGLE_CALENDAR_EXPLAIN'));?>
								</div>
								<div class="<?php echo $controlsClass;?>">
									<?php OSappscheduleConfiguration::showCheckboxfield('integrate_gcalendar',(int)$configs->integrate_gcalendar);?>
									<div class="clearfix"></div>
									<?php
									jimport('joomla.filesystem.folder');
									if($configs->integrate_gcalendar == 1 && !JFolder::exists(JPATH_ROOT."/libraries/osgcalendar"))
									{
									?>
									<span class="label label-important">
									In case you integrate OSB with Google Calendar,
									whenever customers make a booking request in your site, the GCalendar of employee will be added new event.
									Administrator have to enter the Google account for each employee.
									<BR />
									Google API must be installed on your server.
									</span>
									<div class="clearfix"></div>
									<?php
									}
									?>
									<span style="font-size:11px;color:red;">
									In case you integrate OSB with Google Calendar,
									whenever customers make a booking request in your site, the GCalendar of employee will be added new event.
									Administrator have to enter the Google account for each employee.
									<BR />
									Google API V3 must be installed on your server.
									<BR />
									You can download Google API V3 from <a href="https://github.com/google/google-api-php-client" target="_blank">here</a>
									</span>
								</div>
							</div>
							<?php
							if($configs->integrate_gcalendar == 1)
							{
							?>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('OS_GCALENDAR_WIDTH'); ?>
										<?php OSBHelper::generateTip(JText::_('OS_GCALENDAR_WIDTH_DESC'));?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<input type="text" class="<?php echo $mapClass['input-small']; ?>" size="4" name="gcalendar_width" value="<?php echo $configs->gcalendar_width?>" />
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('OS_GCALENDAR_HEIGHT'); ?>
										<?php OSBHelper::generateTip(JText::_('OS_GCALENDAR_HEIGHT_DESC'));?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<input type="text" class="<?php echo $mapClass['input-small']; ?>" size="4" name="gcalendar_height" value="<?php echo $configs->gcalendar_height?>" />
									</div>
								</div>
							<?php } ?>
						</fieldset>
					</div>
				</div>
				<?php echo JHtml::_($tabApiPrefix.'endTab') ?>
				<?php echo JHtml::_($tabApiPrefix.'addTab', 'configTab', 'reminder-page', JText::_('OS_CONFIGURATION_REMINDER')); ?>

					<table width="100%" class="admintable">
						<tr>
							<td class="key">
								<span class="editlinktip hasTooltip hasTip" title="<?php echo JText::_('OS_CONFIGURATION_ENABLE_NOTIFICATION')?>::<?php echo JText::_('OS_CONFIGURATION_ENABLE_NOTIFICATION_DESC')?>">
									<?php echo JText::_('OS_CONFIGURATION_ENABLE_NOTIFICATION')?>
								</span>
							</td>
							<td ><?php echo OSappscheduleConfiguration::showCheckboxfield('value_sch_reminder_enable',(int)$configs->value_sch_reminder_enable);?></td>
						</tr>
						<tr>
							<td class="key">
								<span class="editlinktip hasTooltip hasTip" title="<?php echo JText::_('OS_CONFIGURATION_EMAIL_SEND_REMINDER')?>::<?php echo JText::_('OS_CONFIGURATION_EMAIL_SEND_REMINDER_DESC')?>">
									<?php echo JText::_('OS_CONFIGURATION_EMAIL_SEND_REMINDER')?>
								</span>
							</td>
							<td >
								<input type="number" class="<?php echo $mapClass['input-small']; ?>" size="4" name="value_sch_reminder_email_before" value="<?php echo $configs->value_sch_reminder_email_before?>">
								<?php echo JText::_('OS_CONFIGURATION_HOURS_BEFORE')?>
							</td>
						</tr>
						<tr>
							<td class="key">
								<span class="editlinktip hasTooltip hasTip" title="<?php echo JText::_('OS_ALLOW_CUSTOMER_TO_SELECT_OPTION_TO_RECEIVE_REMINDER_DESC')?>">
									<?php echo JText::_('OS_ALLOW_CUSTOMER_TO_SELECT_OPTION_TO_RECEIVE_REMINDER')?>
								</span>
							</td>
							<td ><?php echo OSappscheduleConfiguration::showCheckboxfield('enable_reminder',(int)$configs->enable_reminder);?></td>
						</tr>
					</table>
				<?php echo JHtml::_($tabApiPrefix.'endTab') ?>
				<?php echo JHtml::_($tabApiPrefix.'addTab', 'configTab', 'booking-page', JText::_('OS_CONFIGURATION_BOOKING')); ?>
					<div class="<?php echo $mapClass['row-fluid'];?>">
						<div class="<?php echo $mapClass['span6'];?>">
							<fieldset class="form-horizontal options-form">
								<legend><?php echo JText::_('OS_USER_SETTING')?></legend>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('OS_EMPLOYEE_ACL_GROUP'); ?>
										<?php
										OSBHelper::generateTip(JText::_('OS_EMPLOYEE_ACL_GROUP_EXPLAIN'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php echo $lists['employee_acl_group'];?>
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('Employee can change availability status'); ?>
										<?php
										OSBHelper::generateTip(JText::_('Do you allow employee to change their availability status'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php OSappscheduleConfiguration::showCheckboxfield('employee_change_availability',(int)$configs->employee_change_availability);?>
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('OS_REQUIRED_LOGIN'); ?>
										<?php
										OSBHelper::generateTip(JText::_('OS_REQUIRED_LOGIN_EXPLAIN'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php echo $lists['allow_registered_only'] ?>
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('OS_SHOW_REGISTER_FORM'); ?>
										<?php
										OSBHelper::generateTip(JText::_('OS_SHOW_REGISTER_FORM_EXPLAIN'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php OSappscheduleConfiguration::showCheckboxfield('allow_registration',(int)$configs->allow_registration);?>
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('OS_SELECT_SPECIAL_GROUP'); ?>
										<?php
										OSBHelper::generateTip(JText::_('OS_SELECT_SPECIAL_GROUP_EXPLAIN'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php echo $lists['group_payment'] ?>
									</div>
								</div>
							</fieldset>

							<fieldset class="form-horizontal options-form">
								<legend><?php echo JText::_('OS_PAYMENT_SETTING')?></legend>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('OS_CONFIGURATION_DISABLE_PAYMENTS'); ?>
										<?php
										OSBHelper::generateTip(JText::_('OS_CONFIGURATION_DISABLE_PAYMENTS_DESC'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php OSappscheduleConfiguration::showCheckboxfield('disable_payments',(int)$configs->disable_payments, JText::_('JYES'), JText::_('JNO'))?>
									</div>
								</div>
								
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('OS_ENABLE_TAX')?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php OSappscheduleConfiguration::showCheckboxfield('enable_tax',(int)$configs->enable_tax);?>
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('OS_CONFIGURATION_TAX_PAYMENT')?>
										<?php
										OSBHelper::generateTip(JText::_('OS_CONFIGURATION_TAX_PAYMENT_DESC'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<input type="text" class="<?php echo $mapClass['input-small']; ?> imini" size="4" name="tax_payment" value="<?php echo $configs->tax_payment?>" />
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<?php echo JText::_('OS_CONFIGURATION_DEPOSIT_PAYMENT')?>
									<?php
									OSBHelper::generateTip(JText::_('OS_CONFIGURATION_DEPOSIT_PAYMENT_DESC'));
									?>
									<BR />
									<table width="100%" class="deposittable">
										<tr>
											<td colspan=2>
												<?php
												echo JText::_('OS_DEPOSIT_TYPE');
												?>
												:
												<?php
												echo $lists['deposit_type'];
												?>
											</td>
										</tr>
										<tr>
											<td>
												<?php
												echo JText::_('OS_PERCENTAGE_AMOUNT');	
												?>
												<input type="text" class="<?php echo $mapClass['input-small']; ?> imini" size="4" name="deposit_payment" value="<?php echo $configs->deposit_payment?>" />%
											</td>
											<td>
												<?php
												echo JText::_('OS_FLAT_RATE_AMOUNT');	
												?>
												<table width="100%" class="depositratetable">
													<tr>
														<td width="33%">
															<?php echo JText::_('OS_FROM');?>
														</td>
														<td width="33%">
															<?php echo JText::_('OS_TO');?>
														</td>
														<td width="33%">
															<?php echo JText::_('OS_RATE');?>
														</td>
													</tr>
													<?php
													for($i=1;$i<=5;$i++)
													{
														$from = (float)$configs->{'from_'.$i};
														$to   = (float)$configs->{'to_'.$i};
														$rate = (float)$configs->{'rate_'.$i};
														?>
														<tr>
															<td width="33%">
																<input type="text" name="from_<?php echo $i;?>" class="input-mini imini form-control" value="<?php echo $from; ?>" />
															</td>
															<td width="33%">
																<input type="text" name="to_<?php echo $i;?>" class="input-mini imini form-control" value="<?php echo $to; ?>" />
															</td>
															<td width="33%">
																<input type="text" name="rate_<?php echo $i;?>" class="input-mini imini form-control" value="<?php echo $rate; ?>" />
															</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
										</tr>
									</table>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('OS_ALL0W_PAY_FULL_AMOUNT')?>
										<?php
										OSBHelper::generateTip(JText::_('OS_ALL0W_PAY_FULL_AMOUNT_DESC'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php OSappscheduleConfiguration::showCheckboxfield('allow_full_payment',(int)$configs->allow_full_payment); ?>
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('OS_ENABLE_EARLY_BIRD')?>
										<?php
										OSBHelper::generateTip(JText::_('OS_ENABLE_EARLY_BIRD_DESC'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php OSappscheduleConfiguration::showCheckboxfield('early_bird',(int)$configs->early_bird);?>
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('OS_ENABLE_SLOTS_DISCOUNT')?>
										<?php
										OSBHelper::generateTip(JText::_('OS_ENABLE_EARLY_BIRD_DESC'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php OSappscheduleConfiguration::showCheckboxfield('enable_slots_discount',(int)$configs->enable_slots_discount);?>
									</div>
								</div>
							</fieldset>

							<fieldset class="form-horizontal options-form">
								<legend><?php echo JText::_('OS_OTHER_SETTING')?></legend>
								
								
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('OS_GENERATE_ICS_FILES'); ?>
										<?php
										OSBHelper::generateTip(JText::_('OS_GENERATE_ICS_FILES_EXPLAIN'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php OSappscheduleConfiguration::showCheckboxfield('generate_ics',(int)$configs->generate_ics); ?>
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('OS_SEND_ICS_FILE_TO_ADMINISTRATOR_AND_EMPLOYEE'); ?>
										<?php
										OSBHelper::generateTip(JText::_('OS_SEND_ICS_FILE_TO_ADMINISTRATOR_AND_EMPLOYEE_EXPLAIN'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php OSappscheduleConfiguration::showCheckboxfield('send_ics_to_administrator',(int)$configs->send_ics_to_administrator); ?>
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('OS_ACTIVATE_LINKED_SERVICE'); ?>
										<?php
										OSBHelper::generateTip(JText::_('OS_ACTIVATE_LINKED_SERVICE_EXPLAIN'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php OSappscheduleConfiguration::showCheckboxfield('active_linked_service',(int)$configs->active_linked_service); ?>
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
											<?php echo JText::_('OS_ALLOWED_FILE_TYPES'); ?>
										<?php
										OSBHelper::generateTip(JText::_('OS_ALLOWED_FILE_TYPES_EXPLAIN'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<input type="text" class="<?php echo $mapClass['input-large']; ?>" name="allowed_file_types" value="<?php echo $configs->allowed_file_types ? $configs->allowed_file_types : 'pdf,doc,docx,xls,xlsx'; ?>" />
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
											<?php echo JText::_('OS_ACTIVATE_COMMENT_REVIEW')?>
										<?php
										OSBHelper::generateTip(JText::_('OS_ACTIVATE_COMMENT_REVIEW_EXPLAIN'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php OSappscheduleConfiguration::showCheckboxfield('active_comment',(int)$configs->active_comment); ?>
									</div>
								</div>
								<?php
								$translatable = JLanguageMultilang::isEnabled() && count($languages);
								?>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
											<?php echo JText::_('OS_CHECKOUT_ITEMID'); ?>
										<?php
										OSBHelper::generateTip(JText::_('OS_CHECKOUT_ITEMID_EXPLAIN'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<input type="number" name="checkout_itemid" class="input-mini form-control ishort" value="<?php echo $configs->checkout_itemid ; ?>" />
									</div>
								</div>
								<?php
								if($translatable)
								{
									foreach ($languages as $language)
									{
										$sef = $language->sef;
										?>
										<div class="<?php echo $controlGroupClass; ?>">
											<div class="<?php echo $controlLabelClass?>">
												<?php echo JText::_('OS_CHECKOUT_ITEMID'); ?>
												&nbsp;
												<img src="<?php echo JURI::root(); ?>media/com_osservicesbooking/flags/<?php echo $sef; ?>.png" />
												<?php
												OSBHelper::generateTip(JText::_('OS_CHECKOUT_ITEMID_EXPLAIN'));
												?>
											</div>
											<div class="<?php echo $controlsClass;?>">
												<input type="number" name="checkout_itemid_<?php echo $sef; ?>" class="input-mini form-control ishort" value="<?php echo $configs->{'checkout_itemid_'.$sef} ; ?>" />
											</div>
										</div>
										<?php
									}
								}
								?>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">

										<?php echo JText::_('OS_BANNED_IP_ADDRESSES'); ?>
										<?php
										OSBHelper::generateTip(JText::_('OS_BANNED_IP_ADDRESSES_EXPLAIN'));
										?>

										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="red" class="bi bi-dash-circle-fill" viewBox="0 0 16 16">
										  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM4.5 7.5a.5.5 0 0 0 0 1h7a.5.5 0 0 0 0-1h-7z"/>
										</svg>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<textarea name="banned_ipaddress" class="input-large form-control ilarge"><?php echo $configs->banned_ipaddress ; ?></textarea>
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('OS_BANNED_USER'); ?>

										<?php
										OSBHelper::generateTip(JText::_('OS_BANNED_USER_EXPLAIN'));
										?>

										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="red" class="bi bi-dash-circle-fill" viewBox="0 0 16 16">
										  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM4.5 7.5a.5.5 0 0 0 0 1h7a.5.5 0 0 0 0-1h-7z"/>
										</svg>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<textarea name="banned_users" class="input-large form-control ilarge"><?php echo $configs->banned_users ; ?></textarea>
									</div>
								</div>
							</fieldset>
						</div>
						<div class="<?php echo $mapClass['span6'];?>">
							<fieldset class="form-horizontal options-form">
								<legend><?php echo JText::_('OS_BOOKING_SETTING')?></legend>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('Disable timeslot of service when one of employees is booked'); ?>
										<?php
										OSBHelper::generateTip(JText::_('In case your service has more than one employee, do you want to disable timeslot of service when one of employees is booked ?'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php
										OSappscheduleConfiguration::showCheckboxfield('disable_timeslot',(int)$configs->disable_timeslot);
										?>
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('Disable timeslots of venue when one of employees is booked'); ?>
										<?php
										OSBHelper::generateTip(JText::_('Do you want to disable timeslots of venue when one timeslot is booked ?'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php OSappscheduleConfiguration::showCheckboxfield('disable_venuetimeslot',(int)$configs->disable_venuetimeslot);?></td>
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('OS_APPLY_VENUE_FEATURE'); ?>
										<?php
										OSBHelper::generateTip(JText::_('OS_APPLY_VENUE_FEATURE_EXPLAIN'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php OSappscheduleConfiguration::showCheckboxfield('apply_venue',(int)$configs->apply_venue);?>
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('OS_MULTIPLE_WORK'); ?>
										<?php
										OSBHelper::generateTip(JText::_('OS_MULTIPLE_WORK_DESC'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php OSappscheduleConfiguration::showCheckboxfield('multiple_work',(int)$configs->multiple_work);?>
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('Limit one timeslot per order'); ?>
										
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php OSappscheduleConfiguration::showCheckboxfield('limit_one_timeslot',(int)$configs->limit_one_timeslot);?>
									</div>
								</div>
								
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('Prevent registered user from booking more than one timeslot per date')?>
										<?php
										OSBHelper::generateTip(JText::_('Do you want to prevent registered user to book more than one time-slot per date. Please remember that this feature only be applied to logged user only'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
									<?php OSappscheduleConfiguration::showCheckboxfield('booking_more_than_one',(int)$configs->booking_more_than_one);?>
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('Do you want to prevent a Joomla User/ Email to book more than x booking per date/week/month')?>
										<?php
										OSBHelper::generateTip(JText::_('Do you want to prevent a Joomla User/ Email to book more than x booking per date/week/month. This feature will applied to both logged or non logged user. Leave 0 (zero) with no limitation'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<input type="number" class="<?php echo $mapClass['input-small']; ?> imini" size="4" name="limit_booking" value="<?php echo (int) $configs->limit_booking ?>" style="display:inline;"/> <?php echo $lists['limit_by']; ?> <?php echo $lists['limit_type']; ?>
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('OS_CONFIGURATION_STEP')?>
										<?php
										OSBHelper::generateTip(JText::_('OS_CONFIGURATION_STEP_DESC'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php echo $lists['step_format'] ?>
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('OS_DEFAULT_ORDER_STATUS')?>
										<?php
										OSBHelper::generateTip(JText::_('OS_ENABLE_SLOTS_DISCOUNT_DESC'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php echo $lists['disable_payment_order_status']?>
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('OS_CONFIGURATION_EMAIL_CONFIRMATION')?>
										<?php
										OSBHelper::generateTip(JText::_('OS_CONFIGURATION_EMAIL_CONFIRMATION_DESC'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php echo $lists['value_enum_email_confirmation'] ?>
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('OS_CONFIGURATION_EMAIL_SEND_PAYMENTS')?>
										<?php
										OSBHelper::generateTip(JText::_('OS_CONFIGURATION_EMAIL_SEND_PAYMENTS_DESC'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php echo $lists['value_enum_email_payment'] ?>
									</div>
								</div>
								<?php
								if($configs->using_cart > 0)
								{
								?>
									<div class="<?php echo $controlGroupClass; ?>">
										<div class="<?php echo $controlLabelClass?>">
											<?php echo JText::_('Add multiple slots per - Add to cart - session'); ?>
											<?php
											OSBHelper::generateTip(JText::_('Do you allow customers add more than one timeslot per - Add to cart - session'));
											?>
										</div>
										<div class="<?php echo $controlsClass;?>">
											<?php OSappscheduleConfiguration::showCheckboxfield('allow_multiple_timeslots',(int)$configs->allow_multiple_timeslots);?>
										</div>
									</div>
								<?php
								}	
								?>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('OS_USE_QRCODE')?>
										<?php
										OSBHelper::generateTip(JText::_('OS_USE_QRCODE_EXPLAIN'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php OSappscheduleConfiguration::showCheckboxfield('use_qrcode',(int)$configs->use_qrcode); ?>
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('Turn on Waiting list')?>
										<?php
										OSBHelper::generateTip(JText::_("Enable this option if you want to allow customers to register into a waiting list of a certain service and with a certain employee. If you don't want to use the waiting list features, you can skip the following parameters"));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php OSappscheduleConfiguration::showCheckboxfield('waiting_list',(int)$configs->waiting_list); ?>
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('OS_REMOVE_CONFIRMATION_STEP')?>
										<?php
										OSBHelper::generateTip(JText::_("Normally, after Checkout step, you will see the Confirmation step where all booking information are shown. You can remove this step if you want"));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php OSappscheduleConfiguration::showCheckboxfield('remove_confirmation_step',(int)$configs->remove_confirmation_step); ?>
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">									
										<?php echo JText::_('OS_TEMPORARILY_LIVE_TIME'); ?>
										<?php
										OSBHelper::generateTip(JText::_('When the timeslot is added into cart, it will be hold within one time period, please enter the temporarily time length (in minutes)'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<input type="number" name="temporarily_time" class="input-mini form-control imini" value="<?php echo $configs->temporarily_time ? $configs->temporarily_time : 60; ?>" style="display:inline;" /> minutes
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">									
										<?php echo JText::_('OS_MIN_CHECK_IN_DATE'); ?>
										<?php
										OSBHelper::generateTip(JText::_('OS_MIN_CHECK_IN_DATE_EXPLAIN'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<input type="number" name="min_check_in" class="input-mini form-control imini" value="<?php echo $configs->min_check_in; ?>" style="display:inline;" /> days
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">									
										<?php echo JText::_('OS_MAX_CHECK_IN_DATE'); ?>
										<?php
										OSBHelper::generateTip(JText::_('OS_MAX_CHECK_IN_DATE_EXPLAIN'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<input type="number" name="max_check_in" class="input-mini form-control imini" value="<?php echo $configs->max_check_in; ?>" style="display:inline;" /> days
									</div>
								</div>
							</fieldset>

							<fieldset class="form-horizontal options-form">
								<legend><?php echo JText::_('OS_ORDERS_REMOVAL_SETTING')?></legend>
								
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('Allow customers to cancel the booking request'); ?>
										<?php
										OSBHelper::generateTip(JText::_('Do you allow customers to cancel the booking request'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php	
										OSappscheduleConfiguration::showCheckboxfield('allow_cancel_request',(int)$configs->allow_cancel_request);?>
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('Allow customers to remove order items'); ?>
										<?php
										OSBHelper::generateTip(JText::_('In each booking request, there are one or more order items, do you allow customers to remove order items'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php OSappscheduleConfiguration::showCheckboxfield('allow_remove_items',(int)$configs->allow_remove_items);?>
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('OS_CANCEL_BOOKING_REQUEST_BEFORE'); ?>
										<?php
										OSBHelper::generateTip(JText::_('OS_ALLOW_CANCEL_BOOKING_REQUEST_BEFORE_EXPLAIN'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<input type="number" name="cancel_before" class="input-mini form-control imini" value="<?php echo $configs->cancel_before ? $configs->cancel_before : 1; ?>"  style="display:inline;" />
										<?php echo JText::_('OS_ALLOW_CANCEL_BOOKING_REQUEST_BEFORE_EXPLAIN1'); ?>
									</div>
								</div>
								
								
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<span class="editlinktip hasTooltip hasTip" title="<?php echo JText::_('Auto remove pending orders')?>::<?php echo JText::_("Do you want to remove pending orders after a time period")?>">
											<?php echo JText::_('Auto remove pending orders')?>
										</span>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php OSappscheduleConfiguration::showCheckboxfield('remove_pending_orders',(int)$configs->remove_pending_orders); ?>
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('OS_REMOVE_PENDING_ORDERS_AFTER'); ?>
										<?php
										OSBHelper::generateTip(JText::_('Please enter number hours that the pending orders will be cancelled'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<input type="number" name="pending_remove" class="input-mini form-control imini" value="<?php echo $configs->pending_remove ? $configs->pending_remove : 3; ?>" style="display:inline;" /> hours
									</div>
								</div>
							</fieldset>
						</div>
					</div>
				<?php echo JHtml::_($tabApiPrefix.'endTab') ?>
				<?php echo JHtml::_($tabApiPrefix.'addTab', 'configTab', 'formfields', JText::_('OS_FORM_FIELDS')); ?>
			
					<table class="admintable adminform" style="width:100%;">
						<tr>
							<td class="key">
								<span class="editlinktip hasTooltip hasTip" title="<?php echo JText::_('OS_FIELDS_INTEGRATION')?>::<?php echo JText::_('OS_FIELDS_INTEGRATION_DESC')?>">
									<?php echo JText::_('OS_FIELDS_INTEGRATION')?>
								</span>
							</td>
							<td >
							<?php 
								echo $lists['field_integration'];
							?>
							</td>
						</tr>
						<tr>
							<td class="key">
								<span class="editlinktip hasTooltip hasTip" title="<?php echo JText::_('OS_CONFIGURATION_PHONE')?>::<?php echo JText::_('OS_CONFIGURATION_PHONE_DESC')?>">
									<?php echo JText::_('OS_CONFIGURATION_PHONE')?>
								</span>
							</td>
							<td ><?php echo $lists['value_sch_include_phone']  ?></td>
							<?php
							if($configs->field_integration > 0)
							{
								?>
								<td width="60%">
									<?php echo JText::_('OS_FIELD_MAPPING');?>: 
									<?php
									echo JHTML::_('select.genericlist',$lists['fieldMapping'],'phone_mapping', ' class="input-large form-select imedium" ','value','text', $configs->phone_mapping);
									?>
								</td>
								<?php
							}
							?>
						</tr>
						<tr>
							<td  class="key">
								<span class="editlinktip hasTooltip hasTip" title="<?php echo JText::_('OS_DEFAULT_DIALING_CODE')?>::<?php echo JText::_('OS_DEFAULT_DIALING_CODE_EXPLAIN')?>">
									<?php echo JText::_('OS_DEFAULT_DIALING_CODE'); ?>
								</span>
							</td>
							<td colspan="2">
								<?php echo $lists['dial']; ?>
							</td>
						</tr>
						<tr>
							<td  class="key">
								<span class="editlinktip hasTooltip hasTip" title="<?php echo JText::_('OS_SHOW_CODE_LIST')?>::<?php echo JText::_('OS_SHOW_CODE_LIST_EXPLAIN')?>">
									<?php echo JText::_('OS_SHOW_CODE_LIST'); ?>
								</span>
							</td>
							<td colspan="2">
								<?php OSappscheduleConfiguration::showCheckboxfield('clickatell_showcodelist',(int)$configs->clickatell_showcodelist); ?>
							</td>
						</tr>
						<tr>
							<td class="key">
								<span class="editlinktip hasTooltip hasTip" title="<?php echo JText::_('OS_CONFIGURATION_COUNTRY')?>::<?php echo JText::_('OS_CONFIGURATION_COUNTRY_DESC')?>">
									<?php echo JText::_('OS_CONFIGURATION_COUNTRY')?>
								</span>
							</td>
							<td ><?php echo $lists['value_sch_include_country']  ?></td>
							<?php
							if($configs->field_integration > 0)
							{
								?>
								<td width="60%">
									<?php echo JText::_('OS_FIELD_MAPPING');?>: 
									<?php
									echo JHTML::_('select.genericlist',$lists['fieldMapping'],'country_mapping', ' class="input-large form-select imedium" ','value','text', $configs->country_mapping);
									?>
								</td>
								<?php
							}
							?>
						</tr>
						<tr>
							<td class="key">
								<span class="editlinktip hasTooltip hasTip" title="<?php echo JText::_('OS_DEFAULT_COUNTRY')?>::<?php echo JText::_('OS_DEFAULT_COUNTRY_DESC')?>">
									<?php echo JText::_('OS_DEFAULT_COUNTRY')?>
								</span>
							</td>
							<td ><?php echo $lists['country']  ?></td>
						</tr>
						<tr>
							<td class="key" >
								<span class="editlinktip hasTooltip hasTip" title="<?php echo JText::_('OS_CONFIGURATION_CITY')?>::<?php echo JText::_('OS_CONFIGURATION_CITY_DESC')?>">
									<?php echo JText::_('OS_CONFIGURATION_CITY')?>
								</span>
							</td>
							<td ><?php echo $lists['value_sch_include_city']?></td>
							<?php
							if($configs->field_integration > 0)
							{
								?>
								<td width="60%">
									<?php echo JText::_('OS_FIELD_MAPPING');?>: 
									<?php
									echo JHTML::_('select.genericlist',$lists['fieldMapping'],'city_mapping', ' class="input-large form-select imedium" ','value','text', $configs->city_mapping);
									?>
								</td>
								<?php
							}
							?>
						</tr>
						<tr>
							<td class="key" >
								<span class="editlinktip hasTooltip hasTip" title="<?php echo JText::_('OS_CONFIGURATION_STATE')?>::<?php echo JText::_('OS_CONFIGURATION_STATE_DESC')?>">
									<?php echo JText::_('OS_CONFIGURATION_STATE')?>
								</span>
							</td>
							<td ><?php echo $lists['value_sch_include_state'] ?></td>
							<?php
							if($configs->field_integration > 0)
							{
								?>
								<td width="60%">
									<?php echo JText::_('OS_FIELD_MAPPING');?>: 
									<?php
									echo JHTML::_('select.genericlist',$lists['fieldMapping'],'state_mapping', ' class="input-large form-select imedium" ','value','text', $configs->state_mapping);
									?>
								</td>
								<?php
							}
							?>
						</tr>
						<tr>
							<td class="key">
								<span class="editlinktip hasTooltip hasTip" title="<?php echo JText::_('OS_CONFIGURATION_ZIP')?>::<?php echo JText::_('OS_CONFIGURATION_ZIP_DESC')?>">
									<?php echo JText::_('OS_CONFIGURATION_ZIP')?>
								</span>
							</td>
							<td ><?php echo $lists['value_sch_include_zip'] ?></td>
							<?php
							if($configs->field_integration > 0)
							{
								?>
								<td width="60%">
									<?php echo JText::_('OS_FIELD_MAPPING');?>: 
									<?php
									echo JHTML::_('select.genericlist',$lists['fieldMapping'],'zip_mapping', ' class="input-large form-select imedium" ','value','text', $configs->zip_mapping);
									?>
								</td>
								<?php
							}
							?>
						</tr>
						<tr>
							<td class="key">
								
								<span class="editlinktip hasTooltip hasTip" title="<?php echo JText::_('OS_CONFIGURATION_ADDRESS')?>::<?php echo JText::_('OS_CONFIGURATION_ADDRESS_DESC')?>">
									<?php echo JText::_('OS_CONFIGURATION_ADDRESS')?>
								</span>
							</td>
							<td ><?php echo $lists['value_sch_include_address'] ?></td>
							<?php
							if($configs->field_integration > 0)
							{
								?>
								<td width="60%">
									<?php echo JText::_('OS_FIELD_MAPPING');?>: 
									<?php
									echo JHTML::_('select.genericlist',$lists['fieldMapping'],'address_mapping', ' class="input-large form-select imedium" ','value','text', $configs->address_mapping);
									?>
								</td>
								<?php
							}
							?>
						</tr>
						<tr>
							<td class="key">
								<span class="editlinktip hasTooltip hasTip" title="<?php echo JText::_('OS_ENABLE_NOTE_FIELD')?>">
									<?php echo JText::_('OS_ENABLE_NOTE_FIELD')?>
								</span>
							</td>
							<td >
								<?php echo $lists['value_sch_include_notes'] ?>
							</td>
						</tr>
						<tr>
							<td class="key">
								<span class="editlinktip hasTooltip hasTip" title="<?php echo JText::_('OS_CONFIGURATION_CAPCHA')?>::<?php echo JText::_('OS_CONFIGURATION_CAPCHA_DESC')?>">
									<?php echo JText::_('OS_CONFIGURATION_CAPCHA')?>
								</span>
							</td>
							<td ><?php echo $lists['value_sch_include_captcha'] ?></td>
						</tr>
						<tr>
							<td class="key">
								<span class="editlinktip hasTooltip hasTip" title="<?php echo JText::_('Bypass captcha for registered users')?>::<?php echo JText::_("If set to Yes, registered users won't have to enter captcha code in registration process")?>">
									<?php echo JText::_('Bypass captcha for registered users')?>
								</span>
							</td>
							<td ><?php echo $lists['pass_captcha'] ?></td>
						</tr>
						<tr>
							<td class="key">
								
								<span class="editlinktip hasTooltip hasTip" title="<?php echo JText::_('OS_ENABLE_TERM_AND_CONDITION')?>">
									<?php echo JText::_('OS_ENABLE_TERM_AND_CONDITION')?>
								</span>
							</td>
							<td >
								<?php echo $lists['enable_termandcondition'] ?>
							</td>
						</tr>
						<tr>
							<td class="key">
								
								<span class="editlinktip hasTooltip hasTip" title="<?php echo JText::_('OS_SELECT_ARTICLE')?>">
									<?php echo JText::_('OS_SELECT_ARTICLE')?>
								</span>
							</td>
							<td >
								<?php echo $lists['article_id'] ?>
							</td>
						</tr>
					</table>
				<?php echo JHtml::_($tabApiPrefix.'endTab') ?>
				<?php echo JHtml::_($tabApiPrefix.'addTab', 'configTab', 'invoice-setting', JText::_('OS_CONFIGURATION_INVOICE_SETTINGS')); ?>
					<div class="<?php echo $mapClass['row-fluid'];?>">
						<div class="<?php echo $mapClass['span12'];?>">
							<fieldset class="form-horizontal options-form">
								<legend><?php echo JText::_('OS_INVOICE_SETTING')?></legend>
								
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
											<?php echo JText::_('OS_CONFIGURATION_ACTIVATE_INVOICE_FEATURE'); ?>
										<?php
										OSBHelper::generateTip(JText::_('OS_CONFIGURATION_ACTIVATE_INVOICE_FEATURE_EXPLAIN'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php OSappscheduleConfiguration::showCheckboxfield('activate_invoice_feature',(int)$configs->activate_invoice_feature); ?>
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
											<?php echo JText::_('Send Invoice to Customer'); ?>
										<?php
										OSBHelper::generateTip(JText::_('OS_CONFIGURATION_SEND_INVOICE_TO_ORDER_EXPLAIN'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php OSappscheduleConfiguration::showCheckboxfield('send_invoice_to_customer',(int)$configs->send_invoice_to_customer) ; ?>
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
											<?php echo JText::_('OS_SEND_INVOICE_TO_ADMINISTRATOR')?>
										<?php
										OSBHelper::generateTip(JText::_('Do you want to send invoice to admin when order is completed'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php OSappscheduleConfiguration::showCheckboxfield('send_invoice_to_admin',(int)$configs->send_invoice_to_admin); ?>
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
											<?php echo JText::_('Invoice will be sent when order status is'); ?>
										<?php
										OSBHelper::generateTip(JText::_('Select order status that you want to send the invoice'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php echo $lists['send_invoice'] ; ?>
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
											<?php echo JText::_('OS_SEND_INVOICE_IN_PAYMENT_NOTIFICATION_EMAIL')?>
										<?php
										OSBHelper::generateTip(JText::_('Do you want to include invoice in Payment notification email'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php OSappscheduleConfiguration::showCheckboxfield('send_invoice_in_payment_email',(int)$configs->send_invoice_in_payment_email); ?>
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
											<?php echo JText::_('OS_CONFIGURATION_INVOICE_START_NUMBER'); ?>
										<?php
										OSBHelper::generateTip(JText::_('OS_CONFIGURATION_INVOICE_START_NUMBER_EXPLAIN'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<input type="number" name="invoice_start_number" class="input-mini form-control" value="<?php echo $configs->invoice_start_number ? $configs->invoice_start_number : 1; ?>" size="10" />
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
											<?php echo JText::_('OS_CONFIGURATION_INVOICE_PREFIX'); ?>
										<?php
										OSBHelper::generateTip(JText::_('OS_CONFIGURATION_INVOICE_PREFIX_DESC'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<input type="text" name="invoice_prefix" class="input-mini form-control" value="<?php echo isset($configs->invoice_prefix) ? $configs->invoice_prefix : 'IV'; ?>" size="10" />
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
											<?php echo JText::_('OS_CONFIGURATION_INVOICE_NUMBER_LENGTH'); ?>
										<?php
										OSBHelper::generateTip(JText::_('OS_CONFIGURATION_INVOICE_NUMBER_LENGTH_EXPLAIN'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<input type="number" name="invoice_number_length" class="input-mini form-control" value="<?php echo $configs->invoice_number_length ? $configs->invoice_number_length : 5; ?>" size="10" />
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
											<?php echo JText::_('Reset invoice number Every Year')?>
										<?php
										OSBHelper::generateTip(JText::_('If set to Yes, invoice number will be reset every year'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php OSappscheduleConfiguration::showCheckboxfield('reset_invoice',(int)$configs->reset_invoice); ?>								
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('OS_PDF_FONT'); ?>
										<?php
										OSBHelper::generateTip(JText::_('OS_PDF_FONT_EXPLAIN'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php echo $lists['pdf_font']; ?>
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
											<?php echo JText::_('OS_CONFIGURATION_INVOICE_FORMAT'); ?>
										<?php
										OSBHelper::generateTip(JText::_('OS_CONFIGURATION_INVOICE_FORMAT_DESC'));
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php echo $editor->display( 'invoice_format',  $configs->invoice_format , '100%', '550', '75', '8' ) ;?>
									</div>
								</div>
							</fieldset>
						</div>
					</div>
					
						
				<?php echo JHtml::_($tabApiPrefix.'endTab') ?>
				<?php echo JHtml::_($tabApiPrefix.'addTab', 'configTab', 'clickatell-setting', JText::_('OS_SMS_SETTING')); ?>
						<div class="<?php echo $mapClass['row-fluid'];?>">
							<div class="<?php echo $mapClass['span12'];?>">
								<fieldset class="form-horizontal options-form">
									<legend>
										<?php echo JText::_('OS_AVAILABLE_TAGS');?>
									</legend>
									<?php
									$tag = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-tag" viewBox="0 0 16 16">
  <path d="M6 4.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm-1 0a.5.5 0 1 0-1 0 .5.5 0 0 0 1 0z"/>
  <path d="M2 1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 1 6.586V2a1 1 0 0 1 1-1zm0 5.586 7 7L13.586 9l-7-7H2v4.586z"/>
</svg>';
									?>
									<div class="<?php echo $mapClass['row-fluid'];?>">
										<div class="<?php echo $mapClass['span2'];?>">
											<?php echo $tag; ?>{OrderID}
										</div>
										<div class="<?php echo $mapClass['span2'];?>">
											 <?php echo $tag; ?>{User}
										</div>
										<div class="<?php echo $mapClass['span2'];?>">
											 <?php echo $tag; ?>{Email}
										</div>
										<div class="<?php echo $mapClass['span2'];?>">
											<?php echo $tag; ?>{business_name}
										</div>
										<div class="<?php echo $mapClass['span2'];?>">
											 <?php echo $tag; ?>{OrderStatus}
										</div>
										<div class="<?php echo $mapClass['span2'];?>">
											<?php echo $tag; ?>{Name}
										</div>
									</div>
									<div class="<?php echo $mapClass['row-fluid'];?>">
										<div class="<?php echo $mapClass['span2'];?>">
											 <?php echo $tag; ?>{Tel}
										</div>
										<div class="<?php echo $mapClass['span2'];?>">
											<?php echo $tag; ?>{Address}
										</div>
										<div class="<?php echo $mapClass['span2'];?>">
											<?php echo $tag; ?>{Message}
										</div>
										<div class="<?php echo $mapClass['span2'];?>">
											<?php echo $tag; ?>{Time}
										</div>
										<div class="<?php echo $mapClass['span2'];?>">
											<?php echo $tag; ?>{Orders_details}
										</div>
									</div>
									    
								</fieldset>
							</div>
						</div>
						<div class="<?php echo $mapClass['row-fluid'];?>">
							<div class="<?php echo $mapClass['span4'];?>">
								<fieldset class="form-horizontal options-form">
									<legend><?php echo JText::_('OS_NEW_BOOKING'). ' - '.JText::_('OS_FOR_ADMIN');?></legend>
									<textarea type="text" class="<?php echo $mapClass['input-large']; ?>" name="sms_new_booking_to_admin" id="sms_new_booking_to_admin" style="width:400px;"><?php echo $configs->sms_new_booking_to_admin?></textarea>
									<BR />
									
									
									<?php echo JText::_('OS_ACTIVATE_SMS');?>
									<?php OSappscheduleConfiguration::showCheckboxfield('sms_new_booking_to_admin_checkbox',(int)$configs->sms_new_booking_to_admin_checkbox); ?>
								</fieldset>
							</div>
							<div class="<?php echo $mapClass['span4'];?>">
								<fieldset class="form-horizontal options-form">
									<legend><?php echo JText::_('OS_NEW_BOOKING'). ' - '.JText::_('OS_FOR_CUSTOMER');?></legend>
									<textarea type="text" class="<?php echo $mapClass['input-large']; ?>" name="sms_new_booking_to_customer" id="sms_new_booking_to_customer" style="width:400px;"><?php echo $configs->sms_new_booking_to_customer?></textarea>
									<BR />
									
									
									<?php echo JText::_('OS_ACTIVATE_SMS');?>
									<?php OSappscheduleConfiguration::showCheckboxfield('sms_new_booking_to_customer_checkbox',(int)$configs->sms_new_booking_to_customer_checkbox); ?>
								</fieldset>
							</div>
							<div class="<?php echo $mapClass['span4'];?>">
								<fieldset class="form-horizontal options-form">
									<legend><?php echo JText::_('OS_NEW_BOOKING'). ' - '.JText::_('OS_FOR_EMPLOYEE');?></legend>
									<textarea type="text" class="<?php echo $mapClass['input-large']; ?>" name="sms_new_booking_to_employee" id="sms_new_booking_to_employee" style="width:400px;"><?php echo $configs->sms_new_booking_to_employee?></textarea>
									<BR />
									
									
									<?php echo JText::_('OS_ACTIVATE_SMS');?>
									<?php OSappscheduleConfiguration::showCheckboxfield('sms_new_booking_to_employee_checkbox',(int)$configs->sms_new_booking_to_employee_checkbox); ?>
								</fieldset>
							</div>
						</div>
						<div class="<?php echo $mapClass['row-fluid'];?>">
							<div class="<?php echo $mapClass['span4'];?>">
								<fieldset class="form-horizontal options-form">
									<legend><?php echo JText::_('OS_PAYMENT_COMPLETE'). ' - '.JText::_('OS_FOR_ADMIN');?></legend>
									<textarea type="text" class="<?php echo $mapClass['input-large']; ?>" name="sms_payment_complete_to_admin" id="sms_payment_complete_to_admin"  style="width:400px;"><?php echo $configs->sms_payment_complete_to_admin?></textarea>
									<BR />
									
								
									<?php echo JText::_('OS_ACTIVATE_SMS');?>
									<?php OSappscheduleConfiguration::showCheckboxfield('sms_payment_complete_to_admin_checkbox',(int)$configs->sms_payment_complete_to_admin_checkbox); ?>
								</fieldset>
							</div>
							<div class="<?php echo $mapClass['span4'];?>">
								<fieldset class="form-horizontal options-form">
									<legend><?php echo JText::_('OS_PAYMENT_COMPLETE'). ' - '.JText::_('OS_FOR_CUSTOMER');?></legend>
									<textarea type="text" class="<?php echo $mapClass['input-large']; ?>" name="sms_payment_complete_to_customer" id="sms_payment_complete_to_customer"  style="width:400px;" ><?php echo $configs->sms_payment_complete_to_customer;?></textarea>
									<BR />
									
								
									<?php echo JText::_('OS_ACTIVATE_SMS');?>
									<?php OSappscheduleConfiguration::showCheckboxfield('sms_payment_complete_to_customer_checkbox',(int)$configs->sms_payment_complete_to_customer_checkbox); ?>
								</fieldset>
							</div>
							<div class="<?php echo $mapClass['span4'];?>">
								<fieldset class="form-horizontal options-form">
									<legend><?php echo JText::_('OS_PAYMENT_COMPLETE'). ' - '.JText::_('OS_FOR_EMPLOYEE');?></legend>
									<textarea type="text" class="<?php echo $mapClass['input-large']; ?>" name="sms_payment_complete_to_employee" id="sms_payment_complete_to_employee" style="width:400px;" ><?php echo $configs->sms_payment_complete_to_employee;?></textarea>
									<BR />
									
									
									<?php echo JText::_('OS_ACTIVATE_SMS');?>
									<?php OSappscheduleConfiguration::showCheckboxfield('sms_payment_complete_to_employee_checkbox',(int)$configs->sms_payment_complete_to_employee_checkbox); ?>
								</fieldset>
							</div>
						</div>
						<div class="<?php echo $mapClass['row-fluid'];?>">
							<div class="<?php echo $mapClass['span4'];?>">
								<fieldset class="form-horizontal options-form">
									<legend><?php echo JText::_('OS_BOOKING_REMINDER'). ' - '.JText::_('OS_FOR_CUSTOMER');?></legend>
									<textarea type="text" class="<?php echo $mapClass['input-large']; ?>" name="sms_reminder_notification" id="sms_reminder_notification" style="width:400px;" ><?php echo $configs->sms_reminder_notification?></textarea>
									<BR />
									
									
									<?php echo JText::_('OS_ACTIVATE_SMS');?>
									<?php OSappscheduleConfiguration::showCheckboxfield('sms_reminder_notification_checkbox',(int)$configs->sms_reminder_notification_checkbox); ?>
								</fieldset>
							</div>
							<div class="<?php echo $mapClass['span4'];?>">
								<fieldset class="form-horizontal options-form">
									<legend><?php echo JText::_('OS_BOOKING_CANCELLED'). ' - '.JText::_('OS_FOR_ADMIN');?></legend>
									<textarea type="text" class="<?php echo $mapClass['input-large']; ?>" name="sms_order_cancelled_notification" id="sms_order_cancelled_notification"  style="width:400px;" ><?php echo $configs->sms_order_cancelled_notification?></textarea>
									<BR />
									
									
									<?php echo JText::_('OS_ACTIVATE_SMS');?>
									<?php OSappscheduleConfiguration::showCheckboxfield('sms_order_cancelled_notification_checkbox',(int)$configs->sms_order_cancelled_notification_checkbox); ?>
								</fieldset>
							</div>
							<div class="<?php echo $mapClass['span4'];?>">
								<fieldset class="form-horizontal options-form">
									<legend><?php echo JText::_('OS_BOOKING_CANCELLED'). ' - '.JText::_('OS_FOR_EMPLOYEE');?></legend>
									<textarea type="text" class="<?php echo $mapClass['input-large']; ?>" name="sms_order_cancelled_notification_employee" id="sms_order_cancelled_notification_employee"  style="width:400px;" ><?php echo $configs->sms_order_cancelled_notification_employee?></textarea>
									<BR />
									
									
									<?php echo JText::_('OS_ACTIVATE_SMS');?>
									<?php OSappscheduleConfiguration::showCheckboxfield('sms_order_cancelled_notification_employee_checkbox',(int)$configs->sms_order_cancelled_notification_employee_checkbox); ?>
								</fieldset>
							</div>
						</div>
						<div class="<?php echo $mapClass['row-fluid'];?>">
							<div class="<?php echo $mapClass['span4'];?>">
								<fieldset class="form-horizontal options-form">
									<legend><?php echo JText::_('OS_ORDER_STATUS_CHANGED'). ' - '.JText::_('OS_FOR_CUSTOMER');?></legend>
									<textarea type="text" class="<?php echo $mapClass['input-large']; ?>" name="order_status_changed_to_customer" id="order_status_changed_to_customer" style="width:400px;" ><?php echo $configs->order_status_changed_to_customer?></textarea>
									<BR />
									
									
									<?php echo JText::_('OS_ACTIVATE_SMS');?>
									<?php OSappscheduleConfiguration::showCheckboxfield('order_status_changed_to_customer_checkbox',(int)$configs->order_status_changed_to_customer_checkbox); ?>
								</fieldset>
							</div>
						</div>
						
					<?php echo JHtml::_($tabApiPrefix.'endTab') ?>
					<?php echo JHtml::_($tabApiPrefix.'addTab', 'configTab', 'layout-setting', JText::_('OS_LAYOUT_SETTING')); ?>
						<table class="admintable adminform" style="width:100%;">
							<tr>
								<td class="key">
									<span class="editlinktip hasTooltip hasTip" title="<?php echo JText::_('OS_CALENDAR_AND_CART_SIDE_EXPLAIN')?>">
										<?php echo JText::_('OS_CALENDAR_AND_CART_SIDE')?>
									</span>
								</td>
								<td colspan="2">
									<?php echo $lists['calendar_position']; ?>
								</td>
							</tr>
							<?php
							if($configs->using_cart > 0)
							{
							?>
							<tr>
								<td class="key">
									<span class="editlinktip hasTooltip hasTip" title="<?php echo JText::_('Do you want to show Tax amount in cart')?>">
										<?php echo JText::_('OS_SHOW_TAX_IN_CART')?>
									</span>
								</td>
								<td colspan="2">
									<?php OSappscheduleConfiguration::showCheckboxfield('show_tax_in_cart',(int)$configs->show_tax_in_cart); ?>
								</td>
							</tr>
							<?php } ?>
							<tr>
								<td class="key">
									<span class="editlinktip hasTooltip hasTip" title="<?php echo JText::_('Show Service Cost including Tax')?>">
										<?php echo JText::_('Service Cost including Tax')?>
									</span>
								</td>
								<td colspan="2">
									<?php OSappscheduleConfiguration::showCheckboxfield('show_service_cost_including_tax',(int)$configs->show_service_cost_including_tax); ?>
								</td>
							</tr>
							<tr>
								<td class="key">
									<span class="editlinktip hasTooltip hasTip" title="<?php echo JText::_('Disable Calendar on Non Available Employees date')?>::<?php echo JText::_('Disable Calendar on Non Available Employees date')?>">
										<?php echo JText::_('Disable Calendar on Non Available Employees date')?>
									</span>
								</td>
								<td colspan="2">
									<?php OSappscheduleConfiguration::showCheckboxfield('disable_calendar_in_off_date',(int)$configs->disable_calendar_in_off_date); ?>
								</td>
							</tr>
							<tr>
								<td class="key">
									<span class="editlinktip hasTooltip hasTip" title="<?php echo JText::_('Skip unavailable dates')?>::<?php echo JText::_('Do you want to skip unavailable dates in month to go to available dates. For example: There are not any available timeslots today, but on tomorrow, there are available timeslots. Then, the calendar will start on tomorrow.')?>">
										<?php echo JText::_('Skip unavailable dates')?>
									</span>
								</td>
								<td colspan="2">
									<?php OSappscheduleConfiguration::showCheckboxfield('skip_unavailable_dates',(int)$configs->skip_unavailable_dates); ?>
								</td>
							</tr>
							<tr>
								<td class="key">
									<span class="editlinktip hasTooltip hasTip" title="<?php echo JText::_('Show Occupied Time Slots')?>::<?php echo JText::_('Do you want to show Occupied Time slots in Booking table')?>">
										<?php echo JText::_('Show Occupied Time Slots')?>
									</span>
								</td>
								<td colspan="2">
									<?php OSappscheduleConfiguration::showCheckboxfield('show_occupied',(int)$configs->show_occupied); ?>
								</td>
							</tr>
							<tr>
								<td class="key">
									<span class="editlinktip hasTooltip hasTip" title="<?php echo JText::_('Show JS Popup at Front-end')?>::<?php echo JText::_('Do you want to show JS Popup at front-end of component?')?>">
										<?php echo JText::_('Show JS Popup at Front-end')?>
									</span>
								</td>
								<td colspan="2">
									<?php OSappscheduleConfiguration::showCheckboxfield('use_js_popup',(int)$configs->use_js_popup); ?>
								</td>
							</tr>
							<tr>
								<td class="key">
									<span class="editlinktip hasTooltip hasTip" title="<?php echo JText::_('OS_SHOW_TOTAL_AMOUNT_IN_CHECKOUT_PAGE')?>::<?php echo JText::_('OS_SHOW_TOTAL_AMOUNT_IN_CHECKOUT_PAGE_EXPLAIN')?>">
										<?php echo JText::_('OS_SHOW_TOTAL_AMOUNT_IN_CHECKOUT_PAGE')?>
									</span>
								</td>
								<td colspan="2">
									<?php OSappscheduleConfiguration::showCheckboxfield('show_total_amount',(int)$configs->show_total_amount); ?>
								</td>
							</tr>
							<tr>
								<td  class="key" width="40%">
									<span class="editlinktip hasTooltip hasTip" title="<?php echo JText::_('Using Cart box')?>::<?php echo JText::_('Do you want to use Cart Box')?>">
										<?php echo JText::_('Using Cart box'); ?>
									</span>
								</td>
								<td colspan="2">
									<?php echo $lists['using_cart']; ?>
								</td>
							</tr>
							<tr>
								<td  class="key" width="40%">
									<span class="editlinktip hasTooltip hasTip" title="<?php echo JText::_('OS_HIDE_CALENDAR_BOX')?>::<?php echo JText::_('OS_HIDE_CALENDAR_BOX_EXPLAIN')?>">
										<?php echo JText::_('OS_HIDE_CALENDAR_BOX'); ?>
									</span>
								</td>
								<td colspan="2">
									<?php echo $lists['show_calendar_box']; ?>
								</td>
							</tr>
							<tr>
								<td class="key">
									<span class="editlinktip hasTooltip hasTip" title="<?php echo JText::_('OS_CALENDAR_START_DATE')?>">
										<?php echo JText::_('OS_CALENDAR_START_DATE')?>
									</span>
								</td>
								<td colspan="2"><?php echo $lists['start_day_in_week'] ?></td>
							</tr>
							<tr>
								<td class="key">
									<span class="editlinktip hasTooltip hasTip" title="<?php echo JText::_('OS_SHOW_SERVICES_AND_EMPLOYEES_IN')?>">
										<?php echo JText::_('OS_SHOW_SERVICES_AND_EMPLOYEES_IN')?>
									</span>
								</td>
								<td colspan="2"><?php echo $lists['usingtab'] ?></td>
							</tr>
							<tr>
								<td  class="key" width="20%" valign="top">
									<?php echo JText::_('OS_SELECT_TIMESLOT_THEME');?>
								</td>
								<td width="30%" valign="top">
									<table width="100%">
										<?php
										echo $lists['booking_theme'];
										?>
									</table>
								</td>
								<td width="50%" valign="top">
									Radio timeslots theme
									<BR />
									<img src="<?php echo JURI::base()?>components/com_osservicesbooking/asset/images/radio_timeslot.png"  style="border:2px solid red; "/>
									<BR />
									Simple timeslots theme
									<BR />
									<img src="<?php echo JURI::base()?>components/com_osservicesbooking/asset/images/simple_timeslot.png"  style="border:2px solid red; "/>
								</td>
							</tr>
							<tr>
								<td  class="key" width="20%" valign="top">
									<?php echo JText::_('OS_SHOW_END_TIME_OF_TIMESLOTS');?>
								</td>
								<td width="30%" valign="top">
									<?php
									OSappscheduleConfiguration::showCheckboxfield('show_end_time',(int)$configs->show_end_time);
									?>
								</td>
								<td width="50%" valign="top">
									<?php echo JText::_('OS_SHOW_END_TIME_OF_TIMESLOTS_EXPLAIN');?>
								</td>
							</tr>
							<tr>
								<td  class="key" width="20%" valign="top">
									<?php echo JText::_('OS_HIDE_TAB_WHEN_HAVING_ONE_ITEM');?>
								</td>
								<td width="30%" valign="top">
									<?php
									OSappscheduleConfiguration::showCheckboxfield('hidetabs',(int)$configs->hidetabs);
									?>
								</td>
								<td width="50%" valign="top">
									<?php echo JText::_('OS_HIDE_TAB_WHEN_HAVING_ONE_ITEM_EXPLAIN');?>
									<BR />
									<img src="<?php echo JURI::base()?>components/com_osservicesbooking/asset/images/tabs.png"  style="border:2px solid red; "/>
								</td>
							</tr>
							<tr>
								<td  class="key" width="20%" valign="top">
									<?php echo JText::_('OS_SHOW_EMPLOYEE_INFORMATION_BAR');?>
								</td>
								<td width="30%" valign="top">
									<?php
									OSappscheduleConfiguration::showCheckboxfield('employee_bar',(int)$configs->employee_bar);
									?>
								</td>
								<td width="50%" valign="top">
									<img src="<?php echo JURI::base()?>components/com_osservicesbooking/asset/images/employee_bar.png"  style="border:2px solid red; "/>
								</td>
							</tr>
							<tr>
								<td  class="key" width="20%" valign="top">
									<?php echo JText::_('OS_SHOW_EMPLOYEE_PHONE_EMAIL');?>
								</td>
								<td width="30%" valign="top">
									<?php
									OSappscheduleConfiguration::showCheckboxfield('employee_phone_email',(int)$configs->employee_phone_email);
									?>
								</td>
								<td width="50%" valign="top">
								</td>
							</tr>
							<tr>
								<td  class="key" width="20%" valign="top">
									<?php echo JText::_('OS_SHOW_EMPLOYEE_INFORMATION');?>
								</td>
								<td width="30%" valign="top">
									<?php
									OSappscheduleConfiguration::showCheckboxfield('employee_information',(int)$configs->employee_information);
									?>
								</td>
								<td width="50%" valign="top">
									<img src="<?php echo JURI::base()?>components/com_osservicesbooking/asset/images/employee_information.png"  style="border:2px solid red; "/>
								</td>
							</tr>
							<tr>
								<td  class="key" width="20%" valign="top">
									<?php echo JText::_('OS_SHOW_EMPLOYEE_COST');?>
								</td>
								<td width="30%" valign="top">
									<?php
									OSappscheduleConfiguration::showCheckboxfield('show_employee_cost',(int)$configs->show_employee_cost);
									?>
								</td>
								<td width="50%" valign="top">
									<img src="<?php echo JURI::base()?>components/com_osservicesbooking/asset/images/show_employee_cost.png"  style="border:2px solid red; "/>
								</td>
							</tr>
							<tr>
								<td  class="key" width="20%" valign="top">
									<?php echo JText::_('Show Venue information');?>
								</td>
								<td width="30%" valign="top">
									<?php
									OSappscheduleConfiguration::showCheckboxfield('show_venue',(int)$configs->show_venue);
									?>
								</td>
								<td width="50%" valign="top">
									Do you want to show Venue information in Booking table page
								</td>
							</tr>
							<tr>
								<td  class="key" width="20%" valign="top">
									<?php echo JText::_('OS_SHOW_NUMBERSLOTS_BOOKING_INPUTBOX');?>
								</td>
								<td width="30%" valign="top">
									<?php
									OSappscheduleConfiguration::showCheckboxfield('show_number_timeslots_booking',(int)$configs->show_number_timeslots_booking);
									?>
								</td>
								<td width="50%" valign="top">
									<?php echo JText::_('OS_SHOW_NUMBERSLOTS_BOOKING_INPUTBOX_EXPLAIN');?>
									<BR />
									<img src="<?php echo JURI::base()?>components/com_osservicesbooking/asset/images/numberslots.png"  style="border:2px solid red; "/>
								</td>
							</tr>
							<tr>
								<td  class="key" width="20%" valign="top">
									<?php echo JText::_('Show dropdown select list Month, Year');?>
								</td>
								<td width="30%" valign="top">
									<?php
									OSappscheduleConfiguration::showCheckboxfield('show_dropdown_month_year',(int)$configs->show_dropdown_month_year);
									?>
								</td>
								<td width="50%" valign="top">
									<?php echo JText::_('Please select Calendar Arrow buttons');?>
									<BR />
									<img src="<?php echo JURI::base()?>components/com_osservicesbooking/asset/images/dropdown_month_year.png"  style="border:2px solid red; "/>
								</td>
							</tr>
							<tr>
								<td  class="key" width="20%" valign="top">
									<?php echo JText::_('Calendar Arrow');?>
								</td>
								<td width="30%" valign="top">
									<table width="100%">
										<?php
										$name = "calendar_arrow";
										$arr1 = array('dark','pink','blue','green','transparent');
										$arr2 = array('Dark arrows','Pink arrows','Blue arrows','Green arrows','Transparent arrows');
										for($i=0;$i<count($arr1);$i++)
										{
											
											if($configs->calendar_arrow == $arr1[$i])
											{
												$checked = "checked";
											}
											else
											{
												$checked = "";
											}
											?>
											<tr>
												<td width="20%" style="text-align:center;">
													<input type="radio" name="<?php echo $name;?>" value="<?php echo $arr1[$i]?>" <?php echo $checked?> />
												</td>
												<td width="80%" style="text-align:left;">
													<img src="<?php echo JURI::root()?>components/com_osservicesbooking/asset/images/icons/previous_<?php echo $arr1[$i]?>.png" />
													<img src="<?php echo JURI::root()?>components/com_osservicesbooking/asset/images/icons/next_<?php echo $arr1[$i]?>.png" />
												</td>
											</tr>
											<?php
										}

										?>
									</table>
								</td>
								<td width="50%" valign="top">
									<?php echo JText::_('Please select Calendar Arrow buttons');?>
									<BR />
									<img src="<?php echo JURI::base()?>components/com_osservicesbooking/asset/images/calendar_arrow.png"  style="border:2px solid red; "/>
								</td>
							</tr>
							<tr>
								<td  class="key" width="20%" valign="top">
									<?php echo JText::_('OS_LOAD_BUTTON_STYLES');?>
								</td>
								<td width="30%" valign="top">
									<?php
									OSappscheduleConfiguration::showCheckboxfield('load_button_style',(int)$configs->load_button_style);
									?>
								</td>
								<td width="50%" valign="top">
								</td>
							</tr>
							<tr>
								<td  class="key" width="20%" valign="top">
									<?php echo JText::_('Header Style');?>
								</td>
								<td width="30%" valign="top">
									<table width="100%">
										<?php
										$name = "header_style";
										$arr1 = array('btn','btn btn-primary','btn btn-info','btn btn-success','btn btn-warning','btn btn-danger','btn btn-inverse');
										$arr2 = array('Gray style','Blue style','Light Blue style','Green style','Yellow style','Red style','Black style');
										$exists = 0;
										for($i=0;$i<count($arr1);$i++){
											
											if($configs->header_style == $arr1[$i])
											{
												$exists = 1;
												$checked = "checked";
											}
											else
											{
												$checked = "";
											}
											?>
											<tr>
												<td width="20%" style="text-align:center;">
													<input type="radio" name="<?php echo $name;?>" value="<?php echo $arr1[$i]?>" <?php echo $checked?> />
												</td>
												<td width="80%" style="text-align:left;">
													<input type="button" class="<?php echo $arr1[$i]?>" value="<?php echo $arr2[$i]?>" style="width:150px;" />
												</td>
											</tr>
											<?php
										}
										?>
										<?php
										if($exists == 0)
										{
											$value = $configs->header_style;
											$checked = "checked";
										}
										else
										{
											$value = "";
											$checked = "";
										}	
										?>
										<tr>
											<td colspan="2">
												Custom CSS Class
											</td>
										</tr>
										<tr>
											<td width="20%" style="text-align:center;">
												<input type="radio" name="<?php echo $name;?>" value="custom_header_style" <?php echo $checked?> />
											</td>
											<td width="80%" style="text-align:left;">
												<?php
												?>
												<input type="text" name="custom_<?php echo $name;?>" class="input-medium form-control" value="<?php echo $value; ?>"/>
											</td>
										</tr>
									</table>
								</td>
								<td width="50%" valign="top">
									<?php echo JText::_('Please select style of Headers');?>
									<BR />
									<img src="<?php echo JURI::base()?>components/com_osservicesbooking/asset/images/header_style.png"  style="border:2px solid red; "/>
								</td>
							</tr>
							<tr>
								<td  class="key" width="20%" valign="top">
									<?php echo JText::_('Calendar Normal Style');?>
								</td>
								<td width="30%" valign="top">
									<table width="100%">
										<?php
										$name = "calendar_normal_style";
										$arr1 = array('btn','btn btn-primary','btn btn-info','btn btn-success','btn btn-warning','btn btn-danger','btn btn-inverse');
										$arr2 = array('Gray style','Blue style','Light Blue style','Green style','Yellow style','Red style','Black style');
										$exists = 0;
										for($i=0;$i<count($arr1);$i++){
											
											if($configs->calendar_normal_style == $arr1[$i]){
												$exists = 1;
												$checked = "checked";
											}else{
												$checked = "";
											}
											?>
											<tr>
												<td width="20%" style="text-align:center;">
													<input type="radio" name="<?php echo $name;?>" value="<?php echo $arr1[$i]?>" <?php echo $checked?> />
												</td>
												<td width="80%" style="text-align:left;">
													<input type="button" class="<?php echo $arr1[$i]?>" value="<?php echo $arr2[$i]?>" style="width:150px;" />
												</td>
											</tr>
											<?php
										}
										?>
										<?php
										if($exists == 0)
										{
											$value = $configs->calendar_normal_style;
											$checked = "checked";
										}
										else
										{
											$value = "";
											$checked = "";
										}	
										?>
										<tr>
											<td colspan="2">
												Custom CSS Class
											</td>
										</tr>
										<tr>
											<td width="20%" style="text-align:center;">
												<input type="radio" name="<?php echo $name;?>" value="custom_<?php echo $name;?>" <?php echo $checked?> />
											</td>
											<td width="80%" style="text-align:left;">
												<?php
												?>
												<input type="text" name="custom_<?php echo $name;?>" class="input-medium form-control" value="<?php echo $value; ?>"/>
											</td>
										</tr>
									</table>
								</td>
								<td width="50%" valign="top">
									<?php echo JText::_('Please select style of Calendar Normal date');?>
									<BR />
									<img src="<?php echo JURI::base()?>components/com_osservicesbooking/asset/images/normal_date.png"  style="border:2px solid red; "/>
								</td>
							</tr>
							
							<tr>
								<td  class="key" width="20%" valign="top">
									<?php echo JText::_('Calendar Actived Date Style');?>
								</td>
								<td width="30%" valign="top">
									<table width="100%">
										<?php
										$name = "calendar_activate_style";
										$arr1 = array('btn','btn btn-primary','btn btn-info','btn btn-success','btn btn-warning','btn btn-danger','btn btn-inverse');
										$arr2 = array('Gray style','Blue style','Light Blue style','Green style','Yellow style','Red style','Black style');
										$exists = 0;
										for($i=0;$i<count($arr1);$i++){
											
											if($configs->calendar_activate_style == $arr1[$i]){
												$exists = 1;
												$checked = "checked";
											}else{
												$checked = "";
											}
											?>
											<tr>
												<td width="20%" style="text-align:center;">
													<input type="radio" name="<?php echo $name;?>" value="<?php echo $arr1[$i]?>" <?php echo $checked?> />
												</td>
												<td width="80%" style="text-align:left;">
													<input type="button" class="<?php echo $arr1[$i]?>" value="<?php echo $arr2[$i]?>" style="width:150px;" />
												</td>
											</tr>
											<?php
										}
										?>
										<?php
										if($exists == 0)
										{
											$value = $configs->calendar_activate_style;
											$checked = "checked";
										}
										else
										{
											$value = "";
											$checked = "";
										}	
										?>
										<tr>
											<td colspan="2">
												Custom CSS Class
											</td>
										</tr>
										<tr>
											<td width="20%" style="text-align:center;">
												<input type="radio" name="<?php echo $name;?>" value="custom_<?php echo $name;?>" <?php echo $checked?> />
											</td>
											<td width="80%" style="text-align:left;">
												<?php
												?>
												<input type="text" name="custom_<?php echo $name;?>" class="input-medium form-control" value="<?php echo $value; ?>"/>
											</td>
										</tr>
									</table>
								</td>
								<td width="50%" valign="top">
									<?php echo JText::_('Please select style of Calendar Normal date');?>
									<BR />
									<img src="<?php echo JURI::base()?>components/com_osservicesbooking/asset/images/activate_date.png"  style="border:2px solid red; "/>
								</td>
							</tr>
							
							<tr>
								<td  class="key" width="20%" valign="top">
									<?php echo JText::_('Calendar Current Date Style');?>
								</td>
								<td width="30%" valign="top">
									<table width="100%">
										<?php
										$name = "calendar_currentdate_style";
										$arr1 = array('btn','btn btn-primary','btn btn-info','btn btn-success','btn btn-warning','btn btn-danger','btn btn-inverse');
										$arr2 = array('Gray style','Blue style','Light Blue style','Green style','Yellow style','Red style','Black style');
										$exists = 0;
										for($i=0;$i<count($arr1);$i++){
											
											if($configs->calendar_currentdate_style == $arr1[$i]){
												$exists = 1;
												$checked = "checked";
											}else{
												$checked = "";
											}
											?>
											<tr>
												<td width="20%" style="text-align:center;">
													<input type="radio" name="<?php echo $name;?>" value="<?php echo $arr1[$i]?>" <?php echo $checked?> />
												</td>
												<td width="80%" style="text-align:left;">
													<input type="button" class="<?php echo $arr1[$i]?>" value="<?php echo $arr2[$i]?>" style="width:150px;" />
												</td>
											</tr>
											<?php
										}
										?>
										<?php
										if($exists == 0)
										{
											$value = $configs->calendar_currentdate_style;
											$checked = "checked";
										}
										else
										{
											$value = "";
											$checked = "";
										}	
										?>
										<tr>
											<td colspan="2">
												Custom CSS Class
											</td>
										</tr>
										<tr>
											<td width="20%" style="text-align:center;">
												<input type="radio" name="<?php echo $name;?>" value="custom_<?php echo $name;?>" <?php echo $checked?> />
											</td>
											<td width="80%" style="text-align:left;">
												<?php
												?>
												<input type="text" name="custom_<?php echo $name;?>" class="input-medium form-control" value="<?php echo $value; ?>"/>
											</td>
										</tr>
									</table>
								</td>
								<td width="50%" valign="top">
									<?php echo JText::_('Please select style of Calendar Normal date');?>
									<BR />
									<img src="<?php echo JURI::base()?>components/com_osservicesbooking/asset/images/current_date.png"  style="border:2px solid red; "/>
								</td>
							</tr>
							<tr>
								<td  class="key" width="20%" valign="top">
									<?php echo JText::_('Calendar Inactivated Date Style');?>
								</td>
								<td width="30%" valign="top">
									<table width="100%">
										<?php
										$name = "calendar_inactivate_style";
										$arr1 = array('btn','btn btn-primary','btn btn-info','btn btn-success','btn btn-warning','btn btn-danger','btn btn-inverse');
										$arr2 = array('Gray style','Blue style','Light Blue style','Green style','Yellow style','Red style','Black style');
										$exists = 0;
										for($i=0;$i<count($arr1);$i++){

											if($configs->calendar_inactivate_style == $arr1[$i]){
												$exists = 1;
												$checked = "checked";
											}else{
												$checked = "";
											}
											?>
											<tr>
												<td width="20%" style="text-align:center;">
													<input type="radio" name="<?php echo $name;?>" value="<?php echo $arr1[$i]?>" <?php echo $checked?> />
												</td>
												<td width="80%" style="text-align:left;">
													<input type="button" class="<?php echo $arr1[$i]?>" value="<?php echo $arr2[$i]?>" style="width:150px;" />
												</td>
											</tr>
											<?php
										}
										?>
										<?php
										if($exists == 0)
										{
											$value = $configs->calendar_inactivate_style;
											$checked = "checked";
										}
										else
										{
											$value = "";
											$checked = "";
										}	
										?>
										<tr>
											<td colspan="2">
												Custom CSS Class
											</td>
										</tr>
										<tr>
											<td width="20%" style="text-align:center;">
												<input type="radio" name="<?php echo $name;?>" value="custom_<?php echo $name;?>" <?php echo $checked?> />
											</td>
											<td width="80%" style="text-align:left;">
												<?php
												?>
												<input type="text" name="custom_<?php echo $name;?>" class="input-medium form-control" value="<?php echo $value; ?>"/>
											</td>
										</tr>
									</table>
								</td>
								<td width="50%" valign="top">
									<?php echo JText::_('Please select style of Calendar Inactivated date');?>
									<BR />
									<img src="<?php echo JURI::base()?>components/com_osservicesbooking/asset/images/inactivated_date.png"  style="border:2px solid red; "/>
								</td>
							</tr>
							<tr>
								<td  class="key" width="20%" valign="top">
									<?php echo JText::_('Calendar No available timeslots');?>
								</td>
								<td width="30%" valign="top">
									<table width="100%">
										<?php
										$name = "non_available_timeslots";
										$arr1 = array('btn','btn btn-primary','btn btn-info','btn btn-success','btn btn-warning','btn btn-danger','btn btn-inverse');
										$arr2 = array('Gray style','Blue style','Light Blue style','Green style','Yellow style','Red style','Black style');
										$exists = 0;
										for($i=0;$i<count($arr1);$i++){
											
											if($configs->non_available_timeslots == $arr1[$i])
											{
												$exists = 1;
												$checked = "checked";
											}
											else
											{
												$checked = "";
											}
											?>
											<tr>
												<td width="20%" style="text-align:center;">
													<input type="radio" name="<?php echo $name;?>" value="<?php echo $arr1[$i]?>" <?php echo $checked?> />
												</td>
												<td width="80%" style="text-align:left;">
													<input type="button" class="<?php echo $arr1[$i]?>" value="<?php echo $arr2[$i]?>" style="width:150px;" />
												</td>
											</tr>
											<?php
										}
										?>
										<?php
										if($exists == 0)
										{
											$value = $configs->non_available_timeslots;
											$checked = "checked";
										}
										else
										{
											$value = "";
											$checked = "";
										}	
										?>
										<tr>
											<td colspan="2">
												Custom CSS Class
											</td>
										</tr>
										<tr>
											<td width="20%" style="text-align:center;">
												<input type="radio" name="<?php echo $name;?>" value="custom_non_available_timeslots" <?php echo $checked?> />
											</td>
											<td width="80%" style="text-align:left;">
												<?php
												?>
												<input type="text" name="custom_<?php echo $name;?>" class="input-medium form-control" value="<?php echo $value; ?>"/>
											</td>
										</tr>
									</table>
								</td>
								<td width="50%" valign="top">
									
								</td>
							</tr>
							<tr>
								<td  class="key" width="20%" valign="top">
									<?php echo JText::_('OS_SHOW_SERVICE_INFORMATION_BOX');?>
								</td>
								<td width="30%" valign="top">
									<?php
									OSappscheduleConfiguration::showCheckboxfield('show_service_info_box',(int)$configs->show_service_info_box);
									?>
								</td>
								<td width="50%" valign="top">
									<?php echo JText::_('OS_SHOW_SERVICE_INFORMATION_BOX_EXPLAIN');?>
									<BR /><BR />
									<img src="<?php echo JURI::base()?>components/com_osservicesbooking/asset/images/service_box.png"  style="border:2px solid red; "/>
								</td>
							</tr>
							<tr>
								<td  class="key" width="20%" valign="top">							
									<?php echo JText::_('OS_SHOW_SERVICE_PHOTO');?>
								</td>
								<td width="30%" valign="top">
									<?php
									OSappscheduleConfiguration::showCheckboxfield('show_service_photo',(int)$configs->show_service_photo);
									?>
								</td>
								<td width="50%" valign="top">
									<?php echo JText::_('OS_SHOW_SERVICE_PHOTO_EXPLAIN');?>
									<BR /><BR />
									<img src="<?php echo JURI::base()?>components/com_osservicesbooking/asset/images/service_photo.png"  style="border:2px solid red; "/>
								</td>
							</tr>
							<tr>
								<td  class="key" width="20%" valign="top">
									<?php echo JText::_('OS_SHOW_SERVICE_DESCRIPTION');?>
								</td>
								<td width="30%" valign="top">
									<?php
									OSappscheduleConfiguration::showCheckboxfield('show_service_description',(int)$configs->show_service_description);
									?>
								</td>
								<td width="50%" valign="top">
									<?php echo JText::_('OS_SHOW_SERVICE_DESCRIPTION_EXPLAIN');?>
									<BR /><BR />
									<img src="<?php echo JURI::base()?>components/com_osservicesbooking/asset/images/service_description.png"  style="border:2px solid red; "/>
								</td>
							</tr>
							<tr>
								<td  class="key" width="20%" valign="top">
									<?php echo JText::_('OS_SHOW_BOOKED_INFO_BOX');?>
								</td>
								<td width="30%" valign="top">
									<?php
									OSappscheduleConfiguration::showCheckboxfield('show_booked_information',(int)$configs->show_booked_information);
									?>
								</td>
								<td width="50%" valign="top">
									<?php echo JText::_('OS_SHOW_BOOKED_INFO_BOX_EXPLAIN');?>
									<BR /><BR />
									<img src="<?php echo JURI::base()?>components/com_osservicesbooking/asset/images/booked_information.png"  style="border:2px solid red; "/>
								</td>
							</tr>
							<tr>
								<td  class="key" width="20%" valign="top">
									<?php echo JText::_('Show Progress bar');?>
								</td>
								<td width="30%" valign="top">
									<?php
									OSappscheduleConfiguration::showCheckboxfield('show_progress_bar',(int)$configs->show_progress_bar);
									?>
								</td>
								<td width="50%" valign="top">
									<?php echo JText::_('Do you want to show progress bar?');?>
								</td>
							</tr>
							<tr>
								<td  class="key" width="20%" valign="top">
									<?php echo JText::_('Progress bar color');?>
								</td>
								<td width="30%" valign="top">
									<table width="100%">
										<?php
										if($configs->progress_bar_background == ""){
											$configs->progress_bar_background = "#1C67A9";
										}
										$name = "progress_bar_background";
										$arr1 = array('#7BA1EB','#1C67A9','#58B158','#F89E1D','#D04640','#2E2E2E','#797979');
										$exists = 0;
										for($i=0;$i<count($arr1);$i++){
											
											if($configs->progress_bar_background == $arr1[$i]){
												$exists = 1;
												$checked = "checked";
											}else{
												$checked = "";
											}
											?>
											<tr>
												<td width="20%" style="text-align:center;">
													<input type="radio" name="<?php echo $name;?>" value="<?php echo $arr1[$i]?>" <?php echo $checked?> />
												</td>
												<td width="80%" style="text-align:center;background-color:<?php echo $arr1[$i];?>;color:white;">
													<?php echo $arr1[$i];?>
												</td>
											</tr>
											<?php
										}
										?>
										<?php
										if($exists == 0)
										{
											$value = $configs->progress_bar_background;
											$checked = "checked";
										}
										else
										{
											$value = "";
											$checked = "";
										}	
										?>
										<tr>
											<td colspan="2">
												Custom Hexa Color code
											</td>
										</tr>
										<tr>
											<td width="20%" style="text-align:center;">
												<input type="radio" name="<?php echo $name;?>" value="custom_<?php echo $name;?>" <?php echo $checked?> />
											</td>
											<td width="80%" style="text-align:left;">
												<?php
												?>
												<input type="text" name="custom_<?php echo $name;?>" class="input-medium form-control" value="<?php echo $value; ?>"/>
											</td>
										</tr>
									</table>
								</td>
								<td width="50%" valign="top">
									<?php echo JText::_('Please select color of Progress bar');?>
									<BR />
									<img src="<?php echo JURI::base()?>components/com_osservicesbooking/asset/images/progress_bar_color.png"  style="border:2px solid red; "/>
								</td>
							</tr>
							<tr>
								<td  class="key" width="20%" valign="top">
									<?php echo JText::_('Time Slots background');?>
								</td>
								<td width="30%" valign="top">
									<table width="100%">
										<?php
										$name = "timeslot_background";
										$arr1 = array('#7BA1EB','#1C67A9','#58B158','#F89E1D','#D04640','#2E2E2E','#797979');
										$exists = 0;
										for($i=0;$i<count($arr1);$i++){
											
											if($configs->timeslot_background == $arr1[$i]){
												$exists = 1;
												$checked = "checked";
											}else{
												$checked = "";
											}
											?>
											<tr>
												<td width="20%" style="text-align:center;">
													<input type="radio" name="<?php echo $name;?>" value="<?php echo $arr1[$i]?>" <?php echo $checked?> />
												</td>
												<td width="80%" style="text-align:center;background-color:<?php echo $arr1[$i];?>;color:white;">
													<?php echo $arr1[$i];?>
												</td>
											</tr>
											<?php
										}
										?>
										<?php
										if($exists == 0)
										{
											$value = $configs->timeslot_background;
											$checked = "checked";
										}
										else
										{
											$value = "";
											$checked = "";
										}	
										?>
										<tr>
											<td colspan="2">
												Custom Hexa Color code
											</td>
										</tr>
										<tr>
											<td width="20%" style="text-align:center;">
												<input type="radio" name="<?php echo $name;?>" value="custom_<?php echo $name;?>" <?php echo $checked?> />
											</td>
											<td width="80%" style="text-align:left;">
												<?php
												?>
												<input type="text" name="custom_<?php echo $name;?>" class="input-medium form-control" value="<?php echo $value; ?>"/>
											</td>
										</tr>
									</table>
								</td>
								<td width="50%" valign="top">
									<?php echo JText::_('Please select style of Calendar Normal date');?>
									<BR />
									<img src="<?php echo JURI::base()?>components/com_osservicesbooking/asset/images/timeslot_background.png"  style="border:2px solid red; width:500px;"/>
								</td>
							</tr>
							<tr>
								<td  class="key" width="20%" valign="top">
									<?php echo JText::_('Booked/ Selected Time Slots background');?>
								</td>
								<td width="30%" valign="top">
									<table width="100%">
										<?php
										$name = "booked_timeslot_background";
										$arr1 = array('#e65789','#1C67A9','#58B158','#F89E1D','#D04640','#2E2E2E','#797979');
										if($configs->booked_timeslot_background == "")
										{
											$configs->booked_timeslot_background = "#e65789";
										}
										$exists = 0;
										for($i=0;$i<count($arr1);$i++)
										{
											
											if($configs->booked_timeslot_background == $arr1[$i])
											{
												$exists = 1;
												$checked = "checked";
											}
											else
											{
												$checked = "";
											}
											?>
											<tr>
												<td width="20%" style="text-align:center;">
													<input type="radio" name="<?php echo $name;?>" value="<?php echo $arr1[$i]?>" <?php echo $checked?> />
												</td>
												<td width="80%" style="text-align:center;background-color:<?php echo $arr1[$i];?>;color:white;">
													<?php echo $arr1[$i];?>
												</td>
											</tr>
											<?php
										}
										?>
										<?php
										if($exists == 0)
										{
											$value = $configs->booked_timeslot_background;
											$checked = "checked";
										}
										else
										{
											$value = "";
											$checked = "";
										}	
										?>
										<tr>
											<td colspan="2">
												Custom Hexa Color code
											</td>
										</tr>
										<tr>
											<td width="20%" style="text-align:center;">
												<input type="radio" name="<?php echo $name;?>" value="custom_<?php echo $name;?>" <?php echo $checked?> />
											</td>
											<td width="80%" style="text-align:left;">
												<?php
												?>
												<input type="text" name="custom_<?php echo $name;?>" class="input-medium form-control" value="<?php echo $value; ?>"/>
											</td>
										</tr>
									</table>
								</td>
								<td width="50%" valign="top">
								</td>
							</tr>
                            <tr>
                                <td  class="key" width="20%" valign="top">
                                    <?php echo JText::_('OS_SHOW_AVAILABLE_SEATS_OF_SLOTS');?>
                                </td>
                                <td width="30%" valign="top">
                                    <?php
                                    OSappscheduleConfiguration::showCheckboxfield('show_avail_slots',(int)$configs->show_avail_slots)
                                    ?>
                                </td>
                                <td width="50%" valign="top">
                                    <?php echo JText::_('OS_SHOW_AVAILABLE_SEATS_OF_SLOTS_EXPLAIN');?>
                                </td>
                            </tr>
							<tr>
								<td  class="key" width="20%" valign="top">
									<?php echo JText::_('OS_SHOW_ORDER_URL_AND_CANCEL_URL');?>
								</td>
								<td width="30%" valign="top">
									<?php
									OSappscheduleConfiguration::showCheckboxfield('show_details_and_orders',(int)$configs->show_details_and_orders)
									?>
								</td>
								<td width="50%" valign="top">
									<?php echo JText::_('OS_SHOW_ORDER_URL_AND_CANCEL_URL_EXPLAIN');?>
									<BR /><BR />
									<img src="<?php echo JURI::base()?>components/com_osservicesbooking/asset/images/url.png"  style="border:2px solid red; width:500px;"/>
								</td>
							</tr>
						</table>
					<?php echo JHtml::_($tabApiPrefix.'endTab') ?>
					<?php echo JHtml::_($tabApiPrefix.'addTab', 'configTab', 'email-marketing', JText::_('OS_EMAIL_MARKETING')); ?>
			
						<table class="admintable adminform" style="width:100%;">
							<tr>
								<td width="100%" colspan="2">
									This feature is used to setup OS Services Booking with access information for adding customers to your AcyMailing lists. When enabled, OS Services Booking will call AcyMailing and insert a new mailing list user as part of the appointment booking process.
									<BR />
									<strong>Note:</strong><BR />
									1. Changing the status of a booking has no effect on AcyMailing.<BR />
									2. Cancelling a booking does not remove a list entry.<BR />
									3. OS Services Booking never removes list entries from AcyMailing. <BR />
									4. You must have the AcyMailing component installed to use this option. See <a href="https://www.acyba.com/acymailing.html" target="_blank">https://www.acyba.com/acymailing.html</a>
								</td>
							</tr>
							<tr>
								<td  class="key" width="30%" >
									<?php echo JText::_('OS_ENABLE_ACYMAILING');?>
								</td>
								<td width="70%" valign="top">
									<?php
									OSappscheduleConfiguration::showCheckboxfield('enable_acymailing',(int)$configs->enable_acymailing)
									?>
								</td>
							</tr>
							<tr>
								<td  class="key" width="30%" valign="top">
									<?php echo JText::_('OS_SELECT_DEFAULT_LIST');?>
								</td>
								<td width="70%" valign="top">
									<?php
									$acyLists = null;
									if(file_exists(JPATH_ADMINISTRATOR . '/components/com_acym/acym.php') && JComponentHelper::isEnabled('com_acym', true))
									{
										if(include_once(rtrim(JPATH_ADMINISTRATOR,DS).'/components/com_acym/helpers/helper.php')){
											$listClass = acym_get('class.list');
											$acyLists  = $listClass->getAllWithIdName();
										}
										echo JHtml::_('select.genericlist', $acyLists, 'acymailing_default_list_id', 'class="'.$mapClass['input-large'].'"', 'id', 'name', $configs->acymailing_default_list_id);
									}
									elseif(file_exists(JPATH_ADMINISTRATOR . '/components/com_acymailing/acymailing.php') && JComponentHelper::isEnabled('com_acymailing', true))
									{
										if(include_once(rtrim(JPATH_ADMINISTRATOR,DS).'/components/com_acymailing/helpers/helper.php')){
											$listClass = acymailing_get('class.list');
											$acyLists = $listClass->getLists();
										}
										?>
										<select name="acymailing_default_list_id">
											<?php
											foreach($acyLists as $List){ ?>
												<option value="<?php echo $List->listid;?>"<?php if($configs->acymailing_default_list_id == $List->listid){echo " selected='selected' ";} ?>><?php echo $List->name;?></option>
											<?php } ?>
										</select>
										<?php
									}
									?>
									<BR />
									Select a default AcyMailing list to receive new customers.
									<BR />
									You can override this at the OS Services Booking service level in the service modification screen
								</td>
							</tr>
						</table>
					<?php echo JHtml::_($tabApiPrefix.'endTab') ;?>
					<?php echo JHtml::_($tabApiPrefix.'addTab', 'configTab', 'custom-css', JText::_('OS_CUSTOM_CSS')); ?>
						<table  width="100%">
							<tr>
								<td>
									<?php
									$customCss = '';
									if (file_exists(JPATH_ROOT.'/media/com_osservicesbooking/assets/css/custom.css'))
									{
										$customCss = file_get_contents(JPATH_ROOT.'/media/com_osservicesbooking/assets/css/custom.css');
									}
									if (OSBHelper::isJoomla4())
									{
									?>
										<textarea class="form-control" name="custom_css" rows="20" style="width:100%;"><?php echo $customCss; ?></textarea>
									<?php
									}
									else
									{
										echo JEditor::getInstance($editorPlugin)->display('custom_css', $customCss, '100%', '550', '75', '8', false, null, null, null, array('syntax' => 'css'));
									}
									?>
								</td>
							</tr>
						</table>
					<?php echo JHtml::_($tabApiPrefix.'endTab'); ?>
					<?php echo JHtml::_($tabApiPrefix.'addTab', 'configTab', 'gdpr_privacy', JText::_('OS_GDPR_SETTING')); ?>

						<table width="100%" class="admintable adminform">
							<tr>
								<td class="key" width="20%">
									<?php echo JText::_('OS_SHOW_PRIVACY_POLICY');?>
								</td>
								<td width="30%" valign="top">
									<?php
									OSappscheduleConfiguration::showCheckboxfield('active_privacy',(int)$configs->active_privacy)
									?>
								</td>
								<td width="50%" valign="top">
									<?php echo JText::_('OS_SHOW_PRIVACY_POLICY_EXPLAIN');?>
								</td>
							</tr>
							<tr>
								<td class="key" width="20%">
									<?php echo JText::_('OS_PRIVACY_ARTICLE');?>
								</td>
								<td width="30%" valign="top">
									<?php echo OSBHelper::getArticleInput($configs->privacy_policy_article_id, 'privacy_policy_article_id'); ?>
								</td>
								<td width="50%" valign="top">
									<?php echo JText::_('OS_PRIVACY_ARTICLE_EXPLAIN');?>
								</td>
							</tr>
							<tr>
								<td class="key" width="20%">
									<?php echo JText::_('OS_SHOW_PRIVACY_POLICY_CHECKBOX_WITH_LOGGED_USERS');?>
								</td>
								<td width="30%" valign="top">
									<?php OSappscheduleConfiguration::showCheckboxfield('show_privacy_with_logged_users',(int)$configs->show_privacy_with_logged_users); ?>
								</td>
								<td width="50%" valign="top">
									<?php echo JText::_('OS_SHOW_PRIVACY_POLICY_CHECKBOX_WITH_LOGGED_USERS_EXPLAIN');?>
								</td>
							</tr>
                            <tr>
                                <td class="key" width="20%">
                                    <?php echo JText::_('OS_SHOW_PRIVACY_POLICY_CHECKBOX_IN_REGISTRATION_FORM');?>
                                </td>
                                <td width="30%" valign="top">
                                    <?php OSappscheduleConfiguration::showCheckboxfield('show_privacy_in_registration_form',(int)$configs->show_privacy_in_registration_form); ?>
                                </td>
                                <td width="50%" valign="top">
                                    <?php echo JText::_('OS_SHOW_PRIVACY_POLICY_CHECKBOX_IN_REGISTRATION_FORM_EXPLAIN');?>
                                </td>
                            </tr>
						</table>
					<?php echo JHtml::_($tabApiPrefix.'endTab') ?>
				<?php echo JHtml::_($tabApiPrefix.'endTabSet'); ?>
			</div>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="live_site" id="live_site" value="<?php echo JUri::root(); ?>" />
		</form>
        <script type="text/javascript">
            function sendTestSMS()
            {
                var answer = confirm('<?php echo JText::_('OS_ARE_YOU_SURE_YOU_WANT_TO_SEND_TEST_SMS');?>');
                if(answer == 1)
                {
                    doSendTestSMS();
                }
            }
        </script>
		<?php
	}
}
?>