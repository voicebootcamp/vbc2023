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

JHtml::_('jquery.framework');

$jinput = JFactory::getApplication()->input;
	$cur_res = $jinput->getInt( 'resource_id' );
	// Get resources for dropdown list
	$database = JFactory::getDBO();
	try{
		$database->setQuery("SELECT * FROM #__sv_apptpro3_resources ORDER BY name" );
		$res_rows = $database -> loadObjectList();
	} catch (RuntimeException $e) {
		logIt($e->getMessage(), "be_bo_detail_tmpl_form", "", "");
		echo JText::_('RS1_SQL_ERROR').$e->getMessage();
		exit;
	}		
	// get config stuff
	$database = JFactory::getDBO();
	$sql = 'SELECT * FROM #__sv_apptpro3_config';
	try{
		$database->setQuery($sql);
		$apptpro_config = NULL;
		$apptpro_config = $database -> loadObject();
	} catch (RuntimeException $e) {
		logIt($e->getMessage(), "be_bo_detail_tmpl_form", "", "");
		echo JText::_('RS1_SQL_ERROR').$e->getMessage();
		exit;
	}		
	$div_cal = "";
	if($apptpro_config->use_div_calendar == "Yes"){
		$div_cal = "'testdiv1'";
	}

	$daily_note = "";
	$rolling_days = array("1","1","1","1","1","1","1");
	if($this->detail->rolling_bookoff != "No"){
		$daily_note = JText::_('RS1_ADMIN_SCRN_BO_DAILY_DATE_NOTE');
		$rolling_days = explode(",", $this->detail->rolling_bookoff);
	}
?>
<div id="testdiv1" style="VISIBILITY: hidden; POSITION: absolute; BACKGROUND-COLOR: white; layer-background-color: white; z-index:99999"> </div>
<link href="<?php echo JURI::root( true );?>/components/com_rsappt_pro3/calStyles.css" rel="stylesheet">
<script language="JavaScript" src="<?php echo JURI::root( true );?>/components/com_rsappt_pro3/CalendarPopup.js"></script>
<script language="JavaScript" src="<?php echo JURI::root( true );?>/components/com_rsappt_pro3/date.js"></script>
<script src="https://code.jquery.com/jquery-3.4.1.js"></script>
<script language="JavaScript">
	var now = new Date();
	var cal = new CalendarPopup( <?php echo $div_cal ?>);
	cal.setCssPrefix("TEST");
	cal.setWeekStartDay(<?php echo $apptpro_config->popup_week_start_day ?>);

window.onload = function() {
  changeDaily();
};

$(document).ready(function(){
  $("form").submit(function(){
		pressbutton = document.adminForm.task.value;  
		var ok = "yes";
		if (pressbutton == 'save' || pressbutton == 'save2new'){
			if(document.getElementById("resource_id").selectedIndex == 0){
				alert("Please select a resource.");
				ok = "no";
			}
			if(document.getElementById("off_date").value == ""){
				alert("Please select a date.");
				ok = "no";
			}
			if(document.getElementById("off_date2")!=null){
				if(ok == "yes" && document.getElementById("off_date2").value != ""){				
					// need to create a series or bookoffs			
					// limit to max 14 days
	//				if(Date.parse(document.getElementById("off_date2").value) > Date.parse(document.getElementById("off_date").value).add(14).days()){
	//					alert("Maximum number of book-off days that can be created at one time is 15");
	//					ok="no";
	//				}
					pressbutton = 'create_bookoff_series'
				}
			}
			if(ok == "yes"){
				if(document.getElementById('rolling_bookoff_select').value != "No"){
					document.getElementById('full_day').disabled = false; // if changing daily_bookoff disabled full_day we need to re-enable it so its No value gets saved.
				}
				Joomla.submitform(pressbutton);
			} else {
				return false;
			}
		} else {
			Joomla.submitform(pressbutton);
		}		
  });
});

function setbookoffstarttime(){
	document.getElementById("bookoff_starttime").value = document.getElementById("bookoff_starttime_hour").value + ":" + document.getElementById("bookoff_starttime_minute").value + ":00";
}
function setbookoffendtime(){
	document.getElementById("bookoff_endtime").value = document.getElementById("bookoff_endtime_hour").value + ":" + document.getElementById("bookoff_endtime_minute").value + ":00";
}

