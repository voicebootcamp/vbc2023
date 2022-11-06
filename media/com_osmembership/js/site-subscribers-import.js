(function (document) {
    Joomla.submitbutton = function (pressbutton) {
        var form = document.adminForm;

        if (pressbutton === 'subscriber.import' && form.input_file.value === '') {
            alert(Joomla.JText._('OSM_SELECT_FILE_TO_IMPORT_SUBSCRIPTIONS'));
            form.input_file.focus();
            return;
        }

        Joomla.submitform(pressbutton);
    };
})(document);