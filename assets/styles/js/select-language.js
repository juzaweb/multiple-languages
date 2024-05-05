$(function () {
    $(document).on('change', '.select-language', function () {
        window.location.href = window.location.href
            .replace(window.location.search, '') + '?'
            + 'locale=' + $(this).val();
    })
});
