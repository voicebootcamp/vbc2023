/**
	Check upload photo
	Avoid Vulnerable
	@element_id: Id of the file type tag
**/
var xmlHttp;

function checkUploadPhotoFiles(element_id)
{
	var element = document.getElementById(element_id);
    if (!element.files[0].name.match(/.(jpg|jpeg|png|gif)$/i))
	{
    	alert('Alow file: *.jpg, *.gif and *.png');
        element.value='';
    }
}

function updateWorkingStatus(sid, eid, date, status)
{
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		alert ("Browser does not support HTTP Request")
		return
	}
	var live_site = document.getElementById('live_site');
	var url = live_site.value + "administrator/index.php?option=com_osservicesbooking&task=service_updateWorkingStatus&tmpl=component&sid=" +  sid  + "&eid=" + eid + "&date=" + date + "&status=" + status;
	xmlHttp.onreadystatechange=updateWorkingStatusView;
    xmlHttp.open("GET",url,true)
    xmlHttp.send(null)
}

function removeWorkingAjax(id, sid)
{
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		alert ("Browser does not support HTTP Request")
		return
	}
	var live_site = document.getElementById('live_site');
	var url = live_site.value + "administrator/index.php?option=com_osservicesbooking&task=service_removeWorking&tmpl=component&sid=" +  sid  + "&id=" + id;
	xmlHttp.onreadystatechange=updateWorkingStatusView;
    xmlHttp.open("GET",url,true)
    xmlHttp.send(null)
}

function saveAssignmentAjax(sid)
{
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		alert ("Browser does not support HTTP Request")
		return
	}
	var employee_id = document.getElementById('employee_id').value;
	const date_in_week = ['mo','tu','we','th','fr','sa','su'];
	var date = '';
	var varSql = '';
	var tmp;
	for(i = 0; i< date_in_week.length; i++)
	{
		date = date_in_week[i];
		tmp = document.getElementById(date).value;
		varSql += '&' + date + '=' + tmp;
	}
	var venue_id = document.getElementById('venue_id');
	var varVenue = '';
	if(venue_id != null)
	{
		venue_id = venue_id.value;
		if(venue_id != '')
		{
			varVenue = '&vid=' + venue_id;
		}
	}
	var live_site = document.getElementById('live_site');
	var url = live_site.value + "administrator/index.php?option=com_osservicesbooking&task=service_saveWorking&tmpl=component&sid=" +  sid  + "&employee_id=" + employee_id + varSql + varVenue;
	xmlHttp.onreadystatechange=updateWorkingStatusView;
    xmlHttp.open("GET",url,true)
    xmlHttp.send(null)
}

function updateWorkingStatusView()
{
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	{ 
		responseTxt = xmlHttp.responseText ;
		document.getElementById("employeeassignedDiv").innerHTML = responseTxt ;
	}
	else
	{
		var live_site = document.getElementById('live_site');
		live_site = live_site.value;
		document.getElementById("employeeassignedDiv").innerHTML = "<img src='" + live_site +"/media/com_osservicesbooking/assets/css/images/loading.gif'>";
	}
}