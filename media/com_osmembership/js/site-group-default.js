(function (document, $) {
    /**
     * function build state field
     */
    buildStateFields = (function (stateFieldId, countryFieldId, defaultState) {
        var $countryField = $('#' + countryFieldId), $stateField = $('#' + stateFieldId);
        if ($stateField.length && $stateField.is('select')) {
            //Bind onchange event to the country
            if ($countryField.length) {
                $countryField.change(function () {
                    $.ajax({
                        type: 'GET',
                        url: siteUrl + 'index.php?option=com_osmembership&task=register.get_states&country_name=' + $(this).val() + '&field_name=' + stateFieldId + '&state_name=' + defaultState + langLinkForAjax,
                        success: function (data) {
                            if ($('#field_' + stateFieldId + ' .controls').length) {
                                $('#field_' + stateFieldId + ' .controls').html(data);
                            } else if ($('#field_' + stateFieldId + ' .col-md-9').length) {
                                $('#field_' + stateFieldId + ' .col-md-9').html(data);
                            } else {
                                $('#field_' + stateFieldId + ' .uk-form-controls').html(data);
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            alert(textStatus);
                        }
                    });
                });
            }
        }
    });

    /**
     * JD validate form
     */
    OSMVALIDATEFORM = (function (formId) {
        var maxErrorsPerField = Joomla.getOptions('maxErrorsPerField');
        $(formId).validationEngine('attach', {
            maxErrorsPerField: maxErrorsPerField,
            onValidationComplete: function (form, status) {
                if (status == true) {
                    form.on('submit', function (e) {
                        e.preventDefault();
                    });
                    return true;
                }
                return false;
            }
        });
    });

    $(document).ready(function () {
        // This is hear for backward compatible purpose
        if ($.colorbox && typeof tingle === 'undefined') {
            $('.osm-modal').colorbox({width: '80%', height: '80%', iframe: true, scrolling: true, rel: false});
        }

        if ($('#osm_login_form').length) {
            OSMVALIDATEFORM("#osm_login_form");
        }

        buildStateFields('state', 'country', Joomla.getOptions('selectedState'));

        OSMVALIDATEFORM("#os_form");

        if (Joomla.getOptions('hidePaymentInformation') === true) {
            $('.payment_information').css('display', 'none');
        }
    });
})(document, jQuery);