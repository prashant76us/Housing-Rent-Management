<?php
/**
 * Plugin Name: Housing Rent Management
 * Plugin URI: https://yourwebsite.com/housing-rent-management
 * Description: Complete housing rent management system with agreements, owner details, monthly rent tracking, and maintenance.
 * Version: 1.0.0
 * Author: Prashant J.
 * Author URI: https://prashantj.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: housing-rent-mgmt
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.2
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('HRM_VERSION', '1.0.0');
define('HRM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('HRM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('HRM_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('HRM_TABLE_PREFIX', 'hrm_');

// Include required files
require_once HRM_PLUGIN_DIR . 'includes/class-functions.php';
require_once HRM_PLUGIN_DIR . 'includes/class-post-types.php';
require_once HRM_PLUGIN_DIR . 'includes/class-meta-boxes.php';
require_once HRM_PLUGIN_DIR . 'includes/class-shortcodes.php';
require_once HRM_PLUGIN_DIR . 'includes/class-admin-menu.php';
require_once HRM_PLUGIN_DIR . 'includes/class-ajax-handlers.php';

// Check database tables on admin init
add_action('admin_init', 'hrm_check_database_tables');

function hrm_check_database_tables() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    global $wpdb;
    
    $required_tables = [
        $wpdb->prefix . 'hrm_properties',
        $wpdb->prefix . 'hrm_property_owners',
        $wpdb->prefix . 'hrm_tenants',
        $wpdb->prefix . 'hrm_rent_agreements',
        $wpdb->prefix . 'hrm_rent_payments',
        $wpdb->prefix . 'hrm_monthly_maintenance',
        $wpdb->prefix . 'hrm_maintenance_requests'
    ];
    
    $missing_tables = [];
    
    foreach ($required_tables as $table) {
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table'") === $table;
        if (!$table_exists) {
            $missing_tables[] = $table;
        }
    }
    
    if (!empty($missing_tables)) {
        update_option('hrm_missing_tables', $missing_tables);
        add_action('admin_notices', 'hrm_missing_tables_notice');
    } else {
        delete_option('hrm_missing_tables');
    }
}

function hrm_missing_tables_notice() {
    $missing_tables = get_option('hrm_missing_tables', []);
    if (empty($missing_tables)) {
        return;
    }
    ?>
    <div class="notice notice-error">
        <p><strong>Housing Rent Management:</strong> Database tables are missing. Please run the SQL installation script from the <code>/sql</code> folder.</p>
        <p>Missing tables: <?php echo implode(', ', $missing_tables); ?></p>
        <p><a href="https://yourwebsite.com/docs/install-sql" target="_blank">View installation instructions</a></p>
    </div>
    <?php
}

// Initialize plugin
add_action('plugins_loaded', 'hrm_init');

function hrm_init() {
    // Load text domain
    load_plugin_textdomain('housing-rent-mgmt', false, dirname(plugin_basename(__FILE__)) . '/languages');
    
    // Initialize classes
    new HRM_Post_Types();
    new HRM_Meta_Boxes();
    new HRM_Shortcodes();
    
    if (is_admin()) {
        new HRM_Admin_Menu();
    }
    
    new HRM_Ajax_Handlers();
}

// Enqueue admin assets

// In housing-rent-management.php
add_action('admin_enqueue_scripts', 'hrm_enqueue_admin_assets');

function hrm_enqueue_admin_assets($hook) {
    // Only load on plugin pages
    if (strpos($hook, 'hrm_') === false && strpos($hook, 'hrm-') === false) {
        return;
    }
    
    wp_enqueue_style('hrm-admin-style', HRM_PLUGIN_URL . 'admin/css/admin-style.css', [], HRM_VERSION);
    wp_enqueue_script('hrm-admin-script', HRM_PLUGIN_URL . 'admin/js/admin-script.js', ['jquery'], HRM_VERSION, true);
    
    wp_localize_script('hrm-admin-script', 'hrm_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('hrm_ajax_nonce')
    ]);
}

// Enqueue public assets
add_action('wp_enqueue_scripts', 'hrm_enqueue_public_assets');

function hrm_enqueue_public_assets() {
    wp_enqueue_style('hrm-public-style', HRM_PLUGIN_URL . 'public/css/public-style.css', [], HRM_VERSION);
    wp_enqueue_script('hrm-public-script', HRM_PLUGIN_URL . 'public/js/public-script.js', ['jquery'], HRM_VERSION, true);
    
    wp_localize_script('hrm-public-script', 'hrm_public', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('hrm_public_nonce')
    ]);
}

// Activation hook
register_activation_hook(__FILE__, 'hrm_activate');

function hrm_activate() {
    // Set default options
    add_option('hrm_version', HRM_VERSION);
    add_option('hrm_currency', 'USD');
    add_option('hrm_date_format', 'F j, Y');
    add_option('hrm_notify_admin', 'yes');
    add_option('hrm_notify_tenant', 'yes');
    
    // Clear permalinks
    flush_rewrite_rules();
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'hrm_deactivate');

function hrm_deactivate() {
    // Clear scheduled hooks
    wp_clear_scheduled_hook('hrm_daily_reminders');
    wp_clear_scheduled_hook('hrm_monthly_reports');
    
    // Flush rewrite rules
    flush_rewrite_rules();
}