
function showInforForm(){
	var live_site = document.getElementById('live_site');
	showInforFormAjax(live_site.value);
}

function updatePayment(payment){
	var select_payment = document.getElementById('select_payment');
	select_payment.value =  payment;
}

function loadServices(year,month,day){
	var live_site = document.getElementById('live_site');
	var select_day = document.getElementById('select_day');
	var select_month = document.getElementById('select_month');
	var select_year = document.getElementById('select_year');
	
	var cday = document.getElementById('day');
	var cmonth = document.getElementById('month');
	var cyear = document.getElementById('year');
	cday = cday.value;
	cmonth = cmonth.value;
	cyear = cyear.value;
	
	sday = select_day.value;
	smonth = select_month.value;
	syear = select_year.value;
	if((sday != "") && (smonth != "") && (syear != "")){
		var spantag = document.getElementById('a' + syear + smonth + sday);
		var screenWidth = jQuery(window).width();
		if(spantag != null){
			if((cday == sday) && (cmonth == smonth) && (cyear == syear)){
				if(screenWidth < 350){
					spantag.className = document.getElementById('calendar_currentdate_style').value;
					jQuery('#a' + syear + smonth + sday).removeClass("buttonpadding10").addClass("buttonpadding5");
				}else{
					spantag.className = document.getElementById('calendar_currentdate_style').value;
					jQuery('#a' + syear + smonth + sday).removeClass("buttonpadding5").addClass("buttonpadding10");
				}
			}else{
				if(screenWidth < 350){
					spantag.className = document.getElementById('calendar_normal_style').value;
					jQuery('#a' + syear + smonth + sday).removeClass("buttonpadding10").addClass("buttonpadding5");
				}else{
					spantag.className = document.getElementById('calendar_normal_style').value;
					jQuery('#a' + syear + smonth + sday).removeClass("buttonpadding5").addClass("buttonpadding10");
				}
			}
		}
	}
	
	var screenWidth = jQuery(window).width();
	select_day.value = day;
	select_month.value = month;
	select_year.value = year;
	var spantag = document.getElementById('a' + year + month + day);
	if(spantag != null){
		if(screenWidth < 350){
			spantag.className = document.getElementById('calendar_activate_style').value;
			jQuery('#a' + year + month + day).removeClass("buttonpadding10").addClass("buttonpadding5");
		}else{
			spantag.className = document.getElementById('calendar_activate_style').value;
			jQuery('#a' + year + month + day).removeClass("buttonpadding5").addClass("buttonpadding10");
		}
	}
	day = parseInt(day);
	loadServicesAjax(live_site.value,year,month,day);
}

function selectEmployee(sid,year,month,day){
	var live_site = document.getElementById('live_site');
	selectEmployeeAjax(live_site.value,year,month,day,sid);
}

function closeForm(d,m,y){
	var live_site = document.getElementById('live_site');
	var select_day = document.getElementById('select_day');
	var select_month = document.getElementById('select_month');
	var select_year = document.getElementById('select_year');
	if(select_day.value == ""){
		var sday = d;
	}else{
		var sday = select_day.value;
	}
	if(select_month.value == ""){
		var smonth = m;
	}else{
		var smonth = select_month.value;
	}
	if(select_year.value == ""){
		var syear = y;
	}else{
		var syear = select_year.value;
	}
	loadServicesAjax(live_site.value,syear,smonth,sday);
}

function updateMonth(month){
	document.getElementById('ossmh').value = month;
}

function updateYear(year){
	document.getElementById('ossyh').value = year;
}
function updateMonthCalendarView(month){
	document.getElementById('month').value = month;
}

function updateYearCalendarView(year){
	document.getElementById('year').value = year;
}

/*
function calendarMovingSmall(){
	var ossmh = document.getElementById('ossmh');
	var ossyh  = document.getElementById('ossyh');
	sm     = ossmh.value;
	sy     = ossyh.value;
	
	var month = document.getElementById('month');
	var year  = document.getElementById('year');
	month 	  = month.value;
	year	  = year.value;
	var div   = document.getElementById("cal" + month + year);
	div.style.display = "none";
	
	div   = document.getElementById("cal" + sm + sy);
	div.style.display = "block";	
	
	document.getElementById('month').value = sm;
	document.getElementById('year').value = sy;
}
*/

