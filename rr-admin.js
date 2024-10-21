jQuery(document).ready(function($) {
    $('.rr-perform-setup').on('click', function(e) {
        e.preventDefault();
        var button = $(this);
        var messageDiv = button.siblings('.rr-setup-message');
        var action = button.data('action');
        var nonce = button.data('nonce');
        
        button.prop('disabled', true);
        messageDiv.html('<p>Running setup...</p>');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: action,
                nonce: nonce
            },
            success: function(response) {
                messageDiv.html('<h3>Setup Results:</h3>' + response);
                button.prop('disabled', false);
            },
            error: function() {
                messageDiv.html('<p>An error occurred. Please try again.</p>');
                button.prop('disabled', false);
            }
        });
    });
});

