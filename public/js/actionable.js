$(function () {
    var showSuccess = function () {
        $('#message-snackbar').eq(0).MaterialSnackbar.showSnackbar({
            message: 'Les éléments ont été supprimés avec succès'
        });
    };

    $('[data-action]').click(function () {
        var action = $(this).attr('data-action');
        var $target = $($(this).attr('data-target'));

        $target.find(':checked').parents('tr').each(function () {
            var $el = $(this);
            var url = $el.attr('data-' + action + '-url');
            var method = $el.attr('data-' + action + '-method') || 'POST';

            if (url) {
                $.post(url, {
                    _method: method,
                    _token: document.CSRF_TOKEN
                }, function (data) {
                    $el.slideUp(function () {
                        $el.remove();
                    });
                });
            }
        });
    });
});