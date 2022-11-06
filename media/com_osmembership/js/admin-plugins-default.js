(function (document) {
    document.addEventListener('DOMContentLoaded', function(){
        document.getElementById('btn-install-plugin').addEventListener('click', function () {
            var form = document.adminForm;
            if (form.plugin_package.value === "") {
                alert(Joomla.JText._('OSM_CHOOSE_PLUGIN'));
                return;
            }
            form.task.value = 'plugin.install';
            form.submit();
        });
    });
})(document);