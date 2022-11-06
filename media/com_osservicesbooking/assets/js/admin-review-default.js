(function (document) {
    Joomla.submitbutton = function(pressbutton)
	{
		form = document.adminForm;
		if (pressbutton == 'review_cancel'){
			Joomla.submitbutton = function(pressbutton)
			return;
		}else if (form.comment_title.value == ''){
			alert(Joomla.JText._('OS_PLEASE_ENTER_COMMENT_TITLE'));
			form.comment_title.focus();
			return;
		}else if (form.service_employee.value == ''){
			alert(Joomla.JText._('OS_PLEASE_SELECT_SERVICE_EMPLOYEE'));
			return;
		}else{
			Joomla.submitbutton = function(pressbutton)
			return;
		}
	}
})(document);