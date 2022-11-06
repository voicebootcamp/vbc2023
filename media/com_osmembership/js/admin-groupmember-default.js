(function (document, Joomla) {
    Joomla.submitbutton = function (pressbutton) {
        var form = document.adminForm;

        if (pressbutton === 'cancel') {
            Joomla.submitform(pressbutton, form);
        } else {
            //Validate the entered data before submitting
            if (form.plan_id.value === '0') {
                alert(Joomla.JText._('OSM_PLEASE_SELECT_PLAN'));
                form.plan_id.focus();
                return;
            }

            if (form.group_admin_id.value === '0') {
                alert(Joomla.JText._('OSM_PLEASE_SELECT_GROUP'));
                form.group_admin_id.focus();
                return;
            }

            if (document.getElementById('user_id_id').value === '0') {
                // Require user to enter username and password
                if (form.username.value === '') {
                    alert(Joomla.JText._('OSM_PLEASE_ENTER_USERNAME'));
                    form.username.focus();
                    return;
                }

                if (form.password.value === '') {
                    alert(Joomla.JText._('OSM_PLEASE_ENTER_PASSWORD'));
                    form.password.focus();
                    return;
                }
            }

            Joomla.submitform(pressbutton, form);
        }
    };

    document.addEventListener('DOMContentLoaded', function () {
        buildStateFields('state', 'country', Joomla.getOptions('selectedState'));
        var planIdElement = document.getElementById('plan_id');

        planIdElement.addEventListener('change', function () {
            var planId = planIdElement.value;
            var groupAdminId = document.getElementById('current_group_admin_id').value;

            Joomla.request({
                url: 'index.php?option=com_osmembership&view=groupmember&format=raw&group_admin_id=' + groupAdminId + '&plan_id=' + planId,
                method: 'POST',
                onSuccess: function (resp) {
                    document.getElementById('group_admin_container').innerHTML = resp;
                },
                onError: function (error) {
                    alert(error.statusText);
                }
            });
        });
    });
})(document, Joomla);