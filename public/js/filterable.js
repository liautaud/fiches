$(function () {
    var redraw = function ($el) {
        var action = $el.attr('data-filter-by');
        var $target = $($el.attr('data-target'));
        var desiredValue = parseInt($el.val());

        $target.find('tbody tr').show();

        if (desiredValue > 0) {
            $target.find('tbody tr').each(function () {
                var $row = $(this);
                var value = parseInt($row.attr('data-' + action));
                if (value != desiredValue) {
                    $row.hide();
                }
            });
        }
    };

    $('[data-filter-by]').change(function () {
        redraw($(this));
    }).change();
});