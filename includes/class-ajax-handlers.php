<?php
/**
 * AJAX handlers for frontend and admin
 */

class HRM_Ajax_Handlers {
    
    public function __construct() {
        // Admin AJAX
        add_action('wp_ajax_hrm_add_property', [$this, 'add_property']);
        add_action('wp_ajax_hrm_add_owner', [$this, 'add_owner']);
        add_action('wp_ajax_hrm_add_tenant', [$this, 'add_tenant']);
        add_action('wp_ajax_hrm_add_agreement', [$this, 'add_agreement']);
        add_action('wp_ajax_hrm_add_payment', [$this, 'add_payment']);
        add_action('wp_ajax_hrm_update_maintenance', [$this, 'update_maintenance']);
        add_action('wp_ajax_hrm_get_stats', [$this, 'get_stats']);
        
        // Public AJAX
        add_action('wp_ajax_hrm_submit_maintenance_request', [$this, 'submit_maintenance_request']);
        add_action('wp_ajax_nopriv_hrm_submit_maintenance_request', [$this, 'submit_maintenance_request']);
        
        add_action('wp_ajax_hrm_process_rent_payment', [$this, 'process_rent_payment']);
        add_action('wp_ajax_hrm_get_tenant_details', [$this, 'get_tenant_details']);
    }
    
