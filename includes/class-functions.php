<?php
/**
 * Helper functions class
 */

class HRM_Functions {
    
    /**
     * Get table name with prefix
     */
    public static function get_table_name($table) {
        global $wpdb;
        return $wpdb->prefix . HRM_TABLE_PREFIX . $table;
    }
    
    /**
     * Get property by ID
     */
    public static function get_property($property_id) {
        global $wpdb;
        $table = self::get_table_name('properties');
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE id = %d",
            $property_id
        ));
    }
    
    /**
     * Get all properties
     */
    public static function get_properties($args = []) {
        global $wpdb;
        $table = self::get_table_name('properties');
        
        $defaults = [
            'status' => '',
            'city' => '',
            'limit' => 0,
            'offset' => 0,
            'orderby' => 'id',
            'order' => 'DESC'
        ];
        
        $args = wp_parse_args($args, $defaults);
        
        $where = ['1=1'];
        
        if (!empty($args['status'])) {
            $where[] = $wpdb->prepare("status = %s", $args['status']);
        }
        
        if (!empty($args['city'])) {
            $where[] = $wpdb->prepare("city = %s", $args['city']);
        }
        
        $where_clause = implode(' AND ', $where);
        
        $limit_clause = '';
        if ($args['limit'] > 0) {
            $limit_clause = $wpdb->prepare(" LIMIT %d, %d", $args['offset'], $args['limit']);
        }
        
        $order_clause = " ORDER BY {$args['orderby']} {$args['order']}";
        
        $query = "SELECT * FROM $table WHERE $where_clause $order_clause $limit_clause";
        
        return $wpdb->get_results($query);
    }
    
    /**
     * Get owner by ID
     */
    public static function get_owner($owner_id) {
        global $wpdb;
        $table = self::get_table_name('property_owners');
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE id = %d",
            $owner_id
        ));
    }
    
    /**
     * Get tenant by ID
     */
    public static function get_tenant($tenant_id) {
        global $wpdb;
        $table = self::get_table_name('tenants');
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE id = %d",
            $tenant_id
        ));
    }
    
    /**
     * Get active agreements
     */
    public static function get_active_agreements() {
        global $wpdb;
        $table = self::get_table_name('rent_agreements');
        $current_date = current_time('Y-m-d');
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table WHERE status = 'active' AND start_date <= %s AND end_date >= %s",
            $current_date,
            $current_date
        ));
    }
    
    /**
     * Get monthly rent payments for property
     */
    public static function get_monthly_payments($property_id, $month, $year) {
        global $wpdb;
        $table = self::get_table_name('rent_payments');
        $start_date = "$year-$month-01";
        $end_date = date('Y-m-t', strtotime($start_date));
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table WHERE property_id = %d AND payment_date BETWEEN %s AND %s",
            $property_id,
            $start_date,
            $end_date
        ));
    }
    
    /**
     * Get maintenance requests for property
     */
    public static function get_maintenance_requests($property_id, $status = '') {
        global $wpdb;
        $table = self::get_table_name('maintenance_requests');
        
        $where = "property_id = %d";
        if (!empty($status)) {
            $where .= $wpdb->prepare(" AND status = %s", $status);
        }
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table WHERE $where ORDER BY request_date DESC",
            $property_id
        ));
    }
    
    /**
     * Calculate total rent collected for period
     */
    public static function calculate_total_rent($start_date, $end_date, $property_id = 0) {
        global $wpdb;
        $table = self::get_table_name('rent_payments');
        
        $where = "payment_date BETWEEN %s AND %s AND status = 'paid'";
        if ($property_id > 0) {
            $where .= $wpdb->prepare(" AND property_id = %d", $property_id);
        }
        
        return $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(amount) FROM $table WHERE $where",
            $start_date,
            $end_date
        ));
    }
    
    /**
     * Get overdue payments
     */
    public static function get_overdue_payments() {
        global $wpdb;
        $table = self::get_table_name('rent_payments');
        $current_date = current_time('Y-m-d');
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT rp.*, ra.monthly_rent, ra.late_fee, t.first_name, t.last_name, t.email, p.property_name 
            FROM $table rp
            JOIN " . self::get_table_name('rent_agreements') . " ra ON rp.agreement_id = ra.id
            JOIN " . self::get_table_name('tenants') . " t ON rp.tenant_id = t.id
            JOIN " . self::get_table_name('properties') . " p ON rp.property_id = p.id
            WHERE rp.status = 'pending' AND rp.due_date < %s",
            $current_date
        ));
    }
    
    /**
     * Generate agreement number
     */
    public static function generate_agreement_number() {
        $year = date('Y');
        $month = date('m');
        
        global $wpdb;
        $table = self::get_table_name('rent_agreements');
        
        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE agreement_number LIKE %s",
            "AGR-{$year}{$month}%"
        ));
        
        $sequence = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
        
        return "AGR-{$year}{$month}-{$sequence}";
    }
    
    /**
     * Send email notification
     */
    public static function send_notification($to, $subject, $message, $headers = []) {
        $headers = wp_parse_args($headers, [
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>'
        ]);
        
        return wp_mail($to, $subject, $message, $headers);
    }
    
    /**
     * Format currency
     */
    public static function format_currency($amount) {
        $currency = get_option('hrm_currency', 'USD');
        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'INR' => '₹'
        ];
        
        $symbol = isset($symbols[$currency]) ? $symbols[$currency] : '$';
        
        return $symbol . number_format($amount, 2);
    }
    
    /**
     * Get dashboard statistics
     */
    public static function get_dashboard_stats() {
        global $wpdb;
        
        $stats = [];
        
        // Total properties
        $properties_table = self::get_table_name('properties');
        $stats['total_properties'] = $wpdb->get_var("SELECT COUNT(*) FROM $properties_table");
        
        // Active tenants
        $tenants_table = self::get_table_name('tenants');
        $stats['active_tenants'] = $wpdb->get_var("SELECT COUNT(*) FROM $tenants_table WHERE status = 'active'");
        
        // Active agreements
        $agreements_table = self::get_table_name('rent_agreements');
        $stats['active_agreements'] = $wpdb->get_var("SELECT COUNT(*) FROM $agreements_table WHERE status = 'active'");
        
        // Total owners
        $owners_table = self::get_table_name('property_owners');
        $stats['total_owners'] = $wpdb->get_var("SELECT COUNT(*) FROM $owners_table");
        
        // Pending maintenance
        $maintenance_table = self::get_table_name('maintenance_requests');
        $stats['pending_maintenance'] = $wpdb->get_var("SELECT COUNT(*) FROM $maintenance_table WHERE status = 'pending'");
        
        // Overdue payments
        $payments_table = self::get_table_name('rent_payments');
        $current_date = current_time('Y-m-d');
        $stats['overdue_payments'] = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $payments_table WHERE status = 'pending' AND due_date < %s",
            $current_date
        ));
        
        // Monthly rent collected
        $first_day = date('Y-m-01');
        $last_day = date('Y-m-t');
        $stats['monthly_rent_collected'] = $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(amount) FROM $payments_table WHERE status = 'paid' AND payment_date BETWEEN %s AND %s",
            $first_day,
            $last_day
        ));
        
        return $stats;
    }
}