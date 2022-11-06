(function (document, Joomla) {
    Joomla.submitbutton = function (pressbutton) {
        var form = document.adminForm;

        if (pressbutton === 'cancel') {
            Joomla.submitform(pressbutton, form);
        } else {
            if (form.title.value === "") {
                alert(Joomla.JText._('OSM_ENTER_TITLE'));
                form.title.focus();
            } else if (form.discount_amount.value === "") {
                alert(Joomla.JText._('OSM_ENTER_DISCOUNT_AMOUNT'));
                form.discount.focus();
            } else {
                Joomla.submitform(pressbutton, form);
            }
        }
    }
})(document, Joomla);