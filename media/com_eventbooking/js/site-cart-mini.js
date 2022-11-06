(function (document, $) {
    closeCartPopup = function () {
        $.colorbox.close();
    };

    checkOut = function (checkoutUrl) {
        document.location.href = checkoutUrl;
    };

    updateCart = function (Itemid) {
        if (checkQuantity()) {

            var eventId = $("input[name='event_id[]']").map(function () {
                return $(this).val();
            }).get();

            var quantity = $("input[name='quantity[]']").map(function () {
                return $(this).val();
            }).get();

            $.ajax({
                type: 'POST',
                url: EBBaseAjaxUrl + '&task=cart.update_cart&Itemid=' + Itemid + '&redirect=0&event_id=' + eventId + '&quantity=' + quantity,
                dataType: 'html',
                beforeSend: function () {
                    $('#add_more_item').before('<span class="wait"><i class="fa fa-2x fa-refresh fa-spin"></i></span>');
                },
                success: function (html) {
                    $('#cboxLoadedContent').html(html);
                    $('.wait').remove();
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        }
    };

    removeCart = function (id, Itemid) {
        $.ajax({
            type: 'POST',
            url: EBBaseAjaxUrl + '&task=cart.remove_cart&id=' + id + '&Itemid=' + Itemid + '&redirect=0',
            dataType: 'html',
            beforeSend: function () {
                $('#add_more_item').before('<span class="wait"><i class="fa fa-2x fa-refresh fa-spin"></i></span>');
            },
            success: function (html) {
                $('#cboxLoadedContent').html(html);
                jQuery.colorbox.resize();
                $('.wait').remove();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    };

    checkQuantity = function () {
        var eventId, enteredQuantity, index;
        var eventIds = [], quantities = [];
        const quantityInputs = document.querySelectorAll('input[name="quantity[]"]');

        [].slice.call(document.querySelectorAll('input[name="event_id[]"]')).forEach(function (input) {
            eventIds.push(input.value);
        });

        [].slice.call(quantityInputs).forEach(function (input) {
            quantities.push(input.value);
        });

        for (var i = 0; i < eventIds.length; i++) {
            eventId = parseInt(eventIds[i], 10);
            enteredQuantity = quantities[i];
            index = arrEventIds.indexOf(eventId);

            if (index !== -1) {
                availableQuantity = arrQuantities[index];

                if ((availableQuantity != -1) && (enteredQuantity > availableQuantity)) {
                    alert(EB_INVALID_QUANTITY + availableQuantity);
                    quantityInputs[i].focus();

                    return false;
                }
            }
        }

        return true;
    };
})(document, Eb.jQuery);