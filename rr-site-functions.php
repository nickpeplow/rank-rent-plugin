<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

function rr_site_function() {
    // Multisite functionality here
}

// Add Rank & Rent settings page
function rr_add_settings_page() {
    add_menu_page(
        'Rank & Rent Settings',
        'Rank & Rent',
        'manage_options',
        'rank-and-rent-settings',
        'rr_render_settings_page',
        'dashicons-admin-generic',
        30
    );
}
add_action('admin_menu', 'rr_add_settings_page');

// Define settings fields
function rr_settings_fields() {
    return array(
        'rr_business_name' => array(
            'label' => 'Business Name',
            'type' => 'text',
        ),
        'rr_phone_number' => array(
            'label' => 'Phone Number',
            'type' => 'text',
        ),
        'rr_address' => array(
            'label' => 'Business Address',
            'type' => 'textarea',
        ),
    );
}

// Render settings page
function rr_render_settings_page() {
    ?>
    <div class="wrap">
        <h1>Rank & Rent Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('rank-and-rent-settings-group');
            do_settings_sections('rank-and-rent-settings-group');
            
            $fields = rr_settings_fields();
            foreach ($fields as $field_id => $field) {
                $value = get_option($field_id);
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
            submit_button();
            ?>
        </form>
        <hr>
        <h2>Initial Setup</h2>
        <p>Click the button below to perform the initial setup tasks:</p>
        <button id="rr-perform-setup" class="button button-primary">Perform Initial Setup</button>
        <div id="rr-setup-message"></div>
    </div>
    <script>
    jQuery(document).ready(function($) {
        $('#rr-perform-setup').on('click', function(e) {
            e.preventDefault();
            var button = $(this);
            button.prop('disabled', true);
            $('#rr-setup-message').html('Running setup...');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'rr_perform_initial_setup',
                    nonce: '<?php echo wp_create_nonce('rr_initial_setup_nonce'); ?>'
                },
                success: function(response) {
                    $('#rr-setup-message').html(response);
                    button.prop('disabled', false);
                },
                error: function() {
                    $('#rr-setup-message').html('An error occurred. Please try again.');
                    button.prop('disabled', false);
                }
            });
        });
    });
    </script>
    <?php
}

// Register settings
function rr_register_settings() {
    $fields = rr_settings_fields();
    foreach ($fields as $field_id => $field) {
        register_setting('rank-and-rent-settings-group', $field_id);
    }
}
add_action('admin_init', 'rr_register_settings');

// Function to get setting value
function rr_get_setting($key) {
    return get_option($key, '');
}

// Add any other multisite-related functions here

// Add AJAX handler for initial setup
add_action('wp_ajax_rr_perform_initial_setup', 'rr_ajax_perform_initial_setup');

function rr_ajax_perform_initial_setup() {
    // Ensure user has necessary permissions
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'rr_initial_setup_nonce')) {
        wp_die('Security check failed.');
    }

    // Perform the initial setup
    rr_perform_initial_setup();

    // Send a response
    echo 'Initial setup completed successfully.';
    wp_die();
}
