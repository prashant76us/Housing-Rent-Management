<?php
/**
 * Uninstall script
 * 
 * This file runs when the plugin is deleted
 */

// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Check if user wants to delete data
$keep_data = get_option('hrm_keep_data_on_uninstall', 'no');

if ($keep_data === 'no') {
    global $wpdb;
    
    // List of plugin tables
    $tables = [
        'hrm_properties',
        'hrm_property_owners',
        'hrm_property_owner_relations',
        'hrm_tenants',
        'hrm_rent_agreements',
        'hrm_rent_payments',
        'hrm_monthly_maintenance',
        'hrm_maintenance_requests',
        'hrm_documents',
        'hrm_notifications',
        'hrm_activity_logs'
    ];
    
    // Drop tables
    foreach ($tables as $table) {
        $table_name = $wpdb->prefix . $table;
        $wpdb->query("DROP TABLE IF EXISTS $table_name");
    }
    
    // Delete options
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'hrm_%'");
    
    // Delete user meta
    $wpdb->query("DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE 'hrm_%'");
    
    // Delete posts and post meta
    $wpdb->query("DELETE FROM {$wpdb->posts} WHERE post_type IN ('hrm_agreement', 'hrm_receipt', 'hrm_maintenance')");
    $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '_hrm_%'");
    
    // Clear any scheduled hooks
    wp_clear_scheduled_hook('hrm_daily_reminders');
    wp_clear_scheduled_hook('hrm_monthly_reports');
}