    public function add_property() {
        $this->verify_nonce('hrm_ajax_nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        global $wpdb;
        $table = HRM_Functions::get_table_name('properties');
        
        $data = [
            'property_name' => sanitize_text_field($_POST['property_name']),
            'property_type' => sanitize_text_field($_POST['property_type']),
            'address_line1' => sanitize_text_field($_POST['address_line1']),
            'address_line2' => sanitize_text_field($_POST['address_line2']),
            'city' => sanitize_text_field($_POST['city']),
            'state' => sanitize_text_field($_POST['state']),
            'zip_code' => sanitize_text_field($_POST['zip_code']),
            'country' => sanitize_text_field($_POST['country']),
            'bedrooms' => intval($_POST['bedrooms']),
            'bathrooms' => floatval($_POST['bathrooms']),
            'square_feet' => intval($_POST['square_feet']),
            'furnished' => isset($_POST['furnished']) ? 1 : 0,
            'parking_spaces' => intval($_POST['parking_spaces']),
            'description' => sanitize_textarea_field($_POST['description']),
            'amenities' => sanitize_textarea_field($_POST['amenities']),
            'status' => sanitize_text_field($_POST['status'])
        ];
        
        $result = $wpdb->insert($table, $data);
        
        if ($result) {
            wp_send_json_success(['id' => $wpdb->insert_id, 'message' => 'Property added successfully']);
        } else {
            wp_send_json_error('Failed to add property');
        }
    }
    
    public function add_owner() {
        $this->verify_nonce('hrm_ajax_nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        global $wpdb;
        $table = HRM_Functions::get_table_name('property_owners');
        
        $data = [
            'first_name' => sanitize_text_field($_POST['first_name']),
            'last_name' => sanitize_text_field($_POST['last_name']),
            'email' => sanitize_email($_POST['email']),
            'phone' => sanitize_text_field($_POST['phone']),
            'alternate_phone' => sanitize_text_field($_POST['alternate_phone']),
            'address' => sanitize_textarea_field($_POST['address']),
            'city' => sanitize_text_field($_POST['city']),
            'state' => sanitize_text_field($_POST['state']),
            'zip_code' => sanitize_text_field($_POST['zip_code']),
            'id_proof_type' => sanitize_text_field($_POST['id_proof_type']),
            'id_proof_number' => sanitize_text_field($_POST['id_proof_number']),
            'bank_name' => sanitize_text_field($_POST['bank_name']),
            'bank_account_number' => sanitize_text_field($_POST['bank_account_number']),
            'bank_routing_number' => sanitize_text_field($_POST['bank_routing_number']),
            'tax_id' => sanitize_text_field($_POST['tax_id']),
            'notes' => sanitize_textarea_field($_POST['notes'])
        ];
        
        $result = $wpdb->insert($table, $data);
        
        if ($result) {
            wp_send_json_success(['id' => $wpdb->insert_id, 'message' => 'Owner added successfully']);
        } else {
            wp_send_json_error('Failed to add owner');
        }
    }
    
    public function add_tenant() {
        $this->verify_nonce('hrm_ajax_nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        global $wpdb;
        $table = HRM_Functions::get_table_name('tenants');
        
        $data = [
            'first_name' => sanitize_text_field($_POST['first_name']),
            'last_name' => sanitize_text_field($_POST['last_name']),
            'email' => sanitize_email($_POST['email']),
            'phone' => sanitize_text_field($_POST['phone']),
            'alternate_phone' => sanitize_text_field($_POST['alternate_phone']),
            'date_of_birth' => sanitize_text_field($_POST['date_of_birth']),
            'emergency_contact_name' => sanitize_text_field($_POST['emergency_contact_name']),
            'emergency_contact_phone' => sanitize_text_field($_POST['emergency_contact_phone']),
            'employment_status' => sanitize_text_field($_POST['employment_status']),
            'employer_name' => sanitize_text_field($_POST['employer_name']),
            'annual_income' => floatval($_POST['annual_income']),
            'id_proof_type' => sanitize_text_field($_POST['id_proof_type']),
            'id_proof_number' => sanitize_text_field($_POST['id_proof_number']),
            'current_address' => sanitize_textarea_field($_POST['current_address']),
            'previous_landlord_name' => sanitize_text_field($_POST['previous_landlord_name']),
            'previous_landlord_phone' => sanitize_text_field($_POST['previous_landlord_phone']),
            'notes' => sanitize_textarea_field($_POST['notes']),
            'status' => 'active'
        ];
        
        $result = $wpdb->insert($table, $data);
        
        if ($result) {
            wp_send_json_success(['id' => $wpdb->insert_id, 'message' => 'Tenant added successfully']);
        } else {
            wp_send_json_error('Failed to add tenant');
        }
    }
    
    public function add_agreement() {
        $this->verify_nonce('hrm_ajax_nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        global $wpdb;
        $table = HRM_Functions::get_table_name('rent_agreements');
        
        $data = [
            'agreement_number' => HRM_Functions::generate_agreement_number(),
            'property_id' => intval($_POST['property_id']),
            'tenant_id' => intval($_POST['tenant_id']),
            'owner_id' => intval($_POST['owner_id']),
            'agreement_type' => sanitize_text_field($_POST['agreement_type']),
            'start_date' => sanitize_text_field($_POST['start_date']),
            'end_date' => sanitize_text_field($_POST['end_date']),
            'monthly_rent' => floatval($_POST['monthly_rent']),
            'security_deposit' => floatval($_POST['security_deposit']),
            'rent_due_day' => intval($_POST['rent_due_day']),
            'late_fee' => floatval($_POST['late_fee']),
            'late_fee_after_days' => intval($_POST['late_fee_after_days']),
            'notice_period_days' => intval($_POST['notice_period_days']),
            'maintenance_responsibility' => sanitize_textarea_field($_POST['maintenance_responsibility']),
            'utilities_included' => sanitize_textarea_field($_POST['utilities_included']),
            'special_clauses' => sanitize_textarea_field($_POST['special_clauses']),
            'status' => 'active'
        ];
        
        $result = $wpdb->insert($table, $data);
        
        if ($result) {
            // Update property status
            $wpdb->update(
                HRM_Functions::get_table_name('properties'),
                ['status' => 'rented'],
                ['id' => $data['property_id']]
            );
            
            wp_send_json_success(['id' => $wpdb->insert_id, 'message' => 'Agreement created successfully']);
        } else {
            wp_send_json_error('Failed to create agreement');
        }
    }
    
    public function add_payment() {
        $this->verify_nonce('hrm_ajax_nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        global $wpdb;
        $table = HRM_Functions::get_table_name('rent_payments');
        
        $agreement_id = intval($_POST['agreement_id']);
        
        // Get agreement details
        $agreement = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM " . HRM_Functions::get_table_name('rent_agreements') . " WHERE id = %d",
            $agreement_id
        ));
        
        if (!$agreement) {
            wp_send_json_error('Agreement not found');
        }
        
        $data = [
            'agreement_id' => $agreement_id,
            'property_id' => $agreement->property_id,
            'tenant_id' => $agreement->tenant_id,
            'amount' => floatval($_POST['amount']),
            'payment_date' => sanitize_text_field($_POST['payment_date']),
            'due_date' => $agreement->start_date,
            'for_month' => sanitize_text_field($_POST['for_month']),
            'payment_type' => 'rent',
            'payment_method' => sanitize_text_field($_POST['payment_method']),
            'transaction_id' => sanitize_text_field($_POST['transaction_id']),
            'receipt_number' => 'RCT-' . time(),
            'notes' => sanitize_textarea_field($_POST['notes']),
            'status' => 'paid'
        ];
        
        $result = $wpdb->insert($table, $data);
        
        if ($result) {
            // Send receipt email
            $this->send_payment_receipt($data);
            
            wp_send_json_success(['id' => $wpdb->insert_id, 'message' => 'Payment recorded successfully']);
        } else {
            wp_send_json_error('Failed to record payment');
        }
    }
    
    public function update_maintenance() {
        $this->verify_nonce('hrm_ajax_nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        global $wpdb;
        $table = HRM_Functions::get_table_name('maintenance_requests');
        
        $data = [
            'status' => sanitize_text_field($_POST['status']),
            'assigned_to' => sanitize_text_field($_POST['assigned_to']),
            'estimated_cost' => floatval($_POST['estimated_cost']),
            'actual_cost' => floatval($_POST['actual_cost']),
            'notes' => sanitize_textarea_field($_POST['notes'])
        ];
        
        if ($_POST['status'] == 'completed') {
            $data['completion_date'] = current_time('mysql');
        }
        
        $result = $wpdb->update(
            $table,
            $data,
            ['id' => intval($_POST['request_id'])]
        );
        
        if ($result !== false) {
            wp_send_json_success(['message' => 'Maintenance request updated successfully']);
        } else {
            wp_send_json_error('Failed to update maintenance request');
        }
    }
    
    public function submit_maintenance_request() {
        $this->verify_nonce('hrm_public_nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error('Please login to submit a request');
        }
        
        global $wpdb;
        $table = HRM_Functions::get_table_name('maintenance_requests');
        
        $current_user = wp_get_current_user();
        
        // Get tenant ID
        $tenant_table = HRM_Functions::get_table_name('tenants');
        $tenant = $wpdb->get_row($wpdb->prepare(
            "SELECT id FROM $tenant_table WHERE email = %s",
            $current_user->user_email
        ));
        
        if (!$tenant) {
            wp_send_json_error('Tenant record not found');
        }
        
        $data = [
            'property_id' => intval($_POST['property_id']),
            'tenant_id' => $tenant->id,
            'issue_type' => sanitize_text_field($_POST['issue_type']),
            'priority' => sanitize_text_field($_POST['priority']),
            'description' => sanitize_textarea_field($_POST['description']),
            'status' => 'pending'
        ];
        
        // Handle file upload if any
        if (!empty($_FILES['attachment'])) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            $uploaded = wp_handle_upload($_FILES['attachment'], ['test_form' => false]);
            
            if (isset($uploaded['url'])) {
                $data['attachments'] = $uploaded['url'];
            }
        }
        
        $result = $wpdb->insert($table, $data);
        
        if ($result) {
            // Notify admin
            $this->notify_admin_new_request($data);
            
            wp_send_json_success(['message' => 'Maintenance request submitted successfully']);
        } else {
            wp_send_json_error('Failed to submit request');
        }
    }
    
    public function process_rent_payment() {
        $this->verify_nonce('hrm_public_nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error('Please login to make a payment');
        }
        
        // Process payment through payment gateway
        // This is a simplified version - implement actual payment processing
        
        global $wpdb;
        $payments_table = HRM_Functions::get_table_name('rent_payments');
        
        $agreement_id = intval($_POST['agreement_id']);
        $amount = floatval($_POST['amount']);
        
        $current_user = wp_get_current_user();
        
        // Get tenant ID
        $tenant_table = HRM_Functions::get_table_name('tenants');
        $tenant = $wpdb->get_row($wpdb->prepare(
            "SELECT id FROM $tenant_table WHERE email = %s",
            $current_user->user_email
        ));
        
        if (!$tenant) {
            wp_send_json_error('Tenant record not found');
        }
        
        // Get agreement details
        $agreement = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM " . HRM_Functions::get_table_name('rent_agreements') . " WHERE id = %d AND tenant_id = %d",
            $agreement_id,
            $tenant->id
        ));
        
        if (!$agreement) {
            wp_send_json_error('Invalid agreement');
        }
        
        $data = [
            'agreement_id' => $agreement_id,
            'property_id' => $agreement->property_id,
            'tenant_id' => $tenant->id,
            'amount' => $amount,
            'payment_date' => current_time('Y-m-d'),
            'due_date' => date('Y-m-d', strtotime($agreement->start_date)),
            'for_month' => date('Y-m-01'),
            'payment_type' => 'rent',
            'payment_method' => 'online',
            'transaction_id' => 'TXN-' . uniqid(),
            'receipt_number' => 'RCT-' . time(),
            'status' => 'paid'
        ];
        
        $result = $wpdb->insert($payments_table, $data);
        
        if ($result) {
            wp_send_json_success([
                'message' => 'Payment successful',
                'receipt_number' => $data['receipt_number'],
                'transaction_id' => $data['transaction_id']
            ]);
        } else {
            wp_send_json_error('Payment failed');
        }
    }
    
    public function get_tenant_details() {
        $this->verify_nonce('hrm_public_nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error('Unauthorized');
        }
        
        $current_user = wp_get_current_user();
        
        global $wpdb;
        $tenant_table = HRM_Functions::get_table_name('tenants');
        $agreement_table = HRM_Functions::get_table_name('rent_agreements');
        $payments_table = HRM_Functions::get_table_name('rent_payments');
        
        $tenant = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $tenant_table WHERE email = %s",
            $current_user->user_email
        ));
        
