function cancelSubscription(subscriptionId) {
    if (confirm(Joomla.JText._('OSM_CANCEL_SUBSCRIPTION_CONFIRM'))) {
        var form = document.osm_form_cancel_subscription;
        form.subscription_id.value = subscriptionId;
        form.submit();
    }
}

(function (document, $) {
    $(document).ready(function () {
        OSMVALIDATEFORM("#osm_form");
        buildStateFields('state', 'country', Joomla.getOptions('selectedState'));
        OSMMaskInputs(document.getElementById('osm_form'));
    });
})(document, jQuery);