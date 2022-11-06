(function (document, $) {
    Joomla.submitbutton = function (pressbutton) {
        var form = document.adminForm;

        if (pressbutton === 'cancel') {
            $("#adminForm").validationEngine('detach');
            Joomla.submitform(pressbutton, form);
        } else if (pressbutton === 'cancel_subscription') {
            if (confirm(Joomla.JText._('OSM_CANCEL_SUBSCRIPTION_CONFIRM'))) {
                $("#adminForm").validationEngine('detach');
                Joomla.submitform(pressbutton, form);
            }
        } else if (pressbutton === 'refund') {
            if (confirm(Joomla.JText._('OSM_REFUND_SUBSCRIPTION_CONFIRM'))) {
                jQuery("#adminForm").validationEngine('detach');
                Joomla.submitform(pressbutton, form);
            }
        } else {
            //Validate the entered data before submitting
            Joomla.submitform(pressbutton, form);
        }
    };

    $(document).ready(function () {
        $('#adminForm').validationEngine('attach', {
            onValidationComplete: function (form, status) {
                if (status === true) {
                    form.on('submit', function (e) {
                        e.preventDefault();
                    });
                    return true;
                }
                return false;
            }
        });

        buildStateFields('state', 'country', Joomla.getOptions('selectedState'));
        OSMMaskInputs(document.getElementById('adminForm'));
    });
})(document, jQuery);