function addBookingMultiple(book_time,end_booktime,start,end,eid,sid,summaryTxt,fromTxt,toTxt,avail)
{
    var optionvalue     = book_time + "-" + end_booktime;
    jQuery("#multiple_" + sid + "_" + eid + " > option").each(function(){
		if(jQuery(this).val() == optionvalue)
		{
			if(jQuery(this).attr("selected") === true || jQuery(this).attr("selected") == "selected")
			{
                jQuery(this).removeAttr("selected");
			}
			else
			{
                jQuery(this).attr("selected","true");
			}
		}
    });
    var max_seats		= document.getElementById('max_allowed_seats_' + sid);
    if(max_seats != null){
        max_seats.value = avail;
    }
    updateSummaryMultiple(eid,sid,summaryTxt);

}

function addBooking(book_time,end_booktime,start,end,eid,sid,summaryTxt,fromTxt,toTxt,avail)
{
	var bookitem 		= document.getElementById('book_' + sid +  '_' + eid);
	bookitem.value 		= book_time;
	var endbooktime 	= document.getElementById('end_book_' + sid +  '_' + eid);
	endbooktime.value 	= end_booktime;
	var startitem 		= document.getElementById('start_' + sid +  '_' + eid);
	startitem.value 	= start;
	var enditem 		= document.getElementById('end_' + sid +  '_' + eid);
	enditem.value 		= end;
	var max_seats		= document.getElementById('max_allowed_seats_' + sid);
	if(max_seats != null){
		max_seats.value = avail;
	}
	updateSummary(eid,sid,summaryTxt,fromTxt,toTxt);
}

function addBookingSimple(book_time,end_booktime,start,end,eid,sid,summaryTxt,fromTxt,toTxt,timeslot,bgcolor,avail)
{
    var bookitem 		= document.getElementById('book_' + sid +  '_' + eid);
    bookitem.value 		= book_time;
    var endbooktime 	= document.getElementById('end_book_' + sid +  '_' + eid);
    endbooktime.value 	= end_booktime;
    var startitem 		= document.getElementById('start_' + sid +  '_' + eid);
    startitem.value 	= start;
    var enditem 		= document.getElementById('end_' + sid +  '_' + eid);
    enditem.value 		= end;
    var temp_item       = document.getElementById('temp_item');
    if(temp_item.value != ""){
        var temp = document.getElementById(temp_item.value);
        if(temp != null){
            temp.style.backgroundColor  = bgcolor;
        }

    }
    temp_item.value = timeslot;
    var booked_timeslot_background	= document.getElementById('booked_timeslot_background');
	if(booked_timeslot_background != null)
	{
		booked_timeslot_background = booked_timeslot_background.value;
	}
	if(booked_timeslot_background == '')
	{
		booked_timeslot_background = 'red';
	}
    document.getElementById(timeslot).style.backgroundColor  = booked_timeslot_background;
	var max_seats		= document.getElementById('max_allowed_seats_' + sid);
	if(max_seats != null){
		max_seats.value = avail;
	}
    updateSummary(eid,sid,summaryTxt,fromTxt,toTxt);
}

function checkNumberSlotsSelected(sid,eid){
	var alrmsg = document.getElementById('alrmsg');
	alrmsg = alrmsg.value;
	alrmsg = alrmsg.split('|');
	var nslots = document.getElementById('nslots_' + sid + '_' + eid);
	nslots_value = nslots.value;
	if(isNaN(nslots_value)){
		alert(alrmsg[0]);
		return false; 
	}else{
		var max_seats = document.getElementById('max_seats_' + sid);
		var max_allowed_seats = document.getElementById('max_allowed_seats_' + sid);
		if(max_seats != null){
			max_seats_value = max_seats.value;
			if(parseInt(max_seats_value) > 0){
				if(max_allowed_seats != null){
					max_allow_seats_value = max_allowed_seats.value;
					max_allow_seats_value = parseInt(max_allow_seats_value);
					nslots_value		  = parseInt(nslots_value);
					if(max_allow_seats_value != ""){
						if(nslots_value > max_allow_seats_value){
							alert(alrmsg[1]);
							return false;
						}else if(nslots_value > max_seats_value){
							alert(alrmsg[2]);
							return false;
						}
					}else{
						alert(alrmsg[3]);
						return false;
					}
				}
			}
		}
	}
}

