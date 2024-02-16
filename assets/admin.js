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
                if (response.status === 'success') {
                    //location.reload();
                } else {
                    alert(response.message);
                }
            }
        });
    });
})(jQuery);
