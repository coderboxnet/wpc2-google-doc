jQuery(document).ready(function($) {
    $('.push-google-doc').on('click', function(e) {
        e.preventDefault();
        var postId = $(this).data('id');
        $.ajax({
            url: wpc2Ajax.ajaxurl,
            type: 'POST',
            data: {
                action: 'push_doc_action',
                post_id: postId
            },
            success: function(response) {
                if(response.data.status) {
                    console.log(response.data.authURL);
                    window.open(response.data.authURL);
                }
                
            },
            error: function() {
                alert('Error: Something went wrong.');
            }
        });
    });
    $('#connect_google').on('click', function(e) {
        e.preventDefault();
        $.ajax({
            url: wpc2Ajax.ajaxurl,
            type: 'POST',
            data: {
                action: 'google_connect_action'
            },
            success: function(response) {
                if(response.data.status) {
                    console.log(response.data.authURL);
                    window.open(response.data.authURL);
                }
                
            },
            error: function() {
                alert('Error: Something went wrong.');
            }
        });
    });
});