function updateDeposit(amount,label){
	document.getElementById('deposit_label').innerHTML = label;
	document.getElementById('deposit_value').innerHTML = amount;
	document.getElementById('payfull').value = 1;
}

function updateSelectlist(sid,eid,fieldid,summaryTxt,fromTxt,toTxt){
	var element = document.getElementById('field_' + sid + '_' + eid + '_' + fieldid + '_selectlist');
	//alert(element.value);
	if(element.value != ""){
		temp1 = element.value;
		pos = temp1.indexOf("||");
		if(pos > 0){
			id = temp1.substring(0,pos);
			value = temp1.substring(pos + 2);
			var element = document.getElementById('field_' + sid + '_' + eid + '_' + fieldid);
			element.value = value;
			var element = document.getElementById('field_' + sid + '_' + eid + '_' + fieldid  + '_selected');
			element.value = id;
		}
	}else{
		var element = document.getElementById('field_' + sid + '_' + eid + '_' + fieldid);
		element.value = "";
		var element = document.getElementById('field_' + sid + '_' + eid + '_' + fieldid  + '_selected');
		element.value = "";
	}
	updateSummary(eid,sid,summaryTxt,fromTxt,toTxt);
}

function updateSelectlistMultiple(sid,eid,fieldid,summaryTxt,fromTxt,toTxt){
	var element = document.getElementById('field_' + sid + '_' + eid + '_' + fieldid + '_selectlist');
	//alert(element.value);
	if(element.value != ""){
		temp1 = element.value;
		pos = temp1.indexOf("||");
		if(pos > 0){
			id = temp1.substring(0,pos);
			value = temp1.substring(pos + 2);
			var element = document.getElementById('field_' + sid + '_' + eid + '_' + fieldid);
			element.value = value;
			var element = document.getElementById('field_' + sid + '_' + eid + '_' + fieldid  + '_selected');
			element.value = id;
		}
	}else{
		var element = document.getElementById('field_' + sid + '_' + eid + '_' + fieldid);
		element.value = "";
		var element = document.getElementById('field_' + sid + '_' + eid + '_' + fieldid  + '_selected');
		element.value = "";
	}
	updateSummaryMultiple(eid,sid,summaryTxt);
}

function updateCheckbox(sid,eid,fieldid,summaryTxt,fromTxt,toTxt){
	var count = document.getElementById('field_' + sid + '_' + eid + '_' + fieldid + '_count');
	var temp,temp1;
	var str;
	str = "";
	vstr = "";
	for(i=0;i<parseInt(count.value);i++){
		temp = document.getElementById('field_' + sid + '_' + eid + '_' + fieldid + '_checkboxes' + i);
		if(temp.checked == true){
			temp1 = temp.value;
			pos   = temp1.indexOf("||");
			if(pos > 0){
				vid = temp1.substring(0, pos);
				vstr += vid + ",";
				str += temp1.substring(pos + 2) + ",";	
			}
		}
	}
	if(str != ""){
		str = str.substr(0,str.length -1);
	}
	if(vstr != ""){
		vstr = vstr.substr(0,vstr.length -1);
	}
	var element = document.getElementById('field_' + sid + '_' + eid + '_' + fieldid);
	element.value = str;
	var element = document.getElementById('field_' + sid + '_' + eid + '_' + fieldid + '_selected');
	element.value = vstr;
	updateSummary(eid,sid,summaryTxt,fromTxt,toTxt);
}

