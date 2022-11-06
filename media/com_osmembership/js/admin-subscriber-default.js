(function (document, Joomla) {
    document.addEventListener('DOMContentLoaded', function () {
        buildStateFields('state', 'country', Joomla.getOptions('selectedState'));
        OSMMaskInputs(document.getElementById('adminForm'));
    });
})(document, Joomla);