<?php
/*
Plugin Name: Book3r
Plugin URI: http://example.com/
Description: Buchungs- und Wohnungsverwaltung
Version: 1.0.0
Author: Jonas D.
Author URI: http://example.com/
License: GPL2
*/

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Load plugin text domain for translations
function book3r_load_textdomain() {
    load_plugin_textdomain('book3r', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'book3r_load_textdomain');

// Activation hook
register_activation_hook(__FILE__, 'activate_book3r');

function activate_book3r() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-book3r-activator.php';
    Book3r_Activator::activate();
}

// Include the main class file
require_once plugin_dir_path(__FILE__) . 'includes/class-book3r.php';

// Initialize the plugin
function run_book3r() {
    $plugin = new Book3r();
    $plugin->run();
}

run_book3r();