function updateCheckboxMultiple(sid,eid,fieldid,summaryTxt,fromTxt,toTxt){
	var count = document.getElementById('field_' + sid + '_' + eid + '_' + fieldid + '_count');
	var temp,temp1;
	var str;
	str = "";
	vstr = "";
	for(i=0;i<parseInt(count.value);i++){
		temp = document.getElementById('field_' + sid + '_' + eid + '_' + fieldid + '_checkboxes' + i);
		if(temp.checked == true){
			temp1 = temp.value;
			pos   = temp1.indexOf("||");
			if(pos > 0){
				vid = temp1.substring(0, pos);
				vstr += vid + ",";
				str += temp1.substring(pos + 2) + ",";	
			}
		}
	}
	if(str != ""){
		str = str.substr(0,str.length -1);
	}
	if(vstr != ""){
		vstr = vstr.substr(0,vstr.length -1);
	}
	var element = document.getElementById('field_' + sid + '_' + eid + '_' + fieldid);
	element.value = str;
	var element = document.getElementById('field_' + sid + '_' + eid + '_' + fieldid + '_selected');
	element.value = vstr;

	updateSummaryMultiple(eid,sid,summaryTxt);
}

function updateCheckboxOrderForm(fieldid){
	var count = document.getElementById('field_' + fieldid + '_count');
	var temp;
	var temp1;
	var str;
	str = "";
	for(i=0;i<parseInt(count.value);i++){
		temp = document.getElementById('field_' + fieldid + '_checkboxes' + i);
		if(temp.checked == true){
			temp1 = temp.value;
			temp1 = temp1.replace("&","(@)");
			str += temp1 + ",";
		}
	}
	if(str != ""){
		str = str.substr(0,str.length -1);
	}
	var element = document.getElementById('field_' + fieldid);
	element.value = str;
}

function openDiv(id){
	var div = document.getElementById('cartdetails_' +  id);
	var hrf = document.getElementById('href_' +  id);
	if(div.style.display == "none"){
		div.style.display  = "block";
		hrf.innerHTML = "[-]";
	}else{
		div.style.display  = "none";
		hrf.innerHTML = "[+]";
	}
}

function updateTempDate(sid,eid,start_time,end_time,year,month,day){
	var temp = document.getElementById('nslots_' + sid + '_' + eid + '_' + year + '_' +  month + '_' +  day);
	var value = temp.value;
	updateTempDateAjax(sid,eid,start_time,end_time,value);
}

function checkNumber(txtName,msg)
{			
	var num = txtName.value			
	if(isNaN(num))			
	{			
		alert(msg);			
		txtName.value = "";			
		txtName.focus();			
	}			
}

function removeTempTimeSlot(id,msg,live_site){
	var answer = confirm(msg);
	if(answer == 1){
		removeTempTimeSlotAjax(id,live_site);
	}
}

function updateSummary(eid,sid,summaryTxt,fromTxt,toTxt){
	var bookitem	= document.getElementById('book_' + sid + '_'  + eid);
	var startitem 	= document.getElementById('start_' + sid + '_' + eid);
	var enditem 	= document.getElementById('end_' + sid + '_' + eid);
	var summary 	= document.getElementById('summary_' + sid + '_' + eid);
	var str = "<strong><span color='red'>" + summaryTxt +  "</span></strong><BR>";
	if(bookitem.value != ""){
		str += fromTxt  + ": " + startitem.value + " " + toTxt + " " + enditem.value + "<BR />";
	}
	var field_ids   = document.getElementById('field_ids' + sid);
	if(field_ids != null){
		field_ids		= field_ids.value;
		if(field_ids != ""){
		var fieldArr 	= new Array();
			fieldArr 		= field_ids.split(",");
			var temp;
			var label;
			for(i=0;i<fieldArr.length;i++){
				temp = fieldArr[i];
				var element = document.getElementById('field_' + sid + '_' + eid + '_' + temp);
				if(element != null){
					label = document.getElementById('field_' + sid + '_' + eid + '_' + temp + '_label');
					if(element.value != ""){
						str += "<strong>" + label.value + ":</strong> " + element.value + "<BR />";
					}
				}
			}
		}
	}
	summary.innerHTML = str;
}

function updateSummaryMultiple(eid,sid,summaryTxt){
    var summary 	= document.getElementById('summary_' + sid + '_' + eid);
    var str = "<strong><span color='red'>" + summaryTxt +  "</span></strong><BR>";

    jQuery("#multiple_" + sid + "_" + eid + " option:selected").each(function () {
        str += jQuery(this).text() + "<BR />";
    });

    var field_ids   = document.getElementById('field_ids' + sid);
    if(field_ids != null)
    {
        field_ids		= field_ids.value;
        if(field_ids != "")
        {
            var fieldArr 	= new Array();
            fieldArr 		= field_ids.split(",");
            var temp;
            var label;
            for(i=0;i<fieldArr.length;i++)
            {
                temp = fieldArr[i];
                var element = document.getElementById('field_' + sid + '_' + eid + '_' + temp);
                if(element != null)
                {
                    label = document.getElementById('field_' + sid + '_' + eid + '_' + temp + '_label');
                    if(element.value != "")
                    {
                        str += "<strong>" + label.value + ":</strong> " + element.value + "<BR />";
                    }
                }
            }
        }
    }
    summary.innerHTML = str;
}

