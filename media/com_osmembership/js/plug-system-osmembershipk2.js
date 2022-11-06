(function (document) {
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.k2-category-check-all').forEach(function (categoryCheckAllCheckbox) {
            categoryCheckAllCheckbox.addEventListener('click', function () {
                var categoryId = categoryCheckAllCheckbox.value;
                var checked = categoryCheckAllCheckbox.checked;

                document.querySelectorAll('.k2-category-' + categoryId).forEach(function (k2ItemCheckbox) {
                    k2ItemCheckbox.checked = checked;
                });

                setSelectedK2ItemIds();
            });
        });

        document.querySelectorAll('.k2-item-checkbox').forEach(function (k2ItemCheckbox) {
            k2ItemCheckbox.addEventListener('click', setSelectedK2ItemIds);
        });
    });

    function setSelectedK2ItemIds() {
        var k2ItemIds = [];

        document.querySelectorAll('.k2-item-checkbox:checked').forEach(function (k2ItemCheckbox) {
            k2ItemIds.push(k2ItemCheckbox.value);
        });

        document.getElementById('k2_item_ids').value = k2ItemIds.join(',');
    }
})(document);