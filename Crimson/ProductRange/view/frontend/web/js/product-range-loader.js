define([
    "jquery",
    "loader"
], function ($) {
    "use strict";
    function renderProductRange(config) {
        var ajaxUrl = BASE_URL + config.ajaxUrl,
            element = '#'+config.elementId,
            form = '#'+config.form;
        $(form).submit(function () {
            let low_range = $(form).find('input[name="low-range"]').val(),
                high_range = $(form).find('input[name="high-range"]').val();

            if(!$.isNumeric(high_range) || !$.isNumeric(low_range) || (low_range > high_range)) {
                alert('enter valid price range');
                return false;
            }

            let sort = $('#render-sort').find(":selected").val();
            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                dataType: 'json',
                data: {
                    low_range: low_range,
                    high_range: high_range,
                    sort: sort
                },
                success: function(data) {
                    if (data.success === true) {
                        $(element).html(data.output);
                    }
                    return data.success;
                },
                error: function (xhr, status, errorThrown) {
                    console.log(errorThrown);
                }
            });
            return false;
        });
    }
    return renderProductRange;
});