function validateEmail(form_id,email) {
   //var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
   var address = document.forms[form_id].elements[email].value;

   if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(address))
   {
	   return true;
   }
   else
   {
	   return false;
   }
}

function removerestday(item,day,eid,live_site){
	document.getElementById('current_item_value').value = item;
	removerestdayAjax(day,eid,item,live_site);
}
function addrestday(item,day,eid,live_site){
	document.getElementById('current_item_value').value = item;
	addrestdayAjax(day,eid,item,live_site);
}
function calendarMoving(eid){
	var ossmh = document.getElementById('ossm');
	var ossyh  = document.getElementById('ossy');
	sm     = ossmh.value;
	sy      = ossyh.value;
	document.getElementById('month').value = sm;
	document.getElementById('year').value = sy;
	location.href = "index.php?option=com_osservicesbooking&task=employee_availability&month=" +  sm  + "&year=" + sy + "&eid=" + eid;
}

function calendarMovingBigCal(employee,eid,live_site){
	var ossmh = document.getElementById('ossm');
	var ossyh  = document.getElementById('ossy');
	sm     = ossmh.value;
	sy     = ossyh.value;
	
	var month = document.getElementById('month');
	var year  = document.getElementById('year');
	
	document.getElementById('month').value = sm;
	document.getElementById('year').value = sy;
	var itemid = jQuery("#Itemid").val();
	var menuItemIdVar = '';
	if(itemid != '')
	{
		menuItemIdVar = '&Itemid=' + itemid;
	}
	if(employee==2)
	{
		location.href = live_site + "index.php?option=com_osservicesbooking&task=employee_availability&month=" +  sm  + "&year=" + sy + "&eid=" + eid + menuItemIdVar;
	}
	else if(employee == 0)
	{
		location.href = live_site + "index.php?option=com_osservicesbooking&task=calendar_customer&month=" +  sm  + "&year=" + sy + "&eid=" + eid + menuItemIdVar;
	}
	else
	{
		location.href = live_site + "index.php?option=com_osservicesbooking&task=calendar_employee&month=" +  sm  + "&year=" + sy + "&eid=" + eid + menuItemIdVar;
	}
}

function nextBigCal(employee,eid, live_site){
	var month = document.getElementById('month');
	var year  = document.getElementById('year');
	month 	  = month.value;
	year	  = year.value;
	if(month < 12){
		month = parseInt(month) + 1;
	}else{
		year  = parseInt(year) + 1;
		month = 1;
	}
	document.getElementById('month').value = month;
	document.getElementById('year').value = year;
	var itemid = jQuery("#Itemid").val();
	var menuItemIdVar = '';
	if(itemid != '')
	{
		menuItemIdVar = '&Itemid=' + itemid;
	}
	if(employee==2)
	{
		location.href = live_site + "index.php?option=com_osservicesbooking&task=employee_availability&month=" +  month  + "&year=" + year + "&eid=" + eid + menuItemIdVar;
	}
	else if(employee == 0)
	{
		location.href = live_site + "index.php?option=com_osservicesbooking&task=calendar_customer&month=" +  month  + "&year=" + year + "&eid=" + eid + menuItemIdVar;
	}
	else
	{
		location.href = live_site +  "index.php?option=com_osservicesbooking&task=calendar_employee&month=" +  month  + "&year=" + year + "&eid=" + eid + menuItemIdVar;
	}
}
function prevBigCal(employee,eid, live_site){
	var month = document.getElementById('month');
	var year  = document.getElementById('year');
	month 	  = month.value;
	year	  = year.value;
	if(month > 1){
		month = parseInt(month) - 1;
	}else{
		year  = parseInt(year) - 1;
		month = 12;
	}
	document.getElementById('month').value = month;
	document.getElementById('year').value = year;
	var itemid = jQuery("#Itemid").val();
	var menuItemIdVar = '';
	if(itemid != '')
	{
		menuItemIdVar = '&Itemid=' + itemid;
	}
	if(employee==2)
	{
		location.href = live_site + "index.php?option=com_osservicesbooking&task=employee_availability&month=" +  month  + "&year=" + year + "&eid=" + eid + menuItemIdVar; 
	}
	else if(employee == 0)
	{
		location.href = live_site + "index.php?option=com_osservicesbooking&task=calendar_customer&month=" +  month  + "&year=" + year + "&eid=" + eid + menuItemIdVar;
	}
	else
	{
		location.href = live_site + "index.php?option=com_osservicesbooking&task=calendar_employee&month=" +  month  + "&year=" + year + "&eid=" + eid + menuItemIdVar;
	}
}

