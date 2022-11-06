(function (document, Joomla) {
    Joomla.submitbutton = function (pressbutton) {
        var form = document.adminForm;
        if (pressbutton === 'cancel') {
            Joomla.submitform(pressbutton, form);
        } else {

            if (form.name.value === '') {
                alert(Joomla.JText._('OSM_ENTER_CUSTOM_FIELD_NAME'));
                form.name.focus();
                return;
            }

            if (form.title.value === '') {
                alert(Joomla.JText._('OSM_ENTER_CUSTOM_FIELD_TITLE'));
                form.title.focus();
                return;
            }

            if (form.fieldtype.value === '-1') {
                alert(Joomla.JText._('OSM_CHOOSE_CUSTOM_FIELD_TYPE'));
                form.fieldtype.focus();
                return;
            }
            //Validate the entered data before submitting
            Joomla.submitform(pressbutton, form);
        }
    };

    validateRules = function () {
        var form = document.adminForm;
        var validateRules = Joomla.getOptions('validateRules');

        if (form.name.value === 'email') {
            //Hard code the validation rule for email
            form.validation_rules.value = 'validate[required,custom[email],ajax[ajaxEmailCall]]';

            return;
        }

        var rules = [], validationRules = '';
        var required = form.required.value;
        var validateType = parseInt(form.datatype_validation.value, 10);
        var selectedRule = validateRules[validateType];

        if (required === '1') {
            rules.push('required');
        }

        if (selectedRule.length > 0) {
            rules.push(selectedRule);
        }
        if (rules.length > 0) {
            validationRules = 'validate[' + rules.join(',') + ']';
        } else {
            validationRules = '';
        }

        form.validation_rules.value = validationRules;
    };

    document.addEventListener('DOMContentLoaded', function () {
        var form = document.adminForm;

        form.name.addEventListener('change', function () {
            var name = form.name.value;
            name = name.replace('osm_', '');
            while (name.indexOf('  ') >= 0)
                name = name.replace('  ', ' ');
            while (name.indexOf(' ') >= 0)
                name = name.replace(' ', '_');
            name = name.replace(/[^a-zA-Z0-9_]*/ig, '');

            form.name.value = name;
        });

        document.getElementById('required').addEventListener('click', validateRules);
        form.datatype_validation.addEventListener('change', validateRules);

        var dependOnFieldId = document.getElementById('depend_on_field_id');

        dependOnFieldId.addEventListener('change', function () {
            var siteUrl = Joomla.getOptions('siteUrl');
            var fieldId = dependOnFieldId.value;

            if (fieldId > 0) {
                Joomla.request({
                    url: siteUrl + '/index.php?option=com_osmembership&view=field&format=raw&field_id=' + fieldId,
                    method: 'POST',
                    onSuccess: function (resp) {
                        document.getElementById('options_container').innerHTML = resp;
                        document.getElementById('depend_on_options_container').style.display = '';
                    },
                    onError: function (error) {
                        alert(error.statusText);
                    }
                });
            } else {
                document.getElementById('options_container').innerHTML = '';
                document.getElementById('depend_on_options_container').style.display = 'none';
            }
        });
    });
})(document, Joomla);