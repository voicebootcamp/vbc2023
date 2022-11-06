(function (document) {
    Joomla.submitbutton =  function(pressbutton)
	{
		var form = document.adminForm;
		if (pressbutton == 'service_cancel'){
			Joomla.submitform( pressbutton );
			return;
		}else if (form.service_name.value == ''){
			alert(Joomla.JText._('OS_PLEASE_ENTER_SERVICE_TITLE'));
			form.service_name.focus();
            return;
		}else if (form.service_price.value == ''){
			alert(Joomla.JText._('OS_PLEASE_ENTER_SERVICE_PRICE'));
			form.service_price.focus();
            return;
		}else if (isNaN(form.service_price.value)){
			alert(Joomla.JText._('OS_PLEASE_ENTER_VALID_SERVICE_PRICE'));
			form.service_price.focus();
			return;
		}else{
			Joomla.submitform( pressbutton );
			return;
		}
	}
})(document);