function calendarMovingBigCal_front(employee,eid){
	var ossmh = document.getElementById('ossm');
	var ossyh  = document.getElementById('ossy');
	sm     = ossmh.value;
	sy     = ossyh.value;
	
	var month = document.getElementById('month');
	var year  = document.getElementById('year');
	
	document.getElementById('month').value = sm;
	document.getElementById('year').value = sy;
	
	var live_site = document.getElementById('live_site').value;

	var itemid = jQuery("#Itemid").val();
	var menuItemIdVar = '';
	if(itemid != '')
	{
		menuItemIdVar = '&Itemid=' + itemid;
	}
	
	if(employee==2)
	{
		location.href = live_site + "index.php?option=com_osservicesbooking&task=calendar_availability&month=" +  sm  + "&year=" + sy + "&eid=" + eid + menuItemIdVar;
	}
	else if(employee == 0)
	{
		location.href = live_site + "index.php?option=com_osservicesbooking&task=calendar_availability&month=" +  sm  + "&year=" + sy + "&eid=" + eid + menuItemIdVar;
	}
	else
	{
		location.href = live_site + "index.php?option=com_osservicesbooking&task=calendar_availability&month=" +  sm  + "&year=" + sy + "&eid=" + eid + menuItemIdVar;;
	}
}

function nextBigCal_front(employee,eid){
	var month = document.getElementById('month');
	var year  = document.getElementById('year');
	month 	  = month.value;
	year	  = year.value;
	if(month < 12){
		month = parseInt(month) + 1;
	}else{
		year  = parseInt(year) + 1;
		month = 1;
	}
	document.getElementById('month').value = month;
	document.getElementById('year').value = year;
	
	var live_site = document.getElementById('live_site').value;
	
	if(employee==2){
		location.href = live_site + "index.php?option=com_osservicesbooking&task=calendar_availability&month=" +  month  + "&year=" + year + "&eid=" + eid;
	}else if(employee == 0){
		location.href = live_site + "index.php?option=com_osservicesbooking&task=calendar_availability&month=" +  month  + "&year=" + year + "&eid=" + eid;
	}else{
		location.href = live_site + "index.php?option=com_osservicesbooking&task=calendar_availability&month=" +  month  + "&year=" + year + "&eid=" + eid;
	}
}
function prevBigCal_front(employee,eid){
	var month = document.getElementById('month');
	var year  = document.getElementById('year');
	month 	  = month.value;
	year	  = year.value;
	if(month > 1){
		month = parseInt(month) - 1;
	}else{
		year  = parseInt(year) - 1;
		month = 12;
	}
	document.getElementById('month').value = month;
	document.getElementById('year').value = year;
	
	var live_site = document.getElementById('live_site').value;
	
	if(employee==2){
		location.href = live_site + "index.php?option=com_osservicesbooking&task=calendar_availability&month=" +  month  + "&year=" + year + "&eid=" + eid;
	}else if(employee == 0){
		location.href = live_site + "index.php?option=com_osservicesbooking&task=calendar_availability&month=" +  month  + "&year=" + year + "&eid=" + eid;
	}else{
		location.href = live_site + "index.php?option=com_osservicesbooking&task=calendar_availability&month=" +  month  + "&year=" + year + "&eid=" + eid;
	}
}


