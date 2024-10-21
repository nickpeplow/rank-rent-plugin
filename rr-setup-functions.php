<?php
/**
 * Rank & Rent Setup Functions
 *
 * This file contains functions related to the initial setup and configuration
 * of the Rank & Rent plugin.
 *
 * @package RankAndRent
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Set the permalink structure to /%postname%/.
 */
function rr_set_permalink_structure() {
    global $wp_rewrite;
    $old_structure = get_option('permalink_structure');
    
    // Set the desired permalink structure
    $new_structure = '/%postname%';
    
    // Update the permalink structure
    update_option('permalink_structure', $new_structure);
    
    // Flush the rewrite rules
    $wp_rewrite->init();
    $wp_rewrite->flush_rules(true);
    
    // Get the updated structure to confirm the change
    $updated_structure = get_option('permalink_structure');
    
    return "Permalink structure changed from '$old_structure' to '$updated_structure'";
}

/**
 * Disable organizing uploads into year/month-based folders.
 */
function rr_disable_uploads_yearmonth_folders() {
    $old_value = get_option('uploads_use_yearmonth_folders');
    update_option('uploads_use_yearmonth_folders', 0);
    $new_value = get_option('uploads_use_yearmonth_folders');
    error_log("uploads_use_yearmonth_folders changed from '$old_value' to '$new_value'");
}

/**
 * Disable WordPress comments.
 */
function rr_disable_comments() {
    // Close comments on all post types
    $post_types = get_post_types();
    foreach ($post_types as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }

    // Close comments on all existing posts
    global $wpdb;
    $wpdb->update($wpdb->posts, ['comment_status' => 'closed', 'ping_status' => 'closed'], ['post_status' => 'publish']);

    // Disable comment-related settings
    $old_status = get_option('default_comment_status');
    update_option('default_comment_status', 'closed');
    $new_status = get_option('default_comment_status');
    error_log("default_comment_status changed from '$old_status' to '$new_status'");

    $old_status = get_option('default_ping_status');
    update_option('default_ping_status', 'closed');
    $new_status = get_option('default_ping_status');
    error_log("default_ping_status changed from '$old_status' to '$new_status'");

    $old_value = get_option('comments_notify');
    update_option('comments_notify', 0);
    $new_value = get_option('comments_notify');
    error_log("comments_notify changed from '$old_value' to '$new_value'");

    $old_value = get_option('moderation_notify');
    update_option('moderation_notify', 0);
    $new_value = get_option('moderation_notify');
    error_log("moderation_notify changed from '$old_value' to '$new_value'");
}

/**
 * Perform initial setup tasks for the Rank & Rent plugin.
 */
function rr_perform_initial_setup() {
    error_log('rr_perform_initial_setup started for site: ' . get_current_blog_id());

    rr_set_permalink_structure();
    rr_disable_uploads_yearmonth_folders();
    rr_disable_comments();

    // Additional setup tasks can be added here

    error_log('rr_perform_initial_setup completed for site: ' . get_current_blog_id());
}

// Hook the setup function to an appropriate action
// add_action('admin_init', 'rr_perform_initial_setup');
