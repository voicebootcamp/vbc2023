/*------------------------------------------------------------------------
# ajax.js - Ossolution Services Booking
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2022 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

var xmlHttp;

function nextCalendarView(sid){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		alert ("Browser does not support HTTP Request")
		return
	}
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
	var live_site = document.getElementById('live_site');
	var url = live_site.value + "index.php?option=com_osservicesbooking&task=ajax_showCalendarView&tmpl=component&month=" +  month  + "&year=" + year + "&id=" + sid;
	xmlHttp.onreadystatechange=ajaxcalendarView;
    xmlHttp.open("GET",url,true)
    xmlHttp.send(null)
}

function previousCalendarView(sid){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		alert ("Browser does not support HTTP Request")
		return
	}
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
	var live_site = document.getElementById('live_site');
	var url = live_site.value + "index.php?option=com_osservicesbooking&task=ajax_showCalendarView&tmpl=component&month=" +  month  + "&year=" + year + "&id=" + sid;
	xmlHttp.onreadystatechange=ajaxcalendarView;
    xmlHttp.open("GET",url,true)
    xmlHttp.send(null)
}

function movingCalendarView(sid){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		alert ("Browser does not support HTTP Request")
		return
	}
	var month = document.getElementById('month');
	var year  = document.getElementById('year');
	month 	  = month.value;
	year	  = year.value;
	var live_site = document.getElementById('live_site');
	var url = live_site.value + "index.php?option=com_osservicesbooking&task=ajax_showCalendarView&tmpl=component&month=" +  month  + "&year=" + year + "&id=" + sid;
	xmlHttp.onreadystatechange=ajaxcalendarView;
    xmlHttp.open("GET",url,true)
    xmlHttp.send(null)
}


function updateSearchForm(layout){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		alert ("Browser does not support HTTP Request")
		return
	}
	sidAdditionalLink			= "";
	categoryAdditionalLink		= "";
	vidAdditionalLink			= "";
	employeeAdditionalLink		= "";
	var sid 		    = document.osbsearchform.sid;
	if(sid != null)
	{
		sidAdditionalLink = "&sid=" + sid.value;
	}
	var category_id     = document.osbsearchform.category_id;
	if(category_id != null)
	{
		categoryAdditionalLink = "&category_id=" + category_id.value;
	}
	var vid				= document.osbsearchform.vid;
	if(vid != null)
	{
		vidAdditionalLink = "&vid=" + vid.value;
	}
	var employee_id     = document.osbsearchform.employee_id;
	if(employee_id != null)
	{
		employeeAdditionalLink = "&employee_id=" + employee_id.value;
	}

	var live_site       = document.getElementById('live_site');
    var show_category   = document.getElementById('show_category');
    var show_service    = document.getElementById('show_service');
    var show_venue      = document.getElementById('show_venue');
    var show_employee   = document.getElementById('show_employee');

	url = live_site.value + 'index.php?option=com_osservicesbooking&task=ajax_generateSearchmodule&' + sidAdditionalLink + categoryAdditionalLink + vidAdditionalLink  + employeeAdditionalLink + '&show_category=' + show_category.value + '&show_venue=' + show_venue.value + '&show_employee=' + show_employee.value + '&show_service=' + show_service.value + '&show_date=' + show_date.value + '&tmpl=component&format=raw&layout=' + layout;
	//alert(url);
	if(layout == 0)
	{
		xmlHttp.onreadystatechange=ajaxsearchmodule;
	}
	else
	{
		xmlHttp.onreadystatechange=ajaxsearchmoduleHorizontal;
	}
    xmlHttp.open("GET",url,true)
    xmlHttp.send(null)
}


function ajaxcalendarView(){
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		responseTxt = xmlHttp.responseText ;
		document.getElementById("calendarView_div").innerHTML = responseTxt ;
	}else{
		var live_site = document.getElementById('live_site');
		live_site = live_site.value;
		document.getElementById("calendarView_div").innerHTML = "<img src='" + live_site +"/media/com_osservicesbooking/assets/css/images/loading.gif'>";
	}
}

function ajaxsearchmodule(){
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		responseTxt = xmlHttp.responseText ;
		document.getElementById("osbsearchmodule").innerHTML = responseTxt ;
	}else{
		var live_site = document.getElementById('live_site');
		live_site = live_site.value;
		document.getElementById("osbsearchmodule").innerHTML = "<img src='" + live_site +"/media/com_osservicesbooking/assets/css/images/loading.gif'>";
	}
}

function ajaxsearchmoduleHorizontal(){
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	{ 
		responseTxt = xmlHttp.responseText ;
		//alert(responseTxt);
		var venuePos = responseTxt.indexOf('Venue');
		if(venuePos > 0)
		{
			var venuePos1 = responseTxt.indexOf('EVe');
			var venue = responseTxt.substring(venuePos + 6, venuePos1 - 1);
			document.getElementById("osbvenuesearch").innerHTML = venue;
		}
		var servicePos = responseTxt.indexOf('Service');
		if(servicePos > 0)
		{
			var servicePos1 = responseTxt.indexOf('ESe');
			var service = responseTxt.substring(servicePos + 8, servicePos1 - 1);
			document.getElementById("osbservicesearch").innerHTML = service;
		}
		var categoryPos = responseTxt.indexOf('Category');
		if(categoryPos > 0)
		{
			var categoryPos1 = responseTxt.indexOf('ECa');
			var category = responseTxt.substring(categoryPos + 9, categoryPos1 - 1);
			document.getElementById("osbcategorysearch").innerHTML = category;
		}
		var employeePos = responseTxt.indexOf('Employee');
		if(employeePos > 0)
		{
			var employeePos1 = responseTxt.indexOf('EEm');
			var employee = responseTxt.substring(employeePos + 9, employeePos1 - 1);
			document.getElementById("osbemployeesearch").innerHTML = employee;
		}
	}
}

function populateUserDataAjax(id,orderid,live_site){
	xmlHttp=GetXmlHttpObject();
    if (xmlHttp==null){
        alert ("Browser does not support HTTP Request")
        return
    }

    url = live_site + 'index.php?option=com_osservicesbooking&task=ajax_getprofiledata&user_id=' + id + '&order_id=' +orderid;
    xmlHttp.onreadystatechange=ajax4w;
    xmlHttp.open("GET",url,true)
    xmlHttp.send(null)
}

function populateEmployeeDataAjax(id,eid,live_site){
	xmlHttp=GetXmlHttpObject();
    if (xmlHttp==null){
        alert ("Browser does not support HTTP Request")
        return
    }
    url = live_site + 'index.php?option=com_osservicesbooking&task=ajax_getprofileemployee&user_id=' + id + '&eid=' + eid;
    xmlHttp.onreadystatechange=ajax4w;
    xmlHttp.open("GET",url,true)
    xmlHttp.send(null)
}

function ajax4w() {
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){
		json = JSON.parse(xmlHttp.responseText);
		var selecteds = [];
		for (var field in json)
		{
			value = json[field];
			if (jQuery("input[name='" + field + "[]']").length)
			{
				//This is a checkbox or multiple select
				if (jQuery.isArray(value))
				{
					selecteds = value;
				}
				else
				{
					selecteds.push(value);
				}
				jQuery("input[name='" + field + "[]']").val(selecteds);
			}
			else if (jQuery("input[type='radio'][name='" + field + "']").length)
			{
				jQuery("input[name="+field+"][value=" + value + "]").attr('checked', 'checked');
			}
			else
			{
				jQuery('#' + field).val(value);
			}
		}
	}
}

function osbnext(live_site,category_id,employee_id,vid,sid,date_from,date_to) {
    var month = document.getElementById('month');
    var year = document.getElementById('year');
    month = month.value;
    year = year.value;
    if (month < 12) {
        month = parseInt(month) + 1;
    } else {
        year = parseInt(year) + 1;
        month = 1;
    }
    document.getElementById('month').value = month;
    document.getElementById('year').value = year;
    var ossm = document.getElementById('ossm');
    if (ossm != null) {
        document.getElementById('ossm').value = month;
    }
    var ossy = document.getElementById('ossy');
    if (ossy != null) {
        document.getElementById('ossy').value = year;
    }

    var ossmh = document.getElementById('ossmh');
    if (ossmh != null) {
        document.getElementById('ossmh').value = month;
    }
    var ossyh = document.getElementById('ossyh');
    if (ossyh != null) {
        document.getElementById('ossyh').value = year;
    }

    xmlHttp=GetXmlHttpObject();
    if (xmlHttp==null){
        alert ("Browser does not support HTTP Request")
        return
    }

    url = live_site + "index.php?option=com_osservicesbooking&no_html=1&tmpl=component&task=ajax_loadCalendatDetails&month=" + month + "&year=" + year + "&category_id=" + category_id + "&employee_id=" + employee_id + "&vid=" + vid + "&sid=" + sid + "&date_from=" + date_from + "&date_to=" + date_to;
    xmlHttp.onreadystatechange=ajax4k;
    xmlHttp.open("GET",url,true)
    xmlHttp.send(null)
}

function osbprev(live_site,category_id,employee_id,vid,sid,date_from,date_to){
	

    var month = document.getElementById('month');
    var year = document.getElementById('year');
    month 	  = month.value;
    year	  = year.value;
    var div   = document.getElementById("cal" + month + year);
    div.style.display = "none";
    if(month > 1){
        month = parseInt(month) - 1;
    }else{
        year  = parseInt(year) - 1;
        month = 12;
    }
    document.getElementById('month').value = month;
    document.getElementById('year').value = year;

    var ossm = document.getElementById('ossm');
    if (ossm != null) {
        document.getElementById('ossm').value = month;
    }
    var ossy = document.getElementById('ossy');
    if (ossy != null) {
        document.getElementById('ossy').value = year;
    }

    var ossmh = document.getElementById('ossmh');
    if (ossmh != null) {
        document.getElementById('ossmh').value = month;
    }
    var ossyh = document.getElementById('ossyh');
    if (ossyh != null) {
        document.getElementById('ossyh').value = year;
    }

    xmlHttp=GetXmlHttpObject();
    if (xmlHttp==null){
        alert ("Browser does not support HTTP Request")
        return
    }

    url = live_site + "index.php?option=com_osservicesbooking&no_html=1&tmpl=component&task=ajax_loadCalendatDetails&month=" + month + "&year=" + year + "&category_id=" + category_id + "&employee_id=" + employee_id + "&vid=" + vid + "&sid=" + sid +  "&date_from=" + date_from + "&date_to=" + date_to;
    //alert(url);
    xmlHttp.onreadystatechange=ajax4k;
    xmlHttp.open("GET",url,true)
    xmlHttp.send(null)
}

function nextBackend(live_site) {
    var month = document.getElementById('month');
    var year = document.getElementById('year');
    month = month.value;
    year = year.value;
    if (month < 12) {
        month = parseInt(month) + 1;
    } else {
        year = parseInt(year) + 1;
        month = 1;
    }
    document.getElementById('month').value = month;
    document.getElementById('year').value = year;
    var ossm = document.getElementById('ossm');
    if (ossm != null) {
        document.getElementById('ossm').value = month;
    }
    var ossy = document.getElementById('ossy');
    if (ossy != null) {
        document.getElementById('ossy').value = year;
    }

    var ossmh = document.getElementById('ossmh');
    if (ossmh != null) {
        document.getElementById('ossmh').value = month;
    }
    var ossyh = document.getElementById('ossyh');
    if (ossyh != null) {
        document.getElementById('ossyh').value = year;
    }

    xmlHttp=GetXmlHttpObject();
    if (xmlHttp==null){
        alert ("Browser does not support HTTP Request")
        return
    }

    url = live_site + "administrator/index.php?option=com_osservicesbooking&no_html=1&format=raw&tmpl=component&task=calendar_loadCalendatDetails&month=" + month + "&year=" + year;
    xmlHttp.onreadystatechange=ajax4k;
    xmlHttp.open("GET",url,true)
    xmlHttp.send(null)
}

function prevBackend(live_site) {
    var month = document.getElementById('month');
    var year = document.getElementById('year');
    month = month.value;
    year = year.value;
    if(month > 1){
        month = parseInt(month) - 1;
    }else{
        year  = parseInt(year) - 1;
        month = 12;
    }
    document.getElementById('month').value = month;
    document.getElementById('year').value = year;
    var ossm = document.getElementById('ossm');
    if (ossm != null) {
        document.getElementById('ossm').value = month;
    }
    var ossy = document.getElementById('ossy');
    if (ossy != null) {
        document.getElementById('ossy').value = year;
    }

    var ossmh = document.getElementById('ossmh');
    if (ossmh != null) {
        document.getElementById('ossmh').value = month;
    }
    var ossyh = document.getElementById('ossyh');
    if (ossyh != null) {
        document.getElementById('ossyh').value = year;
    }

    xmlHttp=GetXmlHttpObject();
    if (xmlHttp==null){
        alert ("Browser does not support HTTP Request")
        return
    }

    url = live_site + "administrator/index.php?option=com_osservicesbooking&no_html=1&format=raw&tmpl=component&task=calendar_loadCalendatDetails&month=" + month + "&year=" + year;
    xmlHttp.onreadystatechange=ajax4k;
    xmlHttp.open("GET",url,true)
    xmlHttp.send(null)
}

function calendarMovingSmall(live_site,category_id,employee_id,vid,sid,date_from,date_to){
    var ossmh = document.getElementById('ossmh');
    var ossyh  = document.getElementById('ossyh');
    sm     = ossmh.value;
    sy     = ossyh.value;
    var month = document.getElementById('month');
    var year  = document.getElementById('year');

    document.getElementById('month').value = sm;
    document.getElementById('year').value = sy;

    xmlHttp=GetXmlHttpObject();
    if (xmlHttp==null){
        alert ("Browser does not support HTTP Request")
        return
    }

    url = live_site + "index.php?option=com_osservicesbooking&no_html=1&tmpl=component&task=ajax_loadCalendatDetails&month=" + sm + "&year=" + sy + "&category_id=" + category_id + "&employee_id=" + employee_id + "&vid=" + vid + "&sid=" + sid + "&date_from=" + date_from + "&date_to=" + date_to;
    //alert(url);
    xmlHttp.onreadystatechange=ajax4k;
    xmlHttp.open("GET",url,true)
    xmlHttp.send(null)
}

function changeTimeSlotDate(tstatus,date,sid,tid,live_site){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	var live_site = document.getElementById('live_site');
	live_site = live_site.value;
	document.getElementById("selected_item").value = "date" + tid + date;
	url = live_site + "index.php?option=com_osservicesbooking&no_html=1&tmpl=component&task=ajax_changeTimeSlotDate&tstatus=" + tstatus + "&sid=" + sid + "&tid=" + tid + "&date=" + date;
	xmlHttp.onreadystatechange=ajax4l;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}
function checkCouponCodeAjax(coupon_code,live_site){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	var live_site = document.getElementById('live_site');
	live_site = live_site.value;
	url = live_site + "index.php?option=com_osservicesbooking&no_html=1&format=raw&tmpl=component&task=ajax_checkcouponcode&coupon_code=" + coupon_code;
	xmlHttp.onreadystatechange=ajax4g;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function updateTempDateAjax(sid,eid,start_time,end_time,value){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	var live_site = document.getElementById('live_site');
	live_site = live_site.value;
	url = live_site + "index.php?option=com_osservicesbooking&no_html=1&tmpl=component&task=ajax_updatenslots&start_time=" + start_time + "&end_time=" + end_time + "&sid=" + sid + "&eid=" + eid  + "&newvalue=" + value;
	xmlHttp.onreadystatechange=ajax4c;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
	
}

function addtoCart2(live_site,sid,eid,start_time,vid,year,month,day){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}		 
	var date_from = document.getElementById('date_from');
	var date_to   = document.getElementById('date_to');

	var service_time_type = document.getElementById('service_time_type_' + sid);
	service_time_type = service_time_type.value;
	var cansubmit =  1;
	var suff = "";
	if(service_time_type == "1")
	{
		var nslots = document.getElementById('nslots_' + sid + '_' + eid + '_' + day + '_' + month + '_' + year);
		if(nslots != null)
		{
			nslots = nslots.value;
			if(nslots == "")
			{
				alert("Invalid number");
				document.getElementById('nslots_' + sid + '_' + eid + '_' + day + '_' + month + '_' + year).focus();
				cansubmit = 0;
			}
			else if(isNaN (nslots))
			{
				alert("Invalid number");
				document.getElementById('nslots_' + sid + '_' + eid + '_' + day + '_' + month + '_' + year).focus();
				cansubmit = 0;
			}
			else
			{
				suff = "&nslots=" + nslots;
			}
		}
	}

	if(cansubmit ==  1)
	{
		var itemid			= document.getElementById('Itemid');
		itemid				= itemid.value;
		url					= live_site + "index.php?option=com_osservicesbooking&tmpl=component&no_html=1&task=ajax_addtocart&update_temp_table=0&start_booking_time=" + start_time + "&sid=" + sid + "&eid=" + eid + "&employee_id=" + eid + "&vid=" + vid + "&date_from=" + date_from.value + "&date_to=" + date_to.value + suff + "&Itemid=" + itemid;
		xmlHttp.onreadystatechange=ajax1;
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null)
	}
}

function addtoCartAjaxMultiple(selectItems,sid,eid,live_site,additional_information,repeat,vid,category_id,employee_id)
{
    xmlHttp=GetXmlHttpObject();
    if (xmlHttp==null){
        alert ("Browser does not support HTTP Request")
        return
    }
    var service_time_type = document.getElementById('service_time_type_' + sid);
    service_time_type = service_time_type.value;
    var cansubmit =  1;
    var suff = "";
    if(service_time_type == "1")
    {
        var nslots = document.getElementById('nslots_' + sid + '_' + eid);
        nslots = nslots.value;
        if(nslots == "")
        {
            alert("Invalid number");
            document.getElementById('nslots_' + sid + '_' + eid).focus();
            cansubmit = 0;
        }
        else if(isNaN (nslots))
        {
            alert("Invalid number");
            document.getElementById('nslots_' + sid + '_' + eid).focus();
            cansubmit = 0;
        }
        else
        {
            suff = "&nslots=" + nslots;
        }
    }
    if(sid > 0)
    {
        document.getElementById('sid').value = sid;
        document.getElementById('eid').value = eid;
    }
    var	count_services = document.getElementById('count_services');
    if(count_services != null)
    {
        count_services = count_services.value;
    }
    else
    {
        count_services = 1;
    }
    var date_from = document.getElementById('date_from');
    var date_to   = document.getElementById('date_to');

    var selectItemsString = selectItems.join("|");

    if(cansubmit ==  1)
    {
        var itemid = document.getElementById('Itemid');
        itemid = itemid.value;
        randomnumber = Math.floor(Math.random() * 100);
        url = live_site + "index.php?rn=" + randomnumber +"&option=com_osservicesbooking&tmpl=component&no_html=1&task=ajax_addtocart&select_items=" + selectItemsString + "&sid=" + sid + "&eid=" + eid + "&additional_information=" + additional_information + suff + "&update_temp_table=1&repeat=" + repeat + "&vid=" +  vid + "&category_id=" + category_id + "&employee_id=" + employee_id + "&date_from=" + date_from.value + "&date_to=" + date_to.value + "&Itemid=" + itemid + "&count_services=" + count_services;
        xmlHttp.onreadystatechange=ajax1;
        xmlHttp.open("GET",url,true)
        xmlHttp.send(null)
    }
}

function addtoCartAjax(start_booking_time,end_booking_time,sid,eid,live_site,additional_information,repeat,vid,category_id,employee_id)
{
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}

	var service_time_type = document.getElementById('service_time_type_' + sid);
	service_time_type = service_time_type.value;
	var cansubmit =  1;
	var suff = "";
	if(service_time_type == "1")
	{
		var nslots = document.getElementById('nslots_' + sid + '_' + eid);
		nslots = nslots.value;
		if(nslots == "")
		{
			alert("Invalid number");
			document.getElementById('nslots_' + sid + '_' + eid).focus();
			cansubmit = 0;
		}
		else if(isNaN (nslots))
		{
			alert("Invalid number");
			document.getElementById('nslots_' + sid + '_' + eid).focus();
			cansubmit = 0;
		}
		else
		{
			suff = "&nslots=" + nslots;
		}
	}
	if(sid > 0)
	{
		document.getElementById('sid').value = sid;
        document.getElementById('eid').value = eid;
	}
	var	count_services = document.getElementById('count_services');
	if(count_services != null)
	{
		count_services = count_services.value;
	}
	else
	{
		count_services = 1;
	}
	var date_from = document.getElementById('date_from');
	var date_to   = document.getElementById('date_to');
	if(cansubmit ==  1)
	{
		var itemid = document.getElementById('Itemid');
		itemid = itemid.value;
		randomnumber = Math.floor(Math.random() * 100);
		url = live_site + "index.php?rn=" + randomnumber +"&option=com_osservicesbooking&tmpl=component&no_html=1&task=ajax_addtocart&start_booking_time=" + start_booking_time + "&end_booking_time=" + end_booking_time + "&sid=" + sid + "&eid=" + eid + "&additional_information=" + additional_information + suff + "&update_temp_table=1&repeat=" + repeat + "&vid=" +  vid + "&category_id=" + category_id + "&employee_id=" + employee_id + "&date_from=" + date_from.value + "&date_to=" + date_to.value + "&Itemid=" + itemid + "&count_services=" + count_services;
		//alert(url);
		xmlHttp.onreadystatechange=ajax1;
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null)
	}
}

function loadWeeklyCalendar(live_site, year, month, day)
{
	document.getElementById('m').value = month;
	document.getElementById('y').value = year;
	document.getElementById('d').value = day;

	var sidArr = [];
	for (var option of document.getElementById('sid').options)
	{
		if (option.selected) {
			sidArr.push(option.value);
		}
	}
	var selected_sid = sidArr.join();
	document.getElementById('selected_sid').value = selected_sid;

	var eidArr = [];
	for (var option of document.getElementById('eid').options)
	{
		if (option.selected) {
			eidArr.push(option.value);
		}
	}
	var selected_eid = eidArr.join();
	document.getElementById('selected_eid').value = selected_eid;

	var selected_sid = document.getElementById('selected_sid').value;
	var selected_eid = document.getElementById('selected_eid').value;

	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	
	url = live_site + "administrator/index.php?option=com_osservicesbooking&tmpl=component&no_html=1&task=calendar_loadWeekyCalendar&year=" + year + "&month=" + month + "&day=" + day + "&selected_sid=" + selected_sid + "&selected_eid=" + selected_eid;
	//alert(url);
	xmlHttp.onreadystatechange=ajaxloadWeeklyCalendar;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function ajaxloadWeeklyCalendar() {
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	{ 
		var responseText    = xmlHttp.responseText ;
		document.getElementById("maincontentdiv").innerHTML = responseText;

		jQuery(function($) {
			 $('.hasTip').each(function() {
				var title = $(this).attr('title');
				if (title) {
					var parts = title.split('::', 2);
					var mtelement = document.id(this);
					mtelement.store('tip:title', parts[0]);
					mtelement.store('tip:text', parts[1]);
				}
			});
			var JTooltips = new Tips($('.hasTip').get(), {"maxTitleChars": 50,"fixed": false});
		});
	}
	else
	{
		var live_site = document.getElementById('live_site');
		live_site = live_site.value;
		document.getElementById("maincontentdiv").innerHTML = "<img src='" + live_site +"/media/com_osservicesbooking/assets/css/images/loading.gif'>";
	}
}

function reselectItem(live_site,sid,eid,start_booking_time){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	url = live_site + "index.php?option=com_osservicesbooking&no_html=1&tmpl=component&task=ajax_reselect&date=" + start_booking_time + "&sid=" + sid + "&eid=" + eid;
	xmlHttp.onreadystatechange=ajax1;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function ajax1() { 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){
		responseTxt = xmlHttp.responseText ;
		firstitem   = responseTxt.indexOf('@@@');
		if(firstitem > 0){
			responseTxt = responseTxt.substring(firstitem + 3);
			alert("Duplicate booking time");
		}
		
		var pos = responseTxt.indexOf("1111");
		str1 = responseTxt.substring(pos + 4);
		pos  = str1.indexOf("@3333");
		str2 = str1.substring(0,pos);
		str3 = str1.substring(pos+5);
		pos  = str3.indexOf("2222");
		str4 = str3.substring(0,pos);
		var use_js_popup = document.getElementById('use_js_popup');
		if(str4 != ""){
			if(use_js_popup.value == 1){
				 var dialog = document.getElementById("dialogstr4");
				 if(dialog != null){
					 dialog.innerHTML = str4 ; 
					 jQuery(function() {
						  jQuery( "#dialogstr4" ).dialog({
						      modal: true
						  });
					 });
				 }
			}
		}
		str5 = str3.substring(pos+4);
		var using_cart = document.getElementById("using_cart");
		if(using_cart.value == 0){
			if(str2 != "#"){
				location.href = str2;
			}
		}else{
			document.getElementById("cartdiv").innerHTML = str2 ;
		}
		var selected_item = document.getElementById('selected_item');

		var pos				  = str5.indexOf('*4444*');
		if(pos > 0){
			var str51		  = str5.substring(0,pos);
			var tab_fields    = str5.substring(pos+6);
			var tab_fields_input = document.getElementById('tab_fields');
			if(tab_fields_input != null){
				jQuery('#tab_fields').val(tab_fields);
			}
		}else{
			var str51		= str5;
		}

		var div = document.getElementById("maincontentdiv");
		div.innerHTML = str51;
		
		var sid 			= document.getElementById("sid").value;
		var eid 			= document.getElementById("eid").value;
		var calendar_name 	= "repeat_to" + sid + "_" + eid;

        var extra_class = '';
        var tab_ids = jQuery('#tab_fields').val();
        if(tab_ids != "")
        {
            jQuery("#services a").click(function (e) {e.preventDefault();jQuery(this).tab("show");});
            var tab_ids_array = new Array();
            tab_ids_array = tab_ids.split("|");
            if(tab_ids_array.length > 0)
            {
                for(i=0;i<tab_ids_array.length;i++)
                {
                    var pane = tab_ids_array[i];
                    var paneArr = new Array();
                    paneArr = pane.split("-");
                    var pane_id = paneArr[0];
                    var pane_id_array = new Array();
                    pane_id_array = pane_id.split("&");
                    pane_id = pane_id_array[0];
                    if(sid > 0){
						if(sid == pane_id){
                            extra_class = 'active';
						}else{
							extra_class = '';
						}
                    }else if(i==0){
                        extra_class = 'active';
                    }else{
                        extra_class = '';
                    }
                    service_name = pane_id_array[1];
                    jQuery("#servicesTabs").append(jQuery("<li role = \"presentation\" class=\"nav-item " + extra_class + " \"><a class=\"nav-link " + extra_class +" \" href=\"#pane" + pane_id + "\" data-bs-toggle = \"tab\" role= \"tab\" aria-controls=\"#pane" + pane_id + "\" data-toggle=\"tab\">" + service_name + "<\/a><\/li>"));
                    jQuery("#employees" + pane_id + " a").click(function (e) {e.preventDefault();jQuery(this).tab("show");});
                    var pane_e = paneArr[1];
                    var pane_e_array = new Array();
                    pane_e_array = pane_e.split("*");
                    if(pane_e_array.length > 0){
                        for(j=0;j<pane_e_array.length;j++){
                            var pane_e_id = pane_e_array[j];
                            var pane_e_id_array = new Array();
                            pane_e_id_array = pane_e_id.split("%");
                            pane_e_id = pane_e_id_array[0];
                            employee_name = pane_e_id_array[1];
                            if(eid > 0){
                                if(pane_e_id == 'pane' + sid + '_' + eid){
                                    extra_class = 'active';
                                }else{
                                    extra_class = '';
                                }
                            }else if(j==0){
                                extra_class = 'active';
                            }else{
                                extra_class = '';
                            }


                            jQuery("#employees" + pane_id + "Tabs").append(jQuery("<li class=\"nav-item " + extra_class + " \"><a class=\"nav-link " + extra_class +"\" href=\"#"+pane_e_id+"\" data-bs-toggle = \"tab\" data-toggle=\"tab\">" + employee_name + "<\/a><\/li>"));
                        }
                    }
                }
            }
        }

		var repeat_to = document.getElementById('repeat_to');
		if(repeat_to != null){
			Calendar._DN = new Array ("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"); Calendar._SDN = new Array ("Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"); Calendar._FD = 0; Calendar._MN = new Array ("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"); Calendar._SMN = new Array ("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"); Calendar._TT = {};Calendar._TT["INFO"] = "About the Calendar"; Calendar._TT["ABOUT"] =
 "DHTML Date/Time Selector\n" +
 "(c) dynarch.com 2002-2005 / Author: Mihai Bazon\n" +
"For latest version visit: http://www.dynarch.com/projects/calendar/\n" +
"Distributed under GNU LGPL.  See http://gnu.org/licenses/lgpl.html for details." +
"\n\n" +
"Date selection:\n" +
"- Use the � and � buttons to select year\n" +
"- Use the < and > buttons to select month\n" +
"- Hold mouse button on any of the above buttons for faster selection.";
Calendar._TT["ABOUT_TIME"] = "\n\n" +
"Time selection:\n" +
"- Click on any of the time parts to increase it\n" +
"- or Shift-click to decrease it\n" +
"- or click and drag for faster selection.";

			Calendar._TT["PREV_YEAR"] = "Click to move to the previous year. Click and hold for a list of years."; Calendar._TT["PREV_MONTH"] = "Click to move to the previous month. Click and hold for a list of the months."; Calendar._TT["GO_TODAY"] = "Go to today"; Calendar._TT["NEXT_MONTH"] = "Click to move to the next month. Click and hold for a list of the months."; Calendar._TT["NEXT_YEAR"] = "Click to move to the next year. Click and hold for a list of years."; Calendar._TT["SEL_DATE"] = "Select a date."; Calendar._TT["DRAG_TO_MOVE"] = "Drag to move"; Calendar._TT["PART_TODAY"] = "Today"; Calendar._TT["DAY_FIRST"] = "Display %s first"; Calendar._TT["WEEKEND"] = "0,6"; Calendar._TT["CLOSE"] = "Close"; Calendar._TT["TODAY"] = "Today"; Calendar._TT["TIME_PART"] = "(Shift-)Click or Drag to change the value."; Calendar._TT["DEF_DATE_FORMAT"] = "%Y-%m-%d"; Calendar._TT["TT_DATE_FORMAT"] = "%a, %b %e"; Calendar._TT["WK"] = "wk"; Calendar._TT["TIME"] = "Time:";
			
				window.addEvent('domready', function() {Calendar.setup({
					// Id of the input field
					inputField: calendar_name,
					// Format of the input field
					ifFormat: "%Y-%m-%d",
					// Trigger for the calendar (button ID)
					button: "repeat_to_img",
					// Alignment (defaults to "Bl")
					align: "Tl",
					singleClick: true,
					firstDay: 0
					});});
		}
		var venue_available = document.getElementById('venue_available');
		if(venue_available != null){
			if(venue_available.value == 1)
			{
				var j4 = document.getElementById('j4');
				if(j4.value == "0")
				{
					window.addEvent('domready', function() {
						SqueezeBox.initialize({});
						SqueezeBox.assign($$('a.osmodal'), {
							parse: 'rel'
						});
					});
				}
			}
		}
		var j4 = document.getElementById('j4');
		if(j4.value == "0")
		{
			window.addEvent('domready', function() {
				$$('.hasTip').each(function(el) {
					var title = el.get('title');
					if (title) {
						var parts = title.split('::', 2);
						el.store('tip:title', parts[0]);
						el.store('tip:text', parts[1]);
					}
				});
				var JTooltips = new Tips($$('.hasTip'), { maxTitleChars: 50, fixed: false});
			});
		}
		if(use_js_popup.value == 0)
		{
			var p = jQuery( "#msgDiv" );
			//jQuery("#msgDiv").fadeIn();
			document.getElementById('msgDiv').style.display = "block";
			document.getElementById('msgDiv').innerHTML = str4;
			var offset = p.offset();
			jQuery('html, body').animate({ scrollTop: offset.top}, 800, 'swing');
			setTimeout(function() { jQuery("#msgDiv").fadeOut(1500); }, 5000);
		}
		else
		{
			var p = jQuery( "#cartbox" );
			var offset = p.offset();
			jQuery('html, body').animate({ scrollTop: offset.top}, 800, 'swing');
		}
	} 
}

function closeDialog(){
	jQuery( "#dialogstr4" ).dialog("close");
}

function ajax1copied() { 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		responseTxt = xmlHttp.responseText ;
		firstitem   = responseTxt.indexOf('@@@');
		if(firstitem > 0){
			responseTxt = responseTxt.substring(firstitem + 3);
			alert("Duplicate booking time");
		}
		
		var pos = responseTxt.indexOf("1111");
		str1 = responseTxt.substring(pos + 4);
		pos  = str1.indexOf("@3333");
		str2 = str1.substring(0,pos);
		str3 = str1.substring(pos+5);
		pos  = str3.indexOf("2222");
		str4 = str3.substring(0,pos);
		var use_js_popup = document.getElementById('use_js_popup');
		if(str4 != ""){
			//var answer = confirm(str4);
			if(use_js_popup.value == 1){
				alert(str4);
			}
			//if(answer == 1){
				//var current_link = document.getElementById('current_link');
				//location.href = current_link + "index.php?option=com_osservicesbooking&task=form_step1";
			//}
		}
		str5 = str3.substring(pos+4);
		var using_cart = document.getElementById("using_cart");
		//if(using_cart.value == 1){
			//location.href = str2;
		//}else{
			document.getElementById("cartdiv").innerHTML = str2 ;
		//}
		var selected_item = document.getElementById('selected_item');
		//var div = document.getElementById(selected_item.value);

		var pos				= str5.indexOf('*4444*');
		if(pos > 0){
			var str51		= str5.substring(0,pos);
			var tab_fields  = str5.substring(pos+6);
			var tab_fields_input = document.getElementById('tab_fields');
			if(tab_fields_input != null){
				jQuery('#tab_fields').val(tab_fields);
			}
		}else{
			var str51		= str5;
		}

		var div = document.getElementById("maincontentdiv");
		if(div != null)
		{
			div.innerHTML = str51;
		
		
			var sid 			= document.getElementById("sid").value;
			var eid 			= document.getElementById("eid").value;
			var calendar_name 	= "repeat_to" + sid + "_" + eid;
			//window.addEvent('domready', function(){ $$('dl.tabs').each(function(tabs){ new JTabs(tabs, {}); }); });

			var extra_class = '';
			var tab_ids = jQuery('#tab_fields').val();
			if(tab_ids != ""){
				jQuery(function($){ $("#services a").click(function (e) {e.preventDefault();$(this).tab("show");});});
				var tab_ids_array = new Array();
				tab_ids_array = tab_ids.split("|");
				if(tab_ids_array.length > 0){
					for(i=0;i<tab_ids_array.length;i++){
						var pane = tab_ids_array[i];
						var paneArr = new Array();
						paneArr = pane.split("-");
						var pane_id = paneArr[0];
						var pane_id_array = new Array();
						pane_id_array = pane_id.split("&");
						pane_id = pane_id_array[0];
						if(sid > 0){
							if(sid == pane_id){
								extra_class = 'active';
							}else{
								extra_class = '';
							}
						}else if(i==0){
							extra_class = 'active';
						}else{
							extra_class = '';
						}
						service_name = pane_id_array[1];
						jQuery(function($){ $("#servicesTabs").append($("<li class=\"nav-item " + extra_class + " \"><a class=\"nav-link " + extra_class +" \" href=\"#pane" + pane_id + " \" data-toggle=\"tab\">" + service_name + "<\/a><\/li>")); });
						jQuery(function($){ $("#employees" + pane_id + " a").click(function (e) {e.preventDefault();$(this).tab("show");});});
						var pane_e = paneArr[1];
						var pane_e_array = new Array();
						pane_e_array = pane_e.split("*");
						if(pane_e_array.length > 0){
							for(j=0;j<pane_e_array.length;j++){
								var pane_e_id = pane_e_array[j];
								var pane_e_id_array = new Array();
								pane_e_id_array = pane_e_id.split("%");
								pane_e_id = pane_e_id_array[0];
								employee_name = pane_e_id_array[1];
								if((eid > 0) && (sid == pane_id)){
									if(pane_e_id == 'pane' + sid + '_' + eid){
										extra_class = 'active';
									}else{
										extra_class = '';
									}
								}else if(j==0){
									extra_class = 'active';
								}else{
									extra_class = '';
								}
								jQuery(function($){ $("#employees" + pane_id + "Tabs").append($("<li class=\"nav-item " + extra_class + " \"><a data-bs-toggle = \"tab\" class=\"nav-link " + extra_class +"\" href=\"#"+pane_e_id+"\" data-toggle=\"tab\">" + employee_name + "<\/a><\/li>")); });
							}
						}
					}
				}
			}
		

			var repeat_to = document.getElementById('repeat_to');
			if(repeat_to != null){
				Calendar._DN = new Array ("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"); Calendar._SDN = new Array ("Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"); Calendar._FD = 0; Calendar._MN = new Array ("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"); Calendar._SMN = new Array ("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"); Calendar._TT = {};Calendar._TT["INFO"] = "About the Calendar"; Calendar._TT["ABOUT"] =
	 "DHTML Date/Time Selector\n" +
	 "(c) dynarch.com 2002-2005 / Author: Mihai Bazon\n" +
	"For latest version visit: http://www.dynarch.com/projects/calendar/\n" +
	"Distributed under GNU LGPL.  See http://gnu.org/licenses/lgpl.html for details." +
	"\n\n" +
	"Date selection:\n" +
	"- Use the � and � buttons to select year\n" +
	"- Use the < and > buttons to select month\n" +
	"- Hold mouse button on any of the above buttons for faster selection.";
	Calendar._TT["ABOUT_TIME"] = "\n\n" +
	"Time selection:\n" +
	"- Click on any of the time parts to increase it\n" +
	"- or Shift-click to decrease it\n" +
	"- or click and drag for faster selection.";

				Calendar._TT["PREV_YEAR"] = "Click to move to the previous year. Click and hold for a list of years."; Calendar._TT["PREV_MONTH"] = "Click to move to the previous month. Click and hold for a list of the months."; Calendar._TT["GO_TODAY"] = "Go to today"; Calendar._TT["NEXT_MONTH"] = "Click to move to the next month. Click and hold for a list of the months."; Calendar._TT["NEXT_YEAR"] = "Click to move to the next year. Click and hold for a list of years."; Calendar._TT["SEL_DATE"] = "Select a date."; Calendar._TT["DRAG_TO_MOVE"] = "Drag to move"; Calendar._TT["PART_TODAY"] = "Today"; Calendar._TT["DAY_FIRST"] = "Display %s first"; Calendar._TT["WEEKEND"] = "0,6"; Calendar._TT["CLOSE"] = "Close"; Calendar._TT["TODAY"] = "Today"; Calendar._TT["TIME_PART"] = "(Shift-)Click or Drag to change the value."; Calendar._TT["DEF_DATE_FORMAT"] = "%Y-%m-%d"; Calendar._TT["TT_DATE_FORMAT"] = "%a, %b %e"; Calendar._TT["WK"] = "wk"; Calendar._TT["TIME"] = "Time:";
				
					window.addEvent('domready', function() {Calendar.setup({
						// Id of the input field
						inputField: calendar_name,
						// Format of the input field
						ifFormat: "%Y-%m-%d",
						// Trigger for the calendar (button ID)
						button: "repeat_to_img",
						// Alignment (defaults to "Bl")
						align: "Tl",
						singleClick: true,
						firstDay: 0
						});});
			}
			var venue_available = document.getElementById('venue_available');
			if(venue_available != null){
				if(venue_available.value == 1)
				{
					var j4 = document.getElementById('j4');
					if(j4.value == "0")
					{
						window.addEvent('domready', function() {
							SqueezeBox.initialize({});
							SqueezeBox.assign($$('a.osmodal'), {
								parse: 'rel'
							});
						});
					}
				}
			}
			var j4 = document.getElementById('j4');
			if(j4.value == "0")
			{
				window.addEvent('domready', function() {
					$$('.hasTip').each(function(el) {
						var title = el.get('title');
						if (title) {
							var parts = title.split('::', 2);
							el.store('tip:title', parts[0]);
							el.store('tip:text', parts[1]);
						}
					});
					var JTooltips = new Tips($$('.hasTip'), { maxTitleChars: 50, fixed: false});
				});
			}
		}
	} 
}

function showInforFormAjax(live_site){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	
	url = live_site + "index.php?option=com_osservicesbooking&no_html=1&task=ajax_showinfor";
	xmlHttp.onreadystatechange=ajax1b;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function ajax1b() { 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		document.getElementById("maincontentdiv").innerHTML = xmlHttp.responseText ;
	} 
}

function confirmBookingAjax(order_name,order_email,order_phone,order_country,order_city,order_state,order_zip,order_address,live_site,fields,notes,paymentMethod,x_card_num,x_card_code,card_holder_name,exp_year,exp_month,card_type){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	
	url = live_site + "index.php?option=com_osservicesbooking&tmpl=component&no_html=1&task=ajax_confirminfo&order_name=" + order_name + "&order_email=" + order_email + "&order_phone=" + order_phone + "&order_country=" + order_country + "&order_city=" + order_city + "&order_state=" + order_state + "&order_zip=" + order_zip + "&order_address=" + order_address + "&fields=" + fields + "&notes=" + notes + "&select_payment=" + paymentMethod + "&x_card_num=" + x_card_num + "&x_card_code=" + x_card_code + "&card_holder_name=" + card_holder_name + "&exp_year=" + exp_year + "&exp_month=" + exp_month  + "&card_type=" + card_type;
	xmlHttp.onreadystatechange=ajax1b;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function createBookingAjax(order_name,order_email,order_phone,order_country,order_city,order_state,order_zip,order_address,live_site){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	
	url = live_site + "index.php?option=com_osservicesbooking&no_html=1&task=ajax_createOrder&order_name=" + order_name + "&order_email=" + order_email + "&order_phone=" + order_phone + "&order_country=" + order_country + "&order_city=" + order_city + "&order_state=" + order_state + "&order_zip=" + order_zip + "&order_address=" + order_address; 
	xmlHttp.onreadystatechange=ajax1b;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function loadServicesAjax(live_site,year,month,day){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	var category_id		= document.getElementById('category_id');
	var employee_id		= document.getElementById('employee_id');
	var vid 			= document.getElementById('vid');
	var sid				= document.getElementById('sid');
	var seid			= document.getElementById('eid');
	var count_services	= document.getElementById('count_services');
	var catid = 0;
	var eid   = 0;
	if(category_id != null){
		catid = category_id.value
	}else{
		catid = "";
	}


	if(employee_id != null){
		eid = employee_id.value
	}else{
		eid = "";
	}
	
	if(sid != null){
		sid = sid.value
	}else{
		sid = "";
	}
	
	if(vid != null){
		vid = vid.value
	}else{
		vid = "";
	}

	if(count_services != null){
		count_services = count_services.value;
	}else{
		count_services = "";
	}

	if(seid.value != ""){
		seid = seid.value;
	}else{
		seid = "";
	}

	url = live_site + "index.php?option=com_osservicesbooking&tmpl=component&no_html=1&task=ajax_loadServices&year=" + year + "&month=" + month + "&day=" + day + "&category_id=" + catid + "&employee_id=" + eid + "&vid=" + vid + "&sid=" + sid + "&count_services=" + count_services + "&eid=" + seid;
	xmlHttp.onreadystatechange=ajax1e;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function ajax1e() { 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		//document.getElementById("cartbox").style.display = "block";
		//document.getElementById("cartbox1").style.display = "none";
		//document.getElementById("servicebox").style.display = "none";
		var responseText    = xmlHttp.responseText ;
		var pos				= responseText.indexOf('*4444*');
		if(pos > 0){
			var responseText1 = responseText.substring(0,pos);
			var tab_fields    = responseText.substring(pos+6);
			var tab_fields_input = document.getElementById('tab_fields');
			if(tab_fields_input != null){
				jQuery('#tab_fields').val(tab_fields);
			}
		}else{
			var responseText1 = responseText;
		}	
		document.getElementById("maincontentdiv").innerHTML = responseText1;
		var sid 			= document.getElementById("sid").value;
		var eid 			= document.getElementById("eid").value;
		var calendar_name 	= "repeat_to" + sid + "_" + eid;
		window.addEvent('domready', function(){ $$('dl.tabs').each(function(tabs){ new JTabs(tabs, {}); }); });

		var extra_class = '';
		var tab_ids = jQuery('#tab_fields').val();
		if(tab_ids != ""){
			jQuery("#services a").click(function (e) {e.preventDefault();jQuery(this).tab("show");});
			var tab_ids_array = new Array();
			tab_ids_array = tab_ids.split("|");
			if(tab_ids_array.length > 0){
				for(i=0;i<tab_ids_array.length;i++){
					/*
					if(i==0){
						extra_class = 'active';
					}else{
						extra_class = '';
					}
					*/
					var pane = tab_ids_array[i];
					var paneArr = new Array();
					paneArr = pane.split("-");
					var pane_id = paneArr[0];
					var pane_id_array = new Array();
					pane_id_array = pane_id.split("&");
					pane_id = pane_id_array[0];
					if(sid > 0)
					{
						if(sid == pane_id)
						{
                            extra_class = 'active';
						}
						else
						{
							extra_class = '';
						}
                    }
					else if(i==0)
					{
                        extra_class = 'active';
                    }
					else
					{
                        extra_class = '';
                    }

					service_name = pane_id_array[1];
					jQuery("#servicesTabs").append(jQuery("<li role = \"presentation\" class=\"nav-item " + extra_class + " \"><a class=\"nav-link " + extra_class +" \" href=\"#pane" + pane_id + "\" data-bs-toggle = \"tab\" role= \"tab\" aria-controls=\"#pane" + pane_id + "\" data-toggle=\"tab\">" + service_name + "<\/a><\/li>"));
					jQuery("#employees" + pane_id + " a").click(function (e) {e.preventDefault();jQuery(this).tab("show");});
					var pane_e = paneArr[1];
					var pane_e_array = new Array();
					pane_e_array = pane_e.split("*");
					if(pane_e_array.length > 0)
					{
						for(j=0;j<pane_e_array.length;j++)
						{
							var pane_e_id = pane_e_array[j];
							var pane_e_id_array = new Array();
							pane_e_id_array = pane_e_id.split("%");
							pane_e_id = pane_e_id_array[0];
							employee_name = pane_e_id_array[1];
							if((eid > 0) && (sid == pane_id))
							{
                                if(pane_e_id == 'pane' + sid + '_' + eid)
								{
                                    extra_class = 'active';
                                }
								else
								{
                                    extra_class = '';
                                }
                            }
							else if(j==0)
							{
                                extra_class = 'active';
                            }
							else
							{
                                extra_class = '';
                            }
							jQuery("#employees" + pane_id + "Tabs").append(jQuery("<li class=\"nav-item " + extra_class + " \"><a class=\"nav-link " + extra_class +"\" href=\"#"+pane_e_id+"\" data-toggle=\"tab\" data-bs-toggle = \"tab\">" + employee_name + "<\/a><\/li>"));
						}
					}
				}
			}
		}

		remembertabs();

		var repeat_to = document.getElementById('repeat_to');
		if(repeat_to != null){
			Calendar._DN = new Array ("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"); Calendar._SDN = new Array ("Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"); Calendar._FD = 0; Calendar._MN = new Array ("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"); Calendar._SMN = new Array ("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"); Calendar._TT = {};Calendar._TT["INFO"] = "About the Calendar"; Calendar._TT["ABOUT"] =
 "DHTML Date/Time Selector\n" +
 "(c) dynarch.com 2002-2005 / Author: Mihai Bazon\n" +
"For latest version visit: http://www.dynarch.com/projects/calendar/\n" +
"Distributed under GNU LGPL.  See http://gnu.org/licenses/lgpl.html for details." +
"\n\n" +
"Date selection:\n" +
"- Use the � and � buttons to select year\n" +
"- Use the < and > buttons to select month\n" +
"- Hold mouse button on any of the above buttons for faster selection.";
Calendar._TT["ABOUT_TIME"] = "\n\n" +
"Time selection:\n" +
"- Click on any of the time parts to increase it\n" +
"- or Shift-click to decrease it\n" +
"- or click and drag for faster selection.";

			Calendar._TT["PREV_YEAR"] = "Click to move to the previous year. Click and hold for a list of years."; Calendar._TT["PREV_MONTH"] = "Click to move to the previous month. Click and hold for a list of the months."; Calendar._TT["GO_TODAY"] = "Go to today"; Calendar._TT["NEXT_MONTH"] = "Click to move to the next month. Click and hold for a list of the months."; Calendar._TT["NEXT_YEAR"] = "Click to move to the next year. Click and hold for a list of years."; Calendar._TT["SEL_DATE"] = "Select a date."; Calendar._TT["DRAG_TO_MOVE"] = "Drag to move"; Calendar._TT["PART_TODAY"] = "Today"; Calendar._TT["DAY_FIRST"] = "Display %s first"; Calendar._TT["WEEKEND"] = "0,6"; Calendar._TT["CLOSE"] = "Close"; Calendar._TT["TODAY"] = "Today"; Calendar._TT["TIME_PART"] = "(Shift-)Click or Drag to change the value."; Calendar._TT["DEF_DATE_FORMAT"] = "%Y-%m-%d"; Calendar._TT["TT_DATE_FORMAT"] = "%a, %b %e"; Calendar._TT["WK"] = "wk"; Calendar._TT["TIME"] = "Time:";

				window.addEvent('domready', function() {Calendar.setup({
					// Id of the input field
					inputField: calendar_name,
					// Format of the input field
					ifFormat: "%Y-%m-%d",
					// Trigger for the calendar (button ID)
					button: "repeat_to_img",
					// Alignment (defaults to "Bl")
					align: "Tl",
					singleClick: true,
					firstDay: 0
					});});
		}
		var venue_available = document.getElementById('venue_available');
		if(venue_available != null){
			if(venue_available.value == 1){
				var j4 = document.getElementById('j4');
				if(j4.value == "0")
				{
					window.addEvent('domready', function() {
						SqueezeBox.initialize({});
						SqueezeBox.assign($$('a.osmodal'), {
							parse: 'rel'
						});
					});
				}
			}
		}
		var j4 = document.getElementById('j4');
		if(j4.value == "0")
		{
			window.addEvent('domready', function() {
				$$('.hasTip').each(function(el) {
					var title = el.get('title');
					if (title) {
						var parts = title.split('::', 2);
						el.store('tip:title', parts[0]);
						el.store('tip:text', parts[1]);
					}
				});
				var JTooltips = new Tips($$('.hasTip'), { maxTitleChars: 50, fixed: false});
			});
		}
		
	}else{
		var live_site = document.getElementById('live_site');
		live_site = live_site.value;
		document.getElementById("maincontentdiv").innerHTML = "<img src='" + live_site +"/media/com_osservicesbooking/assets/css/images/loading.gif'>";
	}
}


