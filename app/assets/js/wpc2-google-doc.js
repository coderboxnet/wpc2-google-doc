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
                alert('Action completed: ' + response.data);
            },
            error: function() {
                alert('Error: Something went wrong.');
            }
        });
    });
});
