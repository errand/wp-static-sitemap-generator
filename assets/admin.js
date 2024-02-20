(function ($) {
    $(document).on('click', '#generate_button', function () {
        $.ajax({
            url: WPSSG.ajax_url,
            type: 'POST',
            data: {
                action: 'wpssg_ajax_generate',
                nonce: WPSSG.nonce
            },

            success(response) {
                console.log(response);
                if (response.success) {
                    //location.reload();
                } else {
                    console.log(response);
                }
            }
        });
    });
})(jQuery);
