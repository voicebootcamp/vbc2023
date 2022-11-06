(function (document, Joomla) {
    document.addEventListener('DOMContentLoaded', function () {
        const selects=  document.querySelectorAll('.ticket_type_quantity');
        [].slice.call(selects).forEach(function (select) {
            select.addEventListener('change', function (e) {
                if (Joomla.getOptions('onlyAllowRegisterOneTicketType'))
                {
                    for (i = 0; i < selects.length; ++i) {
                        if (selects[i] !== select)
                        {
                            selects[i].value = 0;
                        }
                    }
                }

                calculateIndividualRegistrationFee(1);
            });
        });
    });
})(document, Joomla);