function setBODay(which_day){
	if(document.getElementById('chk'+which_day).checked==true){
		document.getElementById('allow'+which_day).value = "Yes";
	} else {
		document.getElementById('allow'+which_day).value = "No";
	}
	// ensure at least one day is checked
	if(document.getElementById('chkSunday').checked==false 
		&& document.getElementById('chkMonday').checked==false
		&& document.getElementById('chkTuesday').checked==false
		&& document.getElementById('chkWednesday').checked==false
		&& document.getElementById('chkThursday').checked==false
		&& document.getElementById('chkFriday').checked==false
		&& document.getElementById('chkSaturday').checked==false){
		alert("You cannot un-check ALL days.");
		document.getElementById('chk'+which_day).checked=true
	}			
	return true;
}

function changeDaily(){
	if(document.getElementById('rolling_bookoff_select').value === "No"){
		document.getElementById('rolling_bookoff').value = "No";
		document.getElementById('full_day').disabled = false;
		document.getElementById('anchor1').disabled = false;
		document.getElementById('daily_bookoff_help').innerHTML = "";
		if(document.getElementById("bo_days")!=null){
			document.getElementById("bo_days").style.visibility = "visible";
			document.getElementById("bo_days").style.display = "";
		}
		document.getElementById("rolling_days_table").style.visibility = "hidden";
		document.getElementById("rolling_days_table").style.display = "none";
		
	} else {
		document.getElementById('full_day').value = "No";
		document.getElementById('full_day').disabled = true;
		document.getElementById('daily_bookoff_help').innerHTML = "<?php echo JText::_('RS1_ADMIN_SCRN_BO_DAILY_DATE_NOTE');?>";
		document.getElementById('off_date').value = Date.today().toString("yyyy-MM-dd")
		document.getElementById("rolling_days_table").style.visibility = "visible";
		document.getElementById("rolling_days_table").style.display = "";
		
		if(document.getElementById("bo_days")!=null){
			document.getElementById("bo_days").style.visibility = "hidden";
			document.getElementById("bo_days").style.display = "none";
			document.getElementById('off_date2').value = Date.today().toString("yyyy-MM-dd")
		}
		// build string from checkboxes
		var day_filter = "";
		if(document.getElementById('chkRollingSunday').checked==true){
			day_filter += "1,";
		} else {
			day_filter += "0,";
		}
		if(document.getElementById('chkRollingMonday').checked==true){
			day_filter += "1,";
		} else {
			day_filter += "0,";
		}
		if(document.getElementById('chkRollingTuesday').checked==true){
			day_filter += "1,";
		} else {
			day_filter += "0,";
		}
		if(document.getElementById('chkRollingWednesday').checked==true){
			day_filter += "1,";
		} else {
			day_filter += "0,";
		}
		if(document.getElementById('chkRollingThursday').checked==true){
			day_filter += "1,";
		} else {
			day_filter += "0,";
		}
		if(document.getElementById('chkRollingFriday').checked==true){
			day_filter += "1,";
		} else {
			day_filter += "0,";
		}
		if(document.getElementById('chkRollingSaturday').checked==true){
			day_filter += "1";
		} else {
			day_filter += "0";
		}
		document.getElementById('rolling_bookoff').value = day_filter;
	}
}

</script>

<link href="<?php echo JURI::root( true );?>/components/com_rsappt_pro3/sv_apptpro.css" rel="stylesheet">
<link href="<?php echo JURI::root( true );?>/administrator/components/com_rsappt_pro3/abpro_admin.css" rel="stylesheet">
<form action="<?php echo JRoute::_($this->request_url) ?>" method="post" name="adminForm" id="adminForm">
<fieldset class="adminform">
<div id="sv_be_admin">
	<?php echo JText::_('RS1_ADMIN_SCRN_BOOKOFF_DETAIL_INTRO');?>
  <table class="table table-striped" >
    <tr>
      <td width="15%"><?php echo JText::_('RS1_ADMIN_SCRN_BOOKOFF_DETAIL_ID');?></td>
      <td><?php echo $this->detail->id_bookoffs ?></td>
      <td width="50%">&nbsp;</td>
    </tr>
    <tr>
      <td><?php echo JText::_('RS1_ADMIN_SCRN_BO_RESOURCE');?></td>
      <td colspan="3">
      <?php if($this->detail->resource_id == ""){ ?>
	      <select name="resource_id" id="resource_id">
          <option value="0" ><?php echo JText::_('RS1_ADMIN_SCRN_BO_RESOURCE_SEL');?></option>
              <?php
				$k = 0;
				for($i=0; $i < sv_count_($res_rows ); $i++) {
				$res_row = $res_rows[$i];
				?>
        	  <option value="<?php echo $res_row->id_resources; ?>"  <?php if($cur_res == $res_row->id_resources){echo " selected='selected' ";} ?>><?php echo stripslashes($res_row->name); ?></option>
              <?php $k = 1 - $k; 
				} ?>
    	  </select>
      <?php } else { ?>
      			<input type="hidden" name="resource_id" id="resource_id" value=<?php echo $this->detail->resource_id;?> />
              <?php
				$k = 0;
				for($i=0; $i < sv_count_($res_rows ); $i++) {
					$res_row = $res_rows[$i];
					if($this->detail->resource_id == $res_row->id_resources){
        	  			echo stripslashes($res_row->name);
              		}
					$k = 1 - $k; 
				}     		
      		} 
			?>    
      &nbsp; </td>
    </tr>
    <tr>
      <td><?php echo JText::_('RS1_ADMIN_SCRN_BO_DAILY');?> </td>
      <td><select name="rolling_bookoff_select" id="rolling_bookoff_select" onchange="changeDaily();">
            <option value="Yes" <?php if($this->detail->rolling_bookoff != "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
            <option value="No" <?php if($this->detail->rolling_bookoff == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
            </select>  
            <div id="rolling_days_table" style="visibility:hidden">    
            <table width="100%" border="0" cellspacing="0" cellpadding="0" >
                  <tr align="left">
                    <td width="15%" class="center"><?php echo JText::_('RS1_ADMIN_SCRN_SUN');?></td>
                    <td width="15%" class="center"><?php echo JText::_('RS1_ADMIN_SCRN_MON');?></td>
                    <td width="15%" class="center"><?php echo JText::_('RS1_ADMIN_SCRN_TUE');?></td>
                    <td width="15%" class="center"><?php echo JText::_('RS1_ADMIN_SCRN_WED');?></td>
                    <td width="15%" class="center"><?php echo JText::_('RS1_ADMIN_SCRN_THU');?></td>
                    <td width="15%" class="center"><?php echo JText::_('RS1_ADMIN_SCRN_FRI');?></td>
                    <td width="10%" class="center"><?php echo JText::_('RS1_ADMIN_SCRN_SAT');?></td>
                  </tr>
                  <tr>
                    <td class="center"><input type="checkbox" name="chkRollingSunday" id="chkRollingSunday" <?php echo ($rolling_days[0]=="1"?"checked":"")?> onchange="changeDaily();"/></td>
                    <td class="center"><input type="checkbox" name="chkRollingMonday" id="chkRollingMonday" <?php echo ($rolling_days[1]=="1"?"checked":"")?> onchange="changeDaily();"/></td>
                    <td class="center"><input type="checkbox" name="chkRollingTuesday" id="chkRollingTuesday" <?php echo ($rolling_days[2]=="1"?"checked":"")?> onchange="changeDaily();"/></td>
                    <td class="center"><input type="checkbox" name="chkRollingWednesday" id="chkRollingWednesday" <?php echo ($rolling_days[3]=="1"?"checked":"")?> onchange="changeDaily();"/></td>
                    <td class="center"><input type="checkbox" name="chkRollingThursday" id="chkRollingThursday" <?php echo ($rolling_days[4]=="1"?"checked":"")?> onchange="changeDaily();"/></td>
                    <td class="center"><input type="checkbox" name="chkRollingFriday" id="chkRollingFriday" <?php echo ($rolling_days[5]=="1"?"checked":"")?> onchange="changeDaily();"/></td>
                    <td class="center"><input type="checkbox" name="chkRollingSaturday" id="chkRollingSaturday" <?php echo ($rolling_days[6]=="1"?"checked":"")?> onchange="changeDaily();"/></td>
              </tr>
         </table>
         </div>
		<input type="hidden" id="rolling_bookoff" name="rolling_bookoff" value="<?php echo $this->detail->rolling_bookoff;?>"/>
            </td>
      <td ><?php echo JText::_('RS1_ADMIN_SCRN_BO_DAILY_HELP');?></td>
    </tr>
    <tr>
      <td valign="top"><?php echo JText::_('RS1_ADMIN_SCRN_BO_DATE');?></td>
      <td valign="top"><input class="sv_date_box"  type="text" size="12" maxsize="10" readonly="readonly" name="off_date" id="off_date" value="<?php echo $this->detail->off_date; ?>" />
		        <a href="#" id="anchor1" onclick="cal.select(document.forms['adminForm'].off_date,'anchor1','yyyy-MM-dd'); return false;"
					 name="anchor1"><img height="15" hspace="2" src="<?php echo JURI::root( true );?>/components/com_rsappt_pro3/icon_cal.gif" width="16" border="0"></a>
           <?php if($this->detail->id_bookoffs == ""){ ?>
            <div id="svlabel"><?php echo JText::_('RS1_ADMIN_SCRN_BO_DATE_TO');?></div><input class="sv_date_box" type="text" size="12" maxsize="10" readonly="readonly" name="off_date2" id="off_date2" value="" />
		        <a href="#" id="anchor2" onclick="cal.select(document.forms['adminForm'].off_date2,'anchor2','yyyy-MM-dd'); return false;"
					 name="anchor2"><img height="15" hspace="2" src="<?php echo JURI::root( true );?>/components/com_rsappt_pro3/icon_cal.gif" width="16" border="0"></a>
            
            </td><td><?php echo JText::_('RS1_ADMIN_SCRN_BO_DATE_HELP');?><label id="daily_bookoff_help"><?php echo $daily_note;?></label></td>
            <tr id="bo_days"><td><?php echo JText::_('RS1_ADMIN_SCRN_BO_DATE_DAYS');?></td><td>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr align="left">
                    <td width="15%" class="center"><?php echo JText::_('RS1_ADMIN_SCRN_SUN');?></td>
                    <td width="15%" class="center"><?php echo JText::_('RS1_ADMIN_SCRN_MON');?></td>
                    <td width="15%" class="center"><?php echo JText::_('RS1_ADMIN_SCRN_TUE');?></td>
                    <td width="15%" class="center"><?php echo JText::_('RS1_ADMIN_SCRN_WED');?></td>
                    <td width="15%" class="center"><?php echo JText::_('RS1_ADMIN_SCRN_THU');?></td>
                    <td width="15%" class="center"><?php echo JText::_('RS1_ADMIN_SCRN_FRI');?></td>
                    <td width="10%" class="center"><?php echo JText::_('RS1_ADMIN_SCRN_SAT');?></td>
                  </tr>
                  <tr>
                    <td class="center"><input type="checkbox" name="chkSunday" id="chkSunday" checked /></td>
                    <td class="center"><input type="checkbox" name="chkMonday" id="chkMonday" checked /></td>
                    <td class="center"><input type="checkbox" name="chkTuesday" id="chkTuesday" checked /></td>
                    <td class="center"><input type="checkbox" name="chkWednesday" id="chkWednesday" checked /></td>
                    <td class="center"><input type="checkbox" name="chkThursday" id="chkThursday" checked /></td>
                    <td class="center"><input type="checkbox" name="chkFriday" id="chkFriday" checked /></td>
                    <td class="center"><input type="checkbox" name="chkSaturday" id="chkSaturday" checked /></td>
              </tr>
              </table>            

            </td><td><?php echo JText::_('RS1_ADMIN_SCRN_BO_DATE_DAYS_HELP');?></td></tr> 
			<?php } else { ?> <td><label id="daily_bookoff_help"><?php echo $daily_note;?></label></td>  <?php } ?>
            </td>
    </tr>
    <tr>
      <td><?php echo JText::_('RS1_ADMIN_SCRN_BO_FULLDAY');?> </td>
      <td><select name="full_day" id="full_day" <?php echo ($this->detail->rolling_bookoff=="Yes"?"disabled='disabled'":"");?>>
            <option value="Yes" <?php if($this->detail->full_day == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
            <option value="No" <?php if($this->detail->full_day == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
            </select>      </td>
      <td rowspan="3"><?php echo JText::_('RS1_ADMIN_SCRN_BO_RANGE_HELP');?></td>
    </tr>
    <tr>
      <td><?php echo JText::_('RS1_ADMIN_SCRN_BO_RANGE_START');?> </td>
      <td><div style="display: table-cell;"><select style="width:auto; min-width:50px" name="bookoff_starttime_hour" id="bookoff_starttime_hour" onchange="setbookoffstarttime();" class="sv_ts_request_dropdown" >
		<?php
		for($x=0; $x<24; $x+=1){
			if($x<10){
				$x = "0".$x;
			}
			echo "<option value=".$x; if(substr($this->detail->bookoff_starttime,0,2) == $x) {echo " selected='selected' ";} echo ">".$x." </option>";  
		}
		?>
        </select></div><div style="display: table-cell; padding-left:5px; vertical-align:middle">:</div>
		<div style="display: table-cell; padding-left:5px;"><select style="width:auto; min-width:50px" name="bookoff_starttime_minute" id="bookoff_starttime_minute" onchange="setbookoffstarttime();" class="sv_ts_request_dropdown" >
		<?php
		for($x=0; $x<59; $x+=5){
			if($x<10){
				$x = "0".$x;
			}
			echo "<option value=".$x; if(substr($this->detail->bookoff_starttime,3,2) == $x) {echo " selected='selected' ";} echo ">".$x." </option>";  
		}
		?>
        </select></div>
		<div style="display: table-cell; padding-left:5px; vertical-align:middle"><?php echo JText::_('RS1_ADMIN_SCRN_HHMM');?></div>
        <input type="hidden" name="bookoff_starttime" id="bookoff_starttime" value="<?php echo $this->detail->bookoff_starttime ?>" />      </td>
    </tr>
    <tr>
      <td><?php echo JText::_('RS1_ADMIN_SCRN_BO_RANGE_END');?> </td>
      <td><div style="display: table-cell;"><select style="width:auto; min-width:50px" name="bookoff_endtime_hour" id="bookoff_endtime_hour" onchange="setbookoffendtime();" class="sv_ts_request_dropdown" >
		<?php
		for($x=0; $x<24; $x+=1){
			if($x<10){
				$x = "0".$x;
			}
			echo "<option value=".$x; if(substr($this->detail->bookoff_endtime,0,2) == $x) {echo " selected='selected' ";} echo ">".$x." </option>";  
		}
		?>
        </select></div><div style="display: table-cell; padding-left:5px; vertical-align:middle">:</div> 
		<div style="display: table-cell; padding-left:5px;"><select style="width:auto; min-width:50px" name="bookoff_endtime_minute" id="bookoff_endtime_minute" onchange="setbookoffendtime();" class="sv_ts_request_dropdown" >
		<?php
		for($x=0; $x<59; $x+=5){
			if($x<10){
				$x = "0".$x;
			}
			echo "<option value=".$x; if(substr($this->detail->bookoff_endtime,3,2) == $x) {echo " selected='selected' ";} echo ">".$x." </option>";  
		}
		?>
        </select></div>
		<div style="display: table-cell; padding-left:5px; vertical-align:middle"><?php echo JText::_('RS1_ADMIN_SCRN_HHMM');?></div>
        <input type="hidden" name="bookoff_endtime" id="bookoff_endtime" value="<?php echo $this->detail->bookoff_endtime ?>" />      </td>
    </tr>
    <tr>
      <td><?php echo JText::_('RS1_ADMIN_SCRN_BO_DESC');?> </td>
      <td><input type="text" style="width:90%" size="60" maxsize="80" name="description" value="<?php echo stripslashes($this->detail->description); ?>" /></td>
      <td><?php echo JText::_('RS1_ADMIN_SCRN_BO_DESC_HELP');?>&nbsp;</td>
    </tr>
    <tr>
        <td ><?php echo JText::_('RS1_ADMIN_SCRN_BOOKOFF_DETAIL_PUBLISHED');?></td>
        <td>
            <select name="published">
            <option value="0" <?php if($this->detail->published == "0"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
            <option value="1" <?php if($this->detail->published == "1"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
            </select>        </td>
        <td>&nbsp;</td>
    </tr>
    <tr>
      <td colspan="3" ><br /></td>
    </tr>  
  </table>
</fieldset>
  <input type="hidden" name="id_bookoffs" value="<?php echo $this->detail->id_bookoffs; ?>" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="controller" value="bookoffs_detail" />
	<input type="hidden" name="alt_cal_pos" id="alt_cal_pos" value="<?php echo $apptpro_config->cal_position_method; ?>" />
  <br />
  <span style="font-size:10px"> Appointment Booking Pro Ver. 4.0.5 - Copyright 2008-20<?php echo date("y");?> - <a href='http://www.softventures.com' target="_blank">Soft Ventures, Inc.</a></span>
</div>
</form>