        if (!$tenant) {
            wp_send_json_error('Tenant not found');
        }
        
        // Get active agreement
        $agreement = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $agreement_table WHERE tenant_id = %d AND status = 'active'",
            $tenant->id
        ));
        
        // Get recent payments
        $payments = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $payments_table WHERE tenant_id = %d ORDER BY payment_date DESC LIMIT 5",
            $tenant->id
        ));
        
        // Get pending maintenance
        $maintenance_table = HRM_Functions::get_table_name('maintenance_requests');
        $maintenance = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $maintenance_table WHERE tenant_id = %d AND status != 'completed' ORDER BY request_date DESC",
            $tenant->id
        ));
        
        wp_send_json_success([
            'tenant' => $tenant,
            'agreement' => $agreement,
            'recent_payments' => $payments,
            'maintenance_requests' => $maintenance
        ]);
    }
    
    public function get_stats() {
        $this->verify_nonce('hrm_ajax_nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        $stats = HRM_Functions::get_dashboard_stats();
        wp_send_json_success($stats);
    }
    
    private function verify_nonce($nonce_name) {
        if (!check_ajax_referer($nonce_name, 'nonce', false)) {
            wp_send_json_error('Invalid security token');
        }
    }
    
    private function send_payment_receipt($payment_data) {
        $tenant = HRM_Functions::get_tenant($payment_data['tenant_id']);
        
        if (!$tenant) {
            return;
        }
        
        $subject = sprintf(__('Payment Receipt - %s', 'housing-rent-mgmt'), $payment_data['receipt_number']);
        
        $message = '<h2>' . __('Payment Receipt', 'housing-rent-mgmt') . '</h2>';
        $message .= '<p><strong>' . __('Receipt Number:', 'housing-rent-mgmt') . '</strong> ' . $payment_data['receipt_number'] . '</p>';
        $message .= '<p><strong>' . __('Amount:', 'housing-rent-mgmt') . '</strong> ' . HRM_Functions::format_currency($payment_data['amount']) . '</p>';
        $message .= '<p><strong>' . __('Date:', 'housing-rent-mgmt') . '</strong> ' . $payment_data['payment_date'] . '</p>';
        $message .= '<p><strong>' . __('Payment Method:', 'housing-rent-mgmt') . '</strong> ' . ucfirst($payment_data['payment_method']) . '</p>';
        
        if (!empty($payment_data['transaction_id'])) {
            $message .= '<p><strong>' . __('Transaction ID:', 'housing-rent-mgmt') . '</strong> ' . $payment_data['transaction_id'] . '</p>';
        }
        
        HRM_Functions::send_notification($tenant->email, $subject, $message);
    }
    
    private function notify_admin_new_request($request_data) {
        $admin_email = get_option('admin_email');
        
        $subject = __('New Maintenance Request', 'housing-rent-mgmt');
        
        $message = '<h2>' . __('New Maintenance Request Submitted', 'housing-rent-mgmt') . '</h2>';
        $message .= '<p><strong>' . __('Issue Type:', 'housing-rent-mgmt') . '</strong> ' . $request_data['issue_type'] . '</p>';
        $message .= '<p><strong>' . __('Priority:', 'housing-rent-mgmt') . '</strong> ' . $request_data['priority'] . '</p>';
        $message .= '<p><strong>' . __('Description:', 'housing-rent-mgmt') . '</strong> ' . $request_data['description'] . '</p>';
        $message .= '<p><a href="' . admin_url('admin.php?page=hrm-maintenance') . '">' . __('View in Admin', 'housing-rent-mgmt') . '</a></p>';
        
        HRM_Functions::send_notification($admin_email, $subject, $message);
    }
}