(function (document, $) {
    Joomla.submitbutton = function (pressbutton) {
        var form = document.adminForm;

        if (pressbutton === 'cancel') {
            $("#adminForm").validationEngine('detach');
            Joomla.submitform(pressbutton, form);
        } else {
            //Validate the entered data before submitting
            Joomla.submitform(pressbutton, form);
        }
    };

    showHideUserAccountFields = (function () {
        var userType = $('input:radio[name="user_type"]:checked').val();

        if (userType === '0') {
            $('.new-user').show();
            $('.existing-user').hide();
            $('#email').attr('class', 'class="validate[required,custom[email],ajax[ajaxValidateGroupMemberEmail]]"').removeAttr('readonly');
        } else if (userType === '1') {
            $('.new-user').hide();
            $('.existing-user').show();
            $('#email').removeAttr('class').attr('readonly', 'readonly');
        }
    });

    populateExistingUserData = (function () {
        var username = $('#existing_user_username').val();
        var planId = $('#plan_id').val();

        var siteUrl = Joomla.getOptions('siteUrl');
        $.ajax({
            type: 'POST',
            url: siteUrl + '/index.php?option=com_osmembership&task=groupmember.get_existing_user_data&username=' + username + '&plan_id=' + planId,
            dataType: 'json',
            success: function (json) {
                var selecteds = [];
                for (var field in json) {
                    value = json[field];

                    if ($("input[name='" + field + "[]']").length) {
                        //This is a checkbox or multiple select
                        if ($.isArray(value)) {
                            selecteds = value;
                        } else {
                            selecteds.push(value);
                        }
                        $("input[name='" + field + "[]']").val(selecteds);
                    } else if ($("input[type='radio'][name='" + field + "']").length) {
                        $("input[name=" + field + "][value=" + value + "]").attr('checked', 'checked');
                    } else {
                        $('#' + field).val(value);
                    }
                }
            }
        })
    });

    $(document).ready(function () {
        OSMVALIDATEFORM("#adminForm");
        buildStateFields('state', 'country', Joomla.getOptions('selectedState'));
        showHideUserAccountFields();
        $('input:radio[name^=user_type]').click(showHideUserAccountFields);
        $('#existing_user_username').change(populateExistingUserData);
    });

})(document, jQuery);