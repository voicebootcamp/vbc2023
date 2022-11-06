(function (document, Joomla) {
    Joomla.submitbutton = function (pressbutton) {
        var form = document.adminForm;

        if (pressbutton === 'export' && document.getElementById('subscriptionsExportForm')) {
            var variables = ['filter_category_id', 'filter_search', 'filter_date_field', 'filter_from_date', 'filter_to_date', 'subscription_type', 'published'];
            var variable, filterElement;

            for (i = 0; i < variables.length; i++) {
                variable = variables[i];
                filterElement = document.getElementById(variable);

                if (filterElement) {
                    document.getElementById('export_' + variable).value = filterElement.value;
                }
            }

            document.getElementById('export_filter_plan_id').value = document.getElementById('plan_id').value;

            var cids = [];

            document.querySelectorAll('input[name="cid[]"]:checked').forEach(function (checkbox) {
                cids.push(checkbox.value);
            });

            document.getElementById('export_cid').value = cids.join(',');

            Joomla.submitform(pressbutton, document.getElementById('subscriptionsExportForm'));

            return;
        }

        if (pressbutton === 'add' && Joomla.getOptions('force_select_plan')) {

            if (form.plan_id.value === '0') {
                alert(Joomla.JText._('OSM_SELECT_PLAN_TO_ADD_SUBSCRIPTION'));
                form.plan_id.focus();
                return;
            }
        }

        Joomla.submitform(pressbutton);
    };
})(document, Joomla);