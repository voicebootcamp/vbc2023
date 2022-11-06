(function (document, Joomla) {
    Joomla.submitbutton = function(pressbutton) {
        const form = document.adminForm;
        if (pressbutton === 'cancel') {
            Joomla.submitform( pressbutton );
        } else {
            if (form.event_id.value == 0) {
                alert(Joomla.JText._('EB_CHOOSE_EVENT'));
                form.event_id.focus() ;
                return ;
            }
            Joomla.submitform( pressbutton );
        }
    };
})(document, Joomla);