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
            // alert(ajaxUrl);
            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                dataType: 'json',
                data: {
                    customdata1: 'test1',
                    customdata2: 'test2',
                },
                complete: function(response) {
                    alert('ajax complete');
                    // country = response.responseJSON.default_country;
                    // state = response.responseJSON.state;
                    // console.log(state+' '+country);
                },
                success: function(data) {
                    alert('success');
                    // var element = $('#' + elementId);
                    if (data.success === true) {
                        $(element).html(data.output);
                        console.log(data.output);
                    //     $('form[data-role="tocart-form"]').catalogAddToCart();
                    // } else {
                    //     element.html('<p><strong>' + data.output + '</strong></p>'); // display error message
                    }
                    return data.success;
                },
                error: function (xhr, status, errorThrown) {
                    // alert(errorThrown)
                    console.log(errorThrown);
                    console.log(status);
                    alert('error');
                    alert(ajaxUrl);
                    // console.log('Error happens. Try again.');
                }
            });
            return false;
        });
    }
    return renderProductRange;
    // return false;
});
