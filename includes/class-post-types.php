<?php
/**
 * Register custom post types
 */

class HRM_Post_Types {
    
    public function __construct() {
        add_action('init', [$this, 'register_post_types']);
    }
    
    public function register_post_types() {
        // Rent Agreement post type
        register_post_type('hrm_agreement',
            [
                'labels' => [
                    'name' => __('Rent Agreements', 'housing-rent-mgmt'),
                    'singular_name' => __('Rent Agreement', 'housing-rent-mgmt'),
                    'add_new' => __('Add New', 'housing-rent-mgmt'),
                    'add_new_item' => __('Add New Agreement', 'housing-rent-mgmt'),
                    'edit_item' => __('Edit Agreement', 'housing-rent-mgmt'),
                    'new_item' => __('New Agreement', 'housing-rent-mgmt'),
                    'view_item' => __('View Agreement', 'housing-rent-mgmt'),
                    'search_items' => __('Search Agreements', 'housing-rent-mgmt'),
                    'not_found' => __('No agreements found', 'housing-rent-mgmt'),
                    'not_found_in_trash' => __('No agreements found in trash', 'housing-rent-mgmt'),
                ],
                'public' => false,
                'show_ui' => true,
                'show_in_menu' => false,
                'supports' => ['title', 'author'],
                'capability_type' => 'post',
                'map_meta_cap' => true,
                'rewrite' => false,
                'query_var' => false
            ]
        );
        
        // Payment Receipt post type
        register_post_type('hrm_receipt',
            [
                'labels' => [
                    'name' => __('Payment Receipts', 'housing-rent-mgmt'),
                    'singular_name' => __('Payment Receipt', 'housing-rent-mgmt'),
                ],
                'public' => false,
                'show_ui' => true,
                'show_in_menu' => false,
                'supports' => ['title'],
                'capability_type' => 'post',
                'map_meta_cap' => true
            ]
        );
        
        // Maintenance Request post type
        register_post_type('hrm_maintenance',
            [
                'labels' => [
                    'name' => __('Maintenance', 'housing-rent-mgmt'),
                    'singular_name' => __('Maintenance Request', 'housing-rent-mgmt'),
                ],
                'public' => false,
                'show_ui' => true,
                'show_in_menu' => false,
                'supports' => ['title', 'editor'],
                'capability_type' => 'post',
                'map_meta_cap' => true
            ]
        );
    }
}