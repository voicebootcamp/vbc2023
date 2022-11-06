(function (document) {
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.sppb-category-check-all').forEach(function (categoryCheckAllCheckbox) {
            categoryCheckAllCheckbox.addEventListener('click', function () {
                var categoryId = categoryCheckAllCheckbox.value;
                var checked = categoryCheckAllCheckbox.checked;

                document.querySelectorAll('.sppb-category-' + categoryId).forEach(function (pageItemCheckbox) {
                    pageItemCheckbox.checked = checked;
                });

                setSelectedPageIds();
            });
        });

        document.querySelectorAll('.sppb-page-checkbox').forEach(function (pageItemCheckbox) {
            pageItemCheckbox.addEventListener('click', setSelectedPageIds);
        });
    });

    function setSelectedPageIds() {
        var pageIds = [];

        document.querySelectorAll('.sppb-page-checkbox:checked').forEach(function (pageItemCheckbox) {
            pageIds.push(pageItemCheckbox.value);
        });

        document.getElementById('sppb_page_ids').value = pageIds.join(',');
    }
})(document);