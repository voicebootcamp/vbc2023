(function (document) {
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.category-check-all').forEach(function (categoryCheckAllCheckbox) {
            categoryCheckAllCheckbox.addEventListener('click', function () {
                var categoryId = categoryCheckAllCheckbox.value;
                var checked = categoryCheckAllCheckbox.checked;

                document.querySelectorAll('.category-' + categoryId).forEach(function (articleCheckbox) {
                    articleCheckbox.checked = checked;
                });

                setSelectedArticleIds();
            });
        });

        document.querySelectorAll('.article-checkbox').forEach(function (articleCheckbox) {
            articleCheckbox.addEventListener('click', setSelectedArticleIds);
        });
    });

    function setSelectedArticleIds() {
        var articleIds = [];

        document.querySelectorAll('.article-checkbox:checked').forEach(function (articleCheckbox) {
            articleIds.push(articleCheckbox.value);
        });

        document.getElementById('article_ids').value = articleIds.join(',');
    }
})(document);