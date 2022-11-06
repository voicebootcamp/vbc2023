(function (document, Joomla) {
    Joomla.submitbutton = function (pressbutton) {
        var form = document.adminForm;

        if (pressbutton === 'cancel') {
            Joomla.submitform(pressbutton, form);
        } else {
            if (form.code.value === "") {
                alert(Joomla.JText._('OSM_ENTER_COUPON'));
                form.code.focus();
            } else if (form.discount.value === "") {
                alert(Joomla.JText._('EN_ENTER_DISCOUNT_AMOUNT'));
                form.discount.focus();
            } else {
                Joomla.submitform(pressbutton, form);
            }
        }
    }
})(document, Joomla);