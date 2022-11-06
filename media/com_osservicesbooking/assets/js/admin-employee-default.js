(function (document) {
    Joomla.submitbutton =  function(pressbutton){
		var form = document.adminForm;
		var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		if (pressbutton == 'employee_cancel'){
			Joomla.submitform( pressbutton );
			return;
		}else if (form.employee_name.value == ''){
			alert(Joomla.JText._('OS_PLEASE_ENTER_EMPLOYEE_NAME'));
			form.employee_name.focus();
			return;
		}else if (form.employee_email.value != '' && !filter.test(form.employee_email.value)){
			alert(Joomla.JText._('OS_PLEASE_ENTER_VALID_EMAIL_ADDRESS'));
			form.employee_name.focus();
			return;
		}else{
			Joomla.submitform( pressbutton );
			return;
		}
	}
})(document);