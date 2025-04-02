<?php
/**
 * Plugin Name:     SavvyWeb Fulfilment
 * Description:     Custom fulfilment integration for Savvy Web.
 * Version:         1.3.9
 * Author:          SavvyWeb Solutions
 */

defined('ABSPATH') || exit;
define('SAVVY_WEB_FULFILMENT_VERSION', '1.0.0');
define('SAVVY_WEB_FULFILMENT_FILE', __FILE__);

// Require the autoloader
require_once plugin_dir_path(__FILE__) . 'includes/Autoloader.php';

// Register autoloader
SavvyWebFulfilment\Autoloader::register();

// Instantiate the main plugin class
add_action('plugins_loaded', function () {
    new SavvyWebFulfilment\Plugin();
});
 
// Activation & Deactivation Hooks
register_activation_hook(__FILE__, ['SavvyWebFulfilment\Plugin', 'activate']);
register_deactivation_hook(__FILE__, ['SavvyWebFulfilment\Plugin', 'deactivate']);
