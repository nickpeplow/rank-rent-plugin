# Rank & Rent WordPress Plugin

## Description

This WordPress plugin provides functionality for managing Rank & Rent websites, including multisite support and customizable site settings.

## Features

- Multisite support
- Customizable site settings
- Easy-to-use settings page in the WordPress admin area

## Installation

1. Upload the plugin files to the `/wp-content/plugins/rank-and-rent` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. For multisite installations, network activate the plugin

## Usage

### Rank & Rent Settings

The plugin adds a new settings page called "Rank & Rent" in the WordPress admin menu. Here, you can configure various settings for your Rank & Rent site.

To access the settings:

1. Go to the WordPress admin dashboard
2. Click on "Rank & Rent" in the left-hand menu
3. Configure the settings as needed
4. Click "Save Changes"

### Retrieving Settings in Your Theme or Plugin

To use the Rank & Rent settings in your theme or plugin, you can use the `rr_get_setting()` function. This function allows you to retrieve the value of any setting you've configured on the Rank & Rent settings page.

Example usage:

$business_name = rr_get_setting('rr_business_name');
$phone_number = rr_get_setting('rr_phone_number');
echo "Welcome to " . $business_name;
echo "Contact us at: " . $phone_number;