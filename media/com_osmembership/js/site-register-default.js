(function (document, $) {
    $(document).ready(function () {
        // This is here for backward compatible purpose
        if ($.colorbox && typeof tingle === 'undefined') {
            $('.osm-modal').colorbox({width: '80%', height: '80%', iframe: true, scrolling: true, rel: false});
        }

        if ($('#osm_login_form').length) {
            OSMVALIDATEFORM("#osm_login_form");
        }

        buildStateFields('state', 'country', Joomla.getOptions('selectedState'));

        OSMMaskInputs(document.getElementById('os_form'));

        if (Joomla.getOptions('hidePaymentInformation') === true) {
            $('.payment_information').css('display', 'none');
        }

        var vatNumberFieldName = Joomla.getOptions('vatNumberField');

        if (vatNumberFieldName) {
            var inputPrependClass = Joomla.getOptions('inputPrependClass');
            var addOnClass = Joomla.getOptions('addOnClass');
            var countryCode = Joomla.getOptions('countryCode');
            var showVatNumberField = Joomla.getOptions('showVatNumberField');
            var vatNumberField = $('#' + vatNumberFieldName);
            vatNumberField.addClass('taxable');
            var html = vatNumberField.parent().html();
            html = '<div class="' + inputPrependClass + ' inline-display"><span class="' + addOnClass + '" id="vat_country_code">' + countryCode + '</span>' + html + '<span class="invalid" id="vatnumber_validate_msg" style="display: none;"> ' + Joomla.JText._('OSM_INVALID_VATNUMBER') + '</span></div>';
            vatNumberField.parent().html(html);

            $('#' + vatNumberFieldName).change(function () {
                calculateSubscriptionFee();
            });

            if (showVatNumberField === 0) {
                $('#field_' + vatNumberFieldName).hide();
            } else {
                $('#field_' + vatNumberFieldName).show();
            }
        }

        var paymentNeeded = Joomla.getOptions('paymentNeeded');
        var hasStripePaymentMethod = Joomla.getOptions('hasStripePaymentMethod');

        if (hasStripePaymentMethod && typeof stripe !== 'undefined') {
            var style = {
                base: {
                    // Add your base input styles here. For example:
                    fontSize: '16px',
                    color: "#32325d",
                }
            };

            // Create an instance of the card Element.
            var card = elements.create('card', {style: style});

            // Add an instance of the card Element into the `card-element` <div>.
            card.mount('#stripe-card-element');
        }

        if (Joomla.getOptions('squareAppId')) {
            createSquareCardElement();
        }

        var maxErrorsPerField = Joomla.getOptions('maxErrorsPerField');

        $("#os_form").validationEngine('attach', {
            maxErrorsPerField: maxErrorsPerField,
            onValidationComplete: function (form, status) {
                if (status === true) {
                    form.on('submit', function (e) {
                        e.preventDefault();
                    });

                    form.find('#btn-submit').prop('disabled', true);

                    if (paymentNeeded) {
                        var paymentMethod;

                        if ($('input:radio[name="payment_method"]').length) {
                            paymentMethod = $('input:radio[name="payment_method"]:checked').val();
                        } else {
                            paymentMethod = $('input[name="payment_method"]').val();
                        }

                        if (paymentMethod === undefined) {
                            return true;
                        }


                        if (typeof stripePublicKey !== 'undefined' && paymentMethod.indexOf('os_stripe') === 0 && $('#tr_card_number').is(':visible')) {
                            Stripe.card.createToken({
                                number: $('input[name="x_card_num"]').val(),
                                cvc: $('input[name="x_card_code"]').val(),
                                exp_month: $('select[name="exp_month"]').val(),
                                exp_year: $('select[name="exp_year"]').val(),
                                name: $('input[name="card_holder_name"]').val()
                            }, stripeResponseHandler);

                            return false;
                        }

                        // Stripe card element
                        if (typeof stripe !== 'undefined' && paymentMethod.indexOf('os_stripe') === 0 && $('#stripe-card-form').is(":visible")) {
                            stripe.createToken(card).then(function (result) {
                                if (result.error) {
                                    // Inform the customer that there was an error.
                                    //var errorElement = document.getElementById('card-errors');
                                    //errorElement.textContent = result.error.message;
                                    alert(result.error.message);
                                    form.find('#btn-submit').removeAttr('disabled');
                                } else {
                                    // Send the token to your server.
                                    stripeTokenHandler(result.token);
                                }
                            });

                            return false;
                        }

                        if (paymentMethod.indexOf('os_squareup') === 0 && $('#tr_card_number').is(':visible')) {
                            sqPaymentForm.requestCardNonce();

                            return false;
                        }

                        if (paymentMethod.indexOf('os_squarecard') === 0 && $('#square-card-form').is(':visible')) {
                            squareCardCallBackHandle();

                            return false;
                        }
                    }

                    return true;
                }

                return false;
            }
        });
    });
})(document, jQuery);