function selectEmployeeAjax(live_site,year,month,day,sid){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	
	url = live_site + "index.php?option=com_osservicesbooking&no_html=1&task=ajax_selectEmployee&year=" + year + "&month=" + month + "&day=" + day + "&sid=" + sid; 
	xmlHttp.onreadystatechange=ajax1d;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function ajax1d(){
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		responseTxt = xmlHttp.responseText ;
		var pos = responseTxt.indexOf("@@@@");
		str1 = responseTxt.substring(0,pos);
		str2 = responseTxt.substring(pos + 4);
		document.getElementById("maincontentdiv").innerHTML = str1 ;
		document.getElementById("cartbox").style.display = "none";
		document.getElementById("cartbox1").style.display = "block";
		document.getElementById("servicebox").style.display = "block";
		document.getElementById("servicebox").innerHTML = str2 ;
	}else{
		var live_site = document.getElementById('live_site');
		live_site = live_site.value;
		document.getElementById("maincontentdiv").innerHTML = "<img src='" + live_site +"/media/com_osservicesbooking/assets/css/images/loading.gif'>";
	}
}

function removeItemAjax(itemid,live_site,sid,start_time,end_time,eid,category_id,employee_id,vid,count_services){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	var select_day		= jQuery('#select_day').val();
	var select_month	= jQuery('#select_month').val();
	var select_year		= jQuery('#select_year').val(); 

	var date_from		= jQuery('#date_from').val(); 
	var date_to			= jQuery('#date_to').val(); 
	url					= live_site + "index.php?option=com_osservicesbooking&no_html=1&task=ajax_removeItem&sid=" + sid + "&start_time=" + start_time + "&end_time=" + end_time + "&eid=" + eid + "&itemid=" + itemid + "&category_id=" + category_id + "&employee_id=" + employee_id + "&vid=" + vid + "&select_day=" + select_day + "&select_month=" + select_month + "&select_year=" + select_year + "&date_from=" + date_from + "&date_to=" + date_to + "&count_services=" + count_services;
	xmlHttp.onreadystatechange=ajax1copied;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function removeAllItemAjax(live_site,sid,category_id,employee_id,vid,count_services){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	var select_day		= jQuery('#select_day').val();
	var select_month	= jQuery('#select_month').val();
	var select_year		= jQuery('#select_year').val(); 

	var date_from		= jQuery('#date_from').val(); 
	var date_to			= jQuery('#date_to').val(); 
	url					= live_site + "index.php?option=com_osservicesbooking&no_html=1&task=ajax_removeAllItem&sid=" + sid  + "&category_id=" + category_id + "&employee_id=" + employee_id + "&vid=" + vid + "&select_day=" + select_day + "&select_month=" + select_month + "&select_year=" + select_year + "&date_from=" + date_from + "&date_to=" + date_to + "&count_services=" + count_services;
	xmlHttp.onreadystatechange=ajax1copied;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function ajax1g(){
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		responseTxt = xmlHttp.responseText ;
		document.getElementById("cartdiv").innerHTML = responseTxt;
	} 
}

function saveNewOptionAjax(live_site,field_option,additional_price,field_id){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	
	url = live_site + "index.php?option=com_osservicesbooking&tmpl=component&no_html=1&task=fields_addOption&field_id=" + field_id + "&field_option=" + field_option + "&additional_price=" + additional_price; 
	xmlHttp.onreadystatechange=ajax3a;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function ajax3a(){
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		var field_option = document.getElementById('field_option');
		var additional_price = document.getElementById('additional_price');
		field_option.value = "";
		additional_price.value = "";
		responseTxt = xmlHttp.responseText ;
		document.getElementById("field_option_div").innerHTML = responseTxt;
	} 
}

function removeFieldOptionAjax(live_site,field_id){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	
	url = live_site + "index.php?option=com_osservicesbooking&tmpl=component&no_html=1&task=fields_removeFieldOption&field_id=" + field_id; 
	xmlHttp.onreadystatechange=ajax3a;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function saveEditOptionAjax(live_site,field_option,additional_price,ordering,field_id,optionid){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	
	url = live_site + "index.php?option=com_osservicesbooking&tmpl=component&no_html=1&task=fields_editOption&field_id=" + field_id + "&field_option=" + field_option + "&additional_price=" + additional_price + "&optionid=" + optionid + "&ordering=" + ordering; 
	xmlHttp.onreadystatechange=ajax3a;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function changeDefaultOptionAjax(live_site,field_id,optionid,new_status){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	
	url = live_site + "index.php?option=com_osservicesbooking&tmpl=component&no_html=1&task=fields_changeDefaultOptionAjax&field_id=" + field_id + "&optionid=" + optionid + "&new_status=" + new_status; 
	xmlHttp.onreadystatechange=ajax3a;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function saveCustomPrice(live_site){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	sid			= document.getElementById('id').value;
	cstart		= document.getElementById('cstart').value;
	cend		= document.getElementById('cend').value;
	camount		= document.getElementById('camount').value;
	url = live_site + "administrator/index.php?option=com_osservicesbooking&tmpl=component&no_html=1&task=service_addcustomprice&sid=" + sid + "&cend=" + cend + "&cstart=" + cstart+ "&camount=" + camount; 
	xmlHttp.onreadystatechange=ajax4a;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function removeCustomPrice(id,sid,live_site){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	url = live_site + "administrator/index.php?option=com_osservicesbooking&tmpl=component&no_html=1&task=service_removecustomprice&id=" + id + "&sid=" + sid; 
	xmlHttp.onreadystatechange=ajax4a;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}


function saveCustomBreakTime(live_site){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	sid = document.getElementById('sid').value;
	eid = document.getElementById('eid').value;
	bdate = document.getElementById('bdate').value;
	bstart = document.getElementById('bstart').value;
	bend = document.getElementById('bend').value;
	url = live_site + "administrator/index.php?option=com_osservicesbooking&tmpl=component&no_html=1&task=employee_addcustombreaktime&sid=" + sid + "&eid=" + eid + "&bdate=" + bdate+ "&bstart=" + bstart+ "&bend=" + bend; 
	xmlHttp.onreadystatechange=ajax4a;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function removeCustomBreakDate(id,live_site){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	sid = document.getElementById('sid').value;
	eid = document.getElementById('eid').value;
	url = live_site + "administrator/index.php?option=com_osservicesbooking&tmpl=component&no_html=1&task=employee_removecustombreaktime&sid=" + sid + "&eid=" + eid + "&id=" + id; 
	xmlHttp.onreadystatechange=ajax4a;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function saveCustomBreakTimeFrontend(live_site){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	sid = document.getElementById('sid').value;
	eid = document.getElementById('eid').value;
	bdate = document.getElementById('bdate').value;
	bstart = document.getElementById('bstart').value;
	bend = document.getElementById('bend').value;
	employee_area = parseInt(jQuery("#employee_area").val());
	url = live_site + "index.php?option=com_osservicesbooking&tmpl=component&no_html=1&task=calendar_addcustombreaktime&sid=" + sid + "&eid=" + eid + "&bdate=" + bdate+ "&bstart=" + bstart+ "&bend=" + bend + "&employee_area=" + employee_area; 
	xmlHttp.onreadystatechange=ajax4a;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function removeCustomBreakDateFrontend(id,live_site){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	sid = document.getElementById('sid').value;
	eid = document.getElementById('eid').value;
	employee_area = parseInt(jQuery("#employee_area").val());
	url = live_site + "index.php?option=com_osservicesbooking&tmpl=component&no_html=1&task=calendar_removecustombreaktime&sid=" + sid + "&eid=" + eid + "&id=" + id + "&employee_area=" + employee_area;  
	xmlHttp.onreadystatechange=ajax4a;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function removeBreakDateAjax(rid,live_site){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	
	url = live_site + "administrator/index.php?option=com_osservicesbooking&tmpl=component&no_html=1&task=employee_removeRestday&rid=" + rid; 
	xmlHttp.onreadystatechange=ajax4a;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function removeBreakDateAjaxFrontend(rid,live_site){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	
	url = live_site + "index.php?option=com_osservicesbooking&tmpl=component&no_html=1&task=default_removeRestday&rid=" + rid; 
	xmlHttp.onreadystatechange=ajax4a;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function removeBusyTimeAjax(rid,live_site){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	
	url = live_site + "administrator/index.php?option=com_osservicesbooking&tmpl=component&no_html=1&task=employee_removeBusytime&rid=" + rid; 
	xmlHttp.onreadystatechange=ajax4a1;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function removeBusyTimeAjaxFrontend(rid,live_site){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	
	url = live_site + "index.php?option=com_osservicesbooking&tmpl=component&no_html=1&task=default_removeBusytime&rid=" + rid; 
	xmlHttp.onreadystatechange=ajax4a1;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function removeTempTimeSlotAjax(id,live_site){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	
	url = live_site + "index.php?option=com_osservicesbooking&tmpl=component&no_html=1&task=ajax_removetemptimeslot&id=" + id; 
	xmlHttp.onreadystatechange=ajax4b;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function removerestdayAjax(day,eid,item,live_site){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	
	url = live_site + "index.php?option=com_osservicesbooking&tmpl=component&no_html=1&task=ajax_removerestdayAjax&day=" + day + "&eid=" + eid + "&item=" + item; 
	xmlHttp.onreadystatechange=ajax4d;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function addrestdayAjax(day,eid,item,live_site){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	
	url = live_site + "index.php?option=com_osservicesbooking&tmpl=component&no_html=1&task=ajax_addrestdayAjax&day=" + day + "&eid=" + eid + "&item=" + item; 
	xmlHttp.onreadystatechange=ajax4d;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function removeOrderItemAjax(order_id,id,live_site){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	url = live_site + "index.php?option=com_osservicesbooking&tmpl=component&no_html=1&task=ajax_removeOrderItemAjax&id=" + id + "&order_id=" + order_id; 
	xmlHttp.onreadystatechange=ajax4e;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function changeCheckinAjax(order_id,id,live_site){
    xmlHttp=GetXmlHttpObject();
    if (xmlHttp==null){
        alert ("Browser does not support HTTP Request")
        return
    }
    url = live_site + "index.php?option=com_osservicesbooking&tmpl=component&no_html=1&task=ajax_changeCheckinOrderItemAjax&id=" + id + "&order_id=" + order_id;
    xmlHttp.onreadystatechange=ajax4t;
    xmlHttp.open("GET",url,true)
    xmlHttp.send(null)
}

function removeOrderItemAjaxCalendar(order_id,order_item_id,i,date,live_site){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	
	url = live_site + "index.php?option=com_osservicesbooking&tmpl=component&no_html=1&task=ajax_removeOrderItemAjaxCalendar&id=" + order_item_id + "&order_id=" + order_id + "&i=" + i + "&date=" + date; 
	xmlHttp.onreadystatechange=ajax4f;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function updateOrderStatusAjax(order_id,live_site){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	var	current_order_id = document.getElementById('current_order_id');
	current_order_id.value = order_id;
	var orderstatus = document.getElementById('orderstatus' + order_id);
	orderstatus = orderstatus.value;
	url = live_site + "administrator/index.php?option=com_osservicesbooking&tmpl=component&no_html=1&task=orders_updateNewOrderStatus&order_id=" + order_id + "&new_status=" + orderstatus; 
	xmlHttp.onreadystatechange=ajax4i;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function checkingVersion(current_version){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	live_site = document.getElementById('live_site');
	live_site = live_site.value;
	url = live_site + "index.php?option=com_osservicesbooking&no_html=1&tmpl=component&task=ajax_checkingVersion&current_version=" + current_version;
	xmlHttp.onreadystatechange=ajax4i1;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function doSendTestSMS()
{
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	live_site = document.getElementById('live_site');
	live_site = live_site.value;
	url = live_site + "index.php?option=com_osservicesbooking&no_html=1&tmpl=component&task=ajax_sendTestSMS";
	xmlHttp.onreadystatechange=ajaxTestSMS;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}


function ajaxTestSMS() 
{
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	{ 
		document.getElementById("smsTestDiv").innerHTML = xmlHttp.responseText ;
	}
}


function changeCheckinStatusAjax(id,status)
{
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request")
	 	 return
	}
	live_site = document.getElementById('live_site');
	live_site = live_site.value;
	url = live_site + "index.php?option=com_osservicesbooking&no_html=1&tmpl=component&task=orders_changeCheckinstatus&id=" + id + "&status=" + status;
	xmlHttp.onreadystatechange=returnCheckinStatusAjax;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}


function returnCheckinStatusAjax() 
{
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	{ 
		var processItem = document.getElementById('processItem');
		processItem = processItem.value;
		document.getElementById("checkin" + processItem).innerHTML = xmlHttp.responseText ;
	}
}


function ajax4i1() {
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		document.getElementById("oschecking_div").innerHTML = xmlHttp.responseText ;
	}else{
		var live_site = document.getElementById('live_site');
        live_site = live_site.value;
        document.getElementById("oschecking_div").innerHTML = "<div class='icon'><a href='#'><img src='" + live_site +"/administrator/components/com_osservicesbooking/asset/images/updated_failure.png'><span>Checking..</span></a></div>";
	}
}



function ajax4i() {
	var	current_order_id = document.getElementById('current_order_id');
	current_order_id = current_order_id.value;
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		document.getElementById("div_orderstatus" + current_order_id).innerHTML = xmlHttp.responseText ;
	}else{
		var live_site = document.getElementById('live_site');
        live_site = live_site.value;
        document.getElementById("div_orderstatus" + current_order_id).innerHTML = "<img src='" + live_site +"/media/com_osservicesbooking/assets/css/images/loading.gif'>";
	}
}



function ajax4a() { 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		//var oid = document.getElementById('oid').value;
		document.getElementById("rest_div").innerHTML = xmlHttp.responseText ;
	}else{
		var live_site = document.getElementById('live_site');
        live_site = live_site.value;
        document.getElementById("rest_div").innerHTML = "<img src='" + live_site +"/media/com_osservicesbooking/assets/css/images/loading.gif'>";
	}
}

function ajax4a1() { 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		//var oid = document.getElementById('oid').value;
		document.getElementById("busy_div").innerHTML = xmlHttp.responseText ;
	}else{
		var live_site = document.getElementById('live_site');
        live_site = live_site.value;
        document.getElementById("busy_div").innerHTML = "<img src='" + live_site +"/media/com_osservicesbooking/assets/css/images/loading.gif'>";
	}
}

function ajax4b() { 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		document.getElementById("bookingerrDiv").innerHTML = xmlHttp.responseText ;
	}
}

function ajax4c() { 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		document.getElementById("divtemp").innerHTML = xmlHttp.responseText ;
	}
}

function ajax4d() { 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		var item_value = document.getElementById('current_item_value').value;
		document.getElementById("a" + item_value).innerHTML = xmlHttp.responseText ;
	}
}

function ajax4e() {
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		var oid = document.getElementById('oid').value;
		document.getElementById("order" + oid).innerHTML = xmlHttp.responseText ;
	}
}

function ajax4t(){
    if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){
        var order_item_id = document.getElementById('order_item_id').value;
        document.getElementById("order" + order_item_id).innerHTML = xmlHttp.responseText ;
    }
}


function ajax4f() {
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		var oid = document.getElementById('current_td').value;
		document.getElementById("a" + oid).innerHTML = xmlHttp.responseText ;
		window.addEvent('domready', function() {
			$$('.hasTip').each(function(el) {
				var title = el.get('title');
				if (title) {
					var parts = title.split('::', 2);
					el.store('tip:title', parts[0]);
					el.store('tip:text', parts[1]);
				}
			});
			var JTooltips = new Tips($$('.hasTip'), { maxTitleChars: 50, fixed: false});
		});
		function keepAlive() {	var myAjax = new Request({method: "get", url: "index.php"}).send();} window.addEvent("domready", function(){ keepAlive.periodical(840000); });
jQuery(document).ready(function() {
			jQuery('.hasTooltip').tooltip({});
		});
	}
}

function ajax4g() { 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		responseText = xmlHttp.responseText ;
		var pos = responseText.indexOf("@return@");
		responseText = responseText.substring(pos+8);
		pos = responseText.indexOf("||");
		var prefix = responseText.substring(0,pos);
		var xpos = prefix.indexOf("XXX");
		var discount100 = prefix.substring(xpos + 3);
		discount100 = parseInt(discount100);
		prefix = prefix.substring(0,xpos);
		var res    = responseText.substring(pos + 2);
		if((prefix != "0") && (prefix != "9999")){
			document.getElementById('coupon_id').value = prefix;
			document.getElementById('discount_100').value = discount100;
			
		}
		document.getElementById('couponcodediv').innerHTML = res;
	}
}

function ajax4l(){
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		responseText = xmlHttp.responseText ;
		var selected_item = document.getElementById('selected_item').value;
		var temp = document.getElementById(selected_item);
		temp.innerHTML = responseText;
	}
}

function ajax4k(){
    if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){
        responseText = xmlHttp.responseText ;
        var temp = document.getElementById('calendardetails');
        temp.innerHTML = responseText;
    }else{
        var live_site = document.getElementById('live_site');
        live_site = live_site.value;
        document.getElementById("calendardetails").innerHTML = "<img src='" + live_site +"/media/com_osservicesbooking/assets/css/images/loading.gif'>";
    }
}

function GetXmlHttpObject(){
	var xmlHttp = null;
	try{
		xmlHttp = new XMLHttpRequest();
	}
	catch (e)
	{
		try{
			xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e)
		{
			xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
	return xmlHttp;
}