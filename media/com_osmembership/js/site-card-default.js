(function (document, $) {
    $(document).ready(function(){
        $("#os_form").validationEngine('attach', {
            onValidationComplete: function(form, status){
                if (status === true) {
                    form.on('submit', function(e) {
                        e.preventDefault();
                    });

                    form.find('#btn-submit').prop('disabled', true);

                    if (typeof stripePublicKey !== 'undefined')
                    {
                        var paymentMethod = Joomla.getOptions('paymentMethod');

                        if (paymentMethod.indexOf('os_stripe') === 0 && $('input[name^=x_card_code]').is(':visible'))
                        {
                            Stripe.card.createToken({
                                number: $('input[name="x_card_num"]').val(),
                                cvc: $('input[name="x_card_code"]').val(),
                                exp_month: $('select[name="exp_month"]').val(),
                                exp_year: $('select[name="exp_year"]').val(),
                                name: $('input[name="card_holder_name"]').val()
                            }, stripeResponseHandler);

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