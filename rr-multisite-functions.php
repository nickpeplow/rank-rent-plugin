<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

function rr_multisite_function() {
    // Multisite functionality here
}

// Add any other multisite-related functions here

// Add network admin menu
function rr_add_network_admin_menu() {
    add_submenu_page(
        'settings.php',
        'Rank & Rent Network Setup',
        'Rank & Rent Setup',
        'manage_network_options',
        'rank-and-rent-network-setup',
        'rr_render_network_setup_page'
    );
}
add_action('network_admin_menu', 'rr_add_network_admin_menu');

// Render network setup page
function rr_render_network_setup_page() {
    ?>
    <div class="wrap">
        <h1>Rank & Rent Network Setup</h1>
        <p>Click the button below to perform the initial setup tasks for all sites in the network:</p>
        <button id="rr-perform-network-setup" class="button button-primary">Perform Network Setup</button>
        <div id="rr-network-setup-message"></div>
    </div>
    <script>
    jQuery(document).ready(function($) {
        $('#rr-perform-network-setup').on('click', function(e) {
            e.preventDefault();
            var button = $(this);
            var messageDiv = $('#rr-network-setup-message');
            button.prop('disabled', true);
            messageDiv.html('<p>Running setup for all sites...</p>');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'rr_perform_network_setup',
                    nonce: '<?php echo wp_create_nonce('rr_network_setup_nonce'); ?>'
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
    </script>
    <?php
}

// Add AJAX handler for network setup
add_action('wp_ajax_rr_perform_network_setup', 'rr_ajax_perform_network_setup');

function rr_ajax_perform_network_setup() {
    // Ensure user has necessary permissions
    if (!current_user_can('manage_network_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'rr_network_setup_nonce')) {
        wp_die('Security check failed.');
    }

    $sites = get_sites();
    $results = array();
    foreach ($sites as $site) {
        $site_results = array();
        $site_results[] = "Starting setup for site ID: " . $site->blog_id;
        switch_to_blog($site->blog_id);
        
        // Permalink structure
        $site_results[] = rr_set_permalink_structure();
        
        // Uploads folder
        $old_value = get_option('uploads_use_yearmonth_folders');
        rr_disable_uploads_yearmonth_folders();
        $new_value = get_option('uploads_use_yearmonth_folders');
        $site_results[] = "uploads_use_yearmonth_folders changed from '$old_value' to '$new_value'";
        
        // Comments
        $old_status = get_option('default_comment_status');
        rr_disable_comments();
        $new_status = get_option('default_comment_status');
        $site_results[] = "default_comment_status changed from '$old_status' to '$new_status'";
        
        $results[] = "<strong>Site ID: " . $site->blog_id . "</strong><br>" . implode('<br>', $site_results);
        restore_current_blog();
    }

    // Send a response
    echo implode('<hr>', $results);
    wp_die();
}