function workingavailabilitystatus(live_site,itemid){
	location.href = live_site + "index.php?option=com_osservicesbooking&task=calendar_availability&Itemid=" + itemid;
}

function workingcalendar(live_site,itemid){
	location.href = live_site + "index.php?option=com_osservicesbooking&task=calendar_employee&Itemid=" + itemid;
}

function customercalendar(live_site,itemid){
	location.href = live_site + "index.php?option=com_osservicesbooking&task=calendar_customer&Itemid=" + itemid;
}

function customerbalances(live_site,itemid){
	location.href = live_site + "index.php?option=com_osservicesbooking&task=default_balances&Itemid=" + itemid;
}

function gcalendar(live_site,itemid){
	location.href = live_site + "index.php?option=com_osservicesbooking&task=calendar_gcalendar&Itemid=" + itemid;
}

function customerorder(live_site,itemid){
	location.href = live_site + "index.php?option=com_osservicesbooking&task=default_customer&Itemid=" + itemid;
}

function removeOrders(){
	document.ftForm.task.value = "manage_removeorders";
	document.ftForm.submit();
}

function openOtherInformation(id,text){
	var div  = document.getElementById("order" + id);
	var href = document.getElementById("href" + id);
	if(div.style.display == "none"){
		href.innerHTML = "[-]&nbsp;" + text;
		div.style.display = "block";
	}else{
		href.innerHTML = "[+]&nbsp;" + text;
		div.style.display = "none";
	}
}

function removeOrder(id,msg,live_site,itemid){
	var answer = confirm(msg);
	if(answer == 1){
		location.href = live_site + "index.php?option=com_osservicesbooking&task=default_removeorder&id=" + id + "&Itemid=" + itemid;
	}
}


function removeOrderItem(order_id,id,msg,live_site,itemid){
	var answer = confirm(msg);
	if(answer == 1){
		document.getElementById('oid').value = order_id;
		removeOrderItemAjax(order_id,id,live_site);
	}
}

function changeCheckin(order_id,id,live_site){
    document.getElementById('order_item_id').value = id;
    changeCheckinAjax(order_id,id,live_site);
}

function removeItemCalendar(order_id,order_item_id,sid,eid,i,date,msg,live_site){
	var answer = confirm(msg);
	if(answer == 1){
		document.getElementById("current_td").value = i;
		document.getElementById("date").value = date;
		removeOrderItemAjaxCalendar(order_id,order_item_id,i,date,live_site);
	}
}

function changeValue(item)
{
	var temp = document.getElementById(item);
	if(temp.value == 1)
	{
		temp.value = 0;
	}
	else
	{
		temp.value = 1;
	}
}

function remembertabs(){
	jQuery(document).ready(function(){
		var selected_sid = jQuery('#sid').val();
		selected_sid = parseInt(selected_sid);
		var navs = jQuery('#servicesTabs .nav-link');
		//alert(navs.length);
		jQuery('#servicesTabs a').click(function(){
			selected_sid = jQuery(this).attr('href');
			pos = selected_sid.indexOf('#pane');
			selected_sid = selected_sid.substring(pos + 5);
			jQuery('#sid').val(selected_sid);
		})
	
		var services = jQuery("#services").val();
		if(services != ''){
			var servicesArr = new Array();
			servicesArr = services.split(',');
			for(i=0;i< servicesArr.length;i++){
				jQuery('#employees' + servicesArr[i] +'Tabs a').click(function(){
					selected_eid = jQuery(this).attr('href');
					pos = selected_eid.indexOf('_');
					selected_eid = selected_eid.substring(pos + 1);
					jQuery('#eid').val(selected_eid);
				})
			}
		}
	});
}

function openWaitingList(link){
	var waitingWindow = window.open(link,'Waiting list','width=550,height=350,location=No,menubar=No,toolbar=No');
}

function openCommentForm(root_link, sid, eid){
	var myWindow = window.open(root_link + 'index.php?option=com_osservicesbooking&task=default_writecomment&sid=' + sid + '&eid=' + eid + '&tmpl=component','Leave comment here','width=380,height=450,location=no,menubar=no,status=no,toolbar=no,left=400,top=100');
}

function validateEmailAddress(email) {
    const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}


