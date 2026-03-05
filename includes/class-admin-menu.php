<?php
/**
 * Admin menu and submenu pages
 */

class HRM_Admin_Menu {
    
    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menus']);
    }
    
    public function add_admin_menus() {
        // Main menu
        add_menu_page(
            __('Housing Rent', 'housing-rent-mgmt'),
            __('Housing Rent', 'housing-rent-mgmt'),
            'manage_options',
            'hrm-dashboard',
            [$this, 'render_dashboard'],
            'dashicons-building',
            30
        );
        
        // Submenus
        add_submenu_page(
            'hrm-dashboard',
            __('Dashboard', 'housing-rent-mgmt'),
            __('Dashboard', 'housing-rent-mgmt'),
            'manage_options',
            'hrm-dashboard',
            [$this, 'render_dashboard']
        );
        
        add_submenu_page(
            'hrm-dashboard',
            __('Properties', 'housing-rent-mgmt'),
            __('Properties', 'housing-rent-mgmt'),
            'manage_options',
            'hrm-properties',
            [$this, 'render_properties']
        );
        
        add_submenu_page(
            'hrm-dashboard',
            __('Owners', 'housing-rent-mgmt'),
            __('Owners', 'housing-rent-mgmt'),
            'manage_options',
            'hrm-owners',
            [$this, 'render_owners']
        );
        
        add_submenu_page(
            'hrm-dashboard',
            __('Tenants', 'housing-rent-mgmt'),
            __('Tenants', 'housing-rent-mgmt'),
            'manage_options',
            'hrm-tenants',
            [$this, 'render_tenants']
        );
        
        add_submenu_page(
            'hrm-dashboard',
            __('Rent Agreements', 'housing-rent-mgmt'),
            __('Agreements', 'housing-rent-mgmt'),
            'manage_options',
            'hrm-agreements',
            [$this, 'render_agreements']
        );
        
        add_submenu_page(
            'hrm-dashboard',
            __('Rent Payments', 'housing-rent-mgmt'),
            __('Payments', 'housing-rent-mgmt'),
            'manage_options',
            'hrm-payments',
            [$this, 'render_payments']
        );
        
        add_submenu_page(
            'hrm-dashboard',
            __('Maintenance', 'housing-rent-mgmt'),
            __('Maintenance', 'housing-rent-mgmt'),
            'manage_options',
            'hrm-maintenance',
            [$this, 'render_maintenance']
        );
        
        add_submenu_page(
            'hrm-dashboard',
            __('Reports', 'housing-rent-mgmt'),
            __('Reports', 'housing-rent-mgmt'),
            'manage_options',
            'hrm-reports',
            [$this, 'render_reports']
        );
        
        add_submenu_page(
            'hrm-dashboard',
            __('Settings', 'housing-rent-mgmt'),
            __('Settings', 'housing-rent-mgmt'),
            'manage_options',
            'hrm-settings',
            [$this, 'render_settings']
        );
    }
    
    public function render_dashboard() {
        include HRM_PLUGIN_DIR . 'admin/partials/dashboard.php';
    }
    
    public function render_properties() {
        include HRM_PLUGIN_DIR . 'admin/partials/properties.php';
    }
    
    public function render_owners() {
        include HRM_PLUGIN_DIR . 'admin/partials/owners.php';
    }
    
    public function render_tenants() {
        include HRM_PLUGIN_DIR . 'admin/partials/tenants.php';
    }
    
    public function render_agreements() {
        include HRM_PLUGIN_DIR . 'admin/partials/agreements.php';
    }
    
    public function render_payments() {
        include HRM_PLUGIN_DIR . 'admin/partials/payments.php';
    }
    
    public function render_maintenance() {
        include HRM_PLUGIN_DIR . 'admin/partials/maintenance.php';
    }
    
    public function render_reports() {
        include HRM_PLUGIN_DIR . 'admin/partials/reports.php';
    }
    
    public function render_settings() {
        include HRM_PLUGIN_DIR . 'admin/partials/settings.php';
    }
}