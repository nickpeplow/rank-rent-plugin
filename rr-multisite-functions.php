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
    // Network setup page
    add_submenu_page(
        'settings.php',
        'Rank & Rent Network Setup',
        'Rank & Rent Setup',
        'manage_network_options',
        'rank-and-rent-network-setup',
        'rr_render_network_setup_page'
    );

    // Network settings page
    add_menu_page(
        'Rank & Rent Network Settings',
        'Rank & Rent',
        'manage_network_options',
        'rank-and-rent-network-settings',
        'rr_render_network_settings_page',
        'dashicons-admin-generic',
        30
    );
}
add_action('network_admin_menu', 'rr_add_network_admin_menu');

// Define network settings fields
function rr_network_settings_fields() {
    $json_file = plugin_dir_path(__FILE__) . 'rr-network-fields.json';
    $fields_json = file_get_contents($json_file);
    return json_decode($fields_json, true);
}

// Render network settings page
function rr_render_network_settings_page() {
    // Enqueue the shared admin script
    wp_enqueue_script('rr-admin-script', plugin_dir_url(__FILE__) . 'rr-admin.js', array('jquery'), '1.0', true);

    ?>
    <div class="wrap">
        <h1>Rank & Rent Network Settings</h1>
        <form method="post" action="edit.php?action=rr_update_network_settings">
            <?php
            wp_nonce_field('rr_network_settings_nonce');
            
            $fields = rr_network_settings_fields();
            foreach ($fields as $field_id => $field) {
                $value = get_site_option($field_id);
                ?>
                <div class="form-field">
                    <label for="<?php echo esc_attr($field_id); ?>"><?php echo esc_html($field['label']); ?></label>
                    <?php if ($field['type'] === 'textarea'): ?>
                        <textarea id="<?php echo esc_attr($field_id); ?>" name="<?php echo esc_attr($field_id); ?>"><?php echo esc_textarea($value); ?></textarea>
                    <?php else: ?>
                        <input type="<?php echo esc_attr($field['type']); ?>" id="<?php echo esc_attr($field_id); ?>" name="<?php echo esc_attr($field_id); ?>" value="<?php echo esc_attr($value); ?>">
                    <?php endif; ?>
                </div>
                <?php
            }
            submit_button('Save Network Settings');
            ?>
        </form>
    </div>
    <?php
}

// Handle network settings update
add_action('network_admin_edit_rr_update_network_settings', 'rr_handle_network_settings_update');

function rr_handle_network_settings_update() {
    check_admin_referer('rr_network_settings_nonce');

    $fields = rr_network_settings_fields();
    foreach ($fields as $field_id => $field) {
        if (isset($_POST[$field_id])) {
            update_site_option($field_id, $_POST[$field_id]);
        }
    }

    wp_redirect(add_query_arg(array('page' => 'rank-and-rent-network-settings', 'updated' => 'true'), network_admin_url('admin.php')));
    exit;
}

// Render network setup page
function rr_render_network_setup_page() {
    // Enqueue the shared admin script
    wp_enqueue_script('rr-admin-script', plugin_dir_url(__FILE__) . 'rr-admin.js', array('jquery'), '1.0', true);

    ?>
    <div class="wrap">
        <h1>Rank & Rent Network Setup</h1>
        <p>Click the button below to perform the initial setup tasks for all sites in the network:</p>
        <button id="rr-perform-network-setup" class="button button-primary rr-perform-setup" data-action="rr_perform_network_setup" data-nonce="<?php echo wp_create_nonce('rr_network_setup_nonce'); ?>">Perform Network Setup</button>
        <div class="rr-setup-message"></div>
    </div>
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

// Function to get network setting value
function rr_get_network_setting($key) {
    return get_site_option($key, '');
}
