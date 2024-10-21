<?php
/*
Plugin Name: Rank & Rent
Plugin URI: https://example.com/
Description: A simple Rank & Rent WordPress plugin
Version: 1.0
Author: Your Name
Author URI: https://example.com/
License: GPL2
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include function files
require_once plugin_dir_path(__FILE__) . 'rr-multisite-functions.php';
require_once plugin_dir_path(__FILE__) . 'rr-site-functions.php';

// Plugin activation hook
register_activation_hook(__FILE__, 'rr_activate');

function rr_activate() {
    // Activation code here (if needed)
}

// Plugin deactivation hook
register_deactivation_hook(__FILE__, 'rr_deactivate');

function rr_deactivate() {
    // Deactivation code here (if needed)
}

// Add your plugin functionality here
