(function (document, Joomla) {
    Joomla.submitbutton = function (pressbutton) {
        var form = document.adminForm;

        if (pressbutton === 'cancel') {
            Joomla.submitform(pressbutton, form);
        } else {
            //Validate the entered data before submitting
            if (form.title.value === '') {
                alert(Joomla.JText._('OSM_ENTER_CATEGORY_TITLE'));
                form.title.focus();
                return;
            }
            Joomla.submitform(pressbutton, form);
        }
    }
})(document, Joomla);