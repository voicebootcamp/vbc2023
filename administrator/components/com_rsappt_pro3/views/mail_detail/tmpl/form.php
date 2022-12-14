<?php
/*
 ****************************************************************
 Copyright (C) 2008-2020 Soft Ventures, Inc. All rights reserved.
 ****************************************************************
 * @package	Appointment Booking Pro - ABPro
 * @copyright	Copyright (C) 2008-2020 Soft Ventures, Inc. All rights reserved.
 * @license	GNU/GPL, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 * ABPro is distributed WITHOUT ANY WARRANTY, or implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This header must not be removed. Additional contributions/changes
 * may be added to this header as long as no information is deleted.
 *
 ************************************************************
 The latest version of ABPro is available to subscribers at:
 http://www.appointmentbookingpro.com/
 ************************************************************
*/

defined('_JEXEC') or die('Restricted access');

## initialize the editor

jimport( 'joomla.html.editor' );
	$config = JFactory::getConfig();
	$global_editor = $config->get( 'editor' );
	$user_editor = JFactory::getUser()->getParam("editor");

	if($user_editor && $user_editor !== 'JEditor') {
		$selected_editor = $user_editor;
	} else {
		$selected_editor = $global_editor;
	}
	$editor = JEditor::getInstance($selected_editor);

	// Get resources for dropdown list
	$database = JFactory::getDBO();
	try{
		$database->setQuery("SELECT * FROM #__sv_apptpro3_resources WHERE timeslots != 'Global' ORDER BY name" );
		$res_rows = $database -> loadObjectList();
	} catch (RuntimeException $e) {
		logIt($e->getMessage(), "", "", "");
		echo JText::_('RS1_SQL_ERROR');
		return false;
	}		
	

?>

<link href="<?php echo JURI::root( true );?>/components/com_rsappt_pro3/sv_apptpro.css" rel="stylesheet">
<link href="<?php echo JURI::root( true );?>/administrator/components/com_rsappt_pro3/abpro_admin.css" rel="stylesheet">
<form action="<?php echo JRoute::_($this->request_url) ?>" method="post" name="adminForm" id="adminForm" >
<fieldset class="adminform">
	<?php echo JText::_('RS1_ADMIN_CONFIG_MSG_INTRO');?><br/>
	<?php echo JText::_('RS1_ADMIN_CONFIG_MSG_INTRO2');?>
	<table class="table table-striped" >
	<tr>
      <td width="15%"><?php echo JText::_('RS1_ADMIN_SCRN_MAIL_ID');?>:</td>
      <td><?php echo $this->detail->id_mail ?></td>
      <td width="50%">&nbsp;</td>
    </tr>
		<tr>
	        <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_MAIL_LABEL');?>:</td>
	        <td valign="top"><?php if ($this->detail->secured == 1) { echo $this->detail->mail_label ?></td>
			<?php } else { ?>
			<input type="text" size="50" maxsize="255" name="mail_label" value="<?php echo $this->detail->mail_label; ?>" /></td>
			<?php }?>
	        <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_MAIL_LABEL_HELP');?></td>
		  </tr>			
              <tr class="sv_message_centre_editor" >
                <td width="15%" valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_BOOKING_COMPLETE');?>:</td>
                <td width="55%" valign="top"><?php echo $editor->display( 'booking_succeeded',  $this->detail->booking_succeeded , '100%', '250', '75', '20', false , null, null, null) ;?></td>
                <td with="30%" valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_BOOKING_COMPLETE_HELP');?></td>
              </tr>
              <tr >
                <td width="15%" valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_BOOKING_COMPLETE_ADMIN');?>:</td>
                <td valign="top"><?php echo $editor->display( 'booking_succeeded_admin',  $this->detail->booking_succeeded_admin , '100%', '250', '75', '20', false ) ;?></td>
                <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_BOOKING_COMPLETE_ADMIN_HELP');?></td>
              </tr>
              <tr >
                <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_CONF_ATTACHMENT');?>:</td>
                <td valign="top"><input type="text" style="width:95%" name="confirmation_attachment" value="<?php echo stripslashes($this->detail->confirmation_attachment); ?>"/></td>
                <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_CONF_ATTACHMENT_HELP');?></td>
              </tr>
              <tr >
                <td width="15%" valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_ICS');?>:</td>
                <td valign="top"><div style="display: table-cell; padding-left:5px; vertical-align:middle"><?php echo JText::_('RS1_ADMIN_CONFIG_ICS_TO_CUSTOMER');?>:</div>
                  <div style="display: table-cell; padding-left:5px;"><select name="attach_ics_customer" style="width:60px !important;" >
                    <option value="Yes" <?php if($this->detail->attach_ics_customer == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
                    <option value="No" <?php if($this->detail->attach_ics_customer == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
                  </select></div>
                  <div style="display: table-cell; padding-left:10px; vertical-align:middle"><?php echo JText::_('RS1_ADMIN_CONFIG_ICS_TO_ADMIN');?>:</div>
                  <div style="display: table-cell; padding-left:5px;"><select name="attach_ics_admin" style="width:60px !important;" >
                    <option value="Yes" <?php if($this->detail->attach_ics_admin == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
                    <option value="No" <?php if($this->detail->attach_ics_admin == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
                  </select></div>
                  <div style="display: table-cell; padding-left:10px; vertical-align:middle"><?php echo JText::_('RS1_ADMIN_CONFIG_ICS_TO_RESOURCE');?>:</div>
                  <div style="display: table-cell; padding-left:5px;"><select name="attach_ics_resource" style="width:60px !important; ">
                    <option value="Yes" <?php if($this->detail->attach_ics_resource == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
                    <option value="No" <?php if($this->detail->attach_ics_resource == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
                </select></div></td>
                <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_ICS_HELP');?></td>
              </tr>
              <tr >
                <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_SMS_BOOKING_COMPLETE_CUSTOMER');?>:</td>
                <td valign="top"><textarea style="width:95%" name="booking_succeeded_sms_customer" rows="3" cols="70"><?php echo stripslashes($this->detail->booking_succeeded_sms_customer); ?></textarea></td>
                <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_SMS_BOOKING_COMPLETE_CUSTOMER_HELP');?></td>
              </tr>
              <tr >
                <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_SMS_BOOKING_COMPLETE');?>:</td>
                <td valign="top"><textarea style="width:95%" name="booking_succeeded_sms" rows="3" cols="70"><?php echo stripslashes($this->detail->booking_succeeded_sms); ?></textarea></td>
                <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_SMS_BOOKING_COMPLETE_HELP');?></td>
              </tr>
              <tr >
                <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_BOOKING_INPROGRESS');?>:</td>
                <td valign="top"><?php echo $editor->display( 'booking_in_progress',  $this->detail->booking_in_progress , '100%', '250', '75', '20', false ) ;?></td>
                <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_BOOKING_INPROGRESS_HELP');?></td>
              </tr>
              <tr >
                <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_BOOKING_INPROGRESS_ADMIN');?>:</td>
                <td valign="top"><?php echo $editor->display( 'booking_in_progress_admin',  $this->detail->booking_in_progress_admin , '100%', '250', '75', '20', false ) ;?></td>
                <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_BOOKING_INPROGRESS_ADMIN_HELP');?></td>
              </tr>
              <tr >
                <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_SMS_BOOKING_INPROGRESS');?>:</td>
                <td valign="top"><textarea style="width:95%" name="booking_in_progress_sms" rows="3" cols="70"><?php echo stripslashes($this->detail->booking_in_progress_sms); ?></textarea></td>
                <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_SMS_BOOKING_INPROGRESS_HELP');?></td>
              </tr>
              <tr >
                <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_CANCELLATION');?>: </td>
                <td valign="top"><?php echo $editor->display( 'booking_cancel',  $this->detail->booking_cancel , '100%', '250', '75', '20', false ) ;?></td>
                <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_CANCELLATION_HELP');?></td>
              </tr>
              <tr >
                <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_SMS_CANCELLATION');?>:</td>
                <td valign="top"><textarea style="width:95%" name="booking_cancel_sms" rows="3" cols="70"><?php echo stripslashes($this->detail->booking_cancel_sms); ?></textarea></td>
                <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_SMS_CANCELLATION_HELP');?></td>
              </tr>
              <tr >
                <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_TOO_LATE');?>:</td>
                <td valign="top"><?php echo $editor->display( 'booking_too_close_to_cancel',  $this->detail->booking_too_close_to_cancel , '100%', '150', '75', '20', false ) ;?></td>
                <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_TOO_LATE_HELP');?></td>
              </tr>
              <tr >
                <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_REMINDER');?>:</td>
                <td valign="top"><?php echo $editor->display( 'booking_reminder',  $this->detail->booking_reminder , '100%', '250', '75', '20', false ) ;?></td>
                <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_REMINDER_HELP');?></td>
              </tr>
              <tr >
                <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_SMS_REMINDER');?>:</td>
                <td valign="top"><textarea style="width:95%" name="booking_reminder_sms" rows="3" cols="70"><?php echo stripslashes($this->detail->booking_reminder_sms); ?></textarea></td>
                <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_SMS_REMINDER_HELP');?></td>
              </tr>
	      <tr>
	        <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_THANKYOU');?>:</td>
	        <td valign="top"><?php echo $editor->display( 'thank_you_msg',  $this->detail->thank_you_msg , '100%', '250', '75', '20' ) ;?></td>
	        <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_THANKYOU_HELP');?></td>
	      </tr>
	      <tr>
	        <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_THANKYOU_STATUS');?>:</td>
	        <td valign="top"><input type="text" size="20" maxsize="20" name="send_on_status" value="<?php echo $this->detail->send_on_status; ?>" /></td>
	        <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_THANKYOU_STATUS_HELP');?></td>
		  </tr>
	      <tr>
	        <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_NOTIFICATION');?>:</td>
	        <td valign="top"><?php echo $editor->display( 'notification_msg',  $this->detail->notification_msg , '100%', '250', '75', '20' ) ;?></td>
	        <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_NOTIFICATION_HELP');?></td>
	      </tr>
          <?php if($this->detail->mail_label == "Global"){ // birthday messasges are not resource related so only set it in Global ?>
	      <tr>
	        <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_BIRTHDAY');?>:</td>
	        <td valign="top"><?php echo $editor->display( 'birthday_msg',  $this->detail->birthday_msg , '100%', '250', '75', '20' ) ;?></td>
	        <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_BIRTHDAY_HELP');?></td>
	      </tr>
          <?php } ?>
              <tr>
                <td valign="top" colspan="3"><hr /></td>
              </tr>
              <tr >
                <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_TOKEN_INTRO');?></td>
                <td colspan="2"><table  border="0" cellpadding="4">
                    <tr>
                      <td><strong><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_TOKEN');?></strong></td>
                      <td><strong><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_TOKEN_REPLACE');?></strong></td>
                      <td width="5%">&nbsp;</td>
                      <td><strong><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_TOKEN');?></strong></td>
                      <td><strong><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_TOKEN_REPLACE');?></strong></td>
                    </tr>
                    <tr>
                      <td>[resource]</td>
                      <td><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_TOKEN_1');?></td>
                      <td>&nbsp;</td>
                      <td>[resource_category]</td>
                      <td><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_TOKEN_2');?></td>
                    </tr>
                    <tr>
                      <td>[requester name]</td>
                      <td><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_TOKEN_3');?></td>
                      <td>&nbsp;</td>
                      <td>[resource_service]</td>
                      <td><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_TOKEN_4');?></td>
                    </tr>
                    <tr>
                      <td>[startdate]</td>
                      <td><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_TOKEN_5');?></td>
                      <td>&nbsp;</td>
                      <td>[phone]</td>
                      <td><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_TOKEN_6');?></td>
                    </tr>
                    <tr>
                      <td>[starttime]</td>
                      <td><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_TOKEN_7');?></td>
                      <td>&nbsp;</td>
                      <td>[email]</td>
                      <td><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_TOKEN_8');?></td>
                    </tr>
                    <tr>
                      <td>[enddate]</td>
                      <td><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_TOKEN_9');?></td>
                      <td>&nbsp;</td>
                      <td>[cancellation_id]</td>
                      <td><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_TOKEN_10');?></td>
                    </tr>
                    <tr>
                      <td>[endtime]</td>
                      <td><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_TOKEN_11');?></td>
                      <td>&nbsp;</td>
                      <td>[booking_total]</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr>
                      <td>[booked_seats]</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>[booking_due]</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr>
                      <td>[coupon]</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>[booking_id]</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr>
                      <td>[today]</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>[booking_deposit]</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr>
                      <td>[cancel_reason]</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>[res_id]</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr>
                      <td>[admin_comment]</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>[manual_payment_collected]</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr>
                      <td colspan="5"><hr /></td>
                    </tr>
                    <tr>
                      <td><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_TOKEN_UDF_1');?></td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_TOKEN_UDF_2');?></td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr>
                      <td><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_TOKEN_UDF_3');?></td>
                      <td><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_TOKEN_UDF_4');?></td>
                      <td>&nbsp;</td>
                      <td><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_TOKEN_UDF_5');?></td>
                      <td><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_TOKEN_UDF_6');?></td>
                    </tr>
                    <tr>
                      <td colspan="5"><hr /></td>
                    </tr>
                    <tr>
                      <td><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_TOKEN_EXTRAS_1');?></td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_TOKEN_EXTRAS_2');?></td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr>
                      <td><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_TOKEN_EXTRAS_3');?></td>
                      <td><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_TOKEN_EXTRAS_4');?></td>
                      <td>&nbsp;</td>
                      <td><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_TOKEN_EXTRAS_5');?></td>
                      <td><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_TOKEN_EXTRAS_6');?></td>
                    </tr>
                    <tr>
                      <td colspan="5"><hr /></td>
                    </tr>
                    <tr>
                    <tr>
                      <td><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_TOKEN_SEATS_1');?></td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_TOKEN_SEATS_2');?></td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr>
                      <td><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_TOKEN_SEATS_3');?></td>
                      <td><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_TOKEN_SEATS_4');?></td>
                      <td>&nbsp;</td>
                      <td><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_TOKEN_SEATS_5');?></td>
                      <td><?php echo JText::_('RS1_ADMIN_CONFIG_MSG_TOKEN_SEATS_6');?></td>
                    </tr>
                  </table>
                  <p>&nbsp;</p></td>
              </tr>
            </table>
            </table>

</fieldset>
<input type = "hidden" name="id_mail" value="<?php echo $this->detail->id_mail; ?>" />
<input type = "hidden" name="task" value="" />
<input type = "hidden" name="controller" value="mail_detail" />
<input type = "hidden" name="published" value="<?php echo $this->detail->published; ?>" />
<br />
  <span style="font-size:10px"> Appointment Booking Pro Ver. 4.0.5 - Copyright 2008-20<?php echo date("y");?> - <a href='http://www.softventures.com' target="_blank">Soft Ventures, Inc.</a></span>
</form>
		
