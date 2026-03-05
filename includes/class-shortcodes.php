<?php
/**
 * Shortcodes for frontend display
 */

class HRM_Shortcodes {
    
    public function __construct() {
        add_shortcode('hrm_tenant_dashboard', [$this, 'tenant_dashboard']);
        add_shortcode('hrm_pay_rent', [$this, 'pay_rent_form']);
        add_shortcode('hrm_maintenance_request', [$this, 'maintenance_request_form']);
        add_shortcode('hrm_properties_list', [$this, 'properties_list']);
        add_shortcode('hrm_rental_history', [$this, 'rental_history']);
    }
    
    public function tenant_dashboard($atts) {
        if (!is_user_logged_in()) {
            return '<p>' . __('Please login to view your dashboard.', 'housing-rent-mgmt') . '</p>';
        }
        
        ob_start();
        include HRM_PLUGIN_DIR . 'public/partials/tenant-dashboard.php';
        return ob_get_clean();
    }
    
    public function pay_rent_form($atts) {
        if (!is_user_logged_in()) {
            return '<p>' . __('Please login to pay rent.', 'housing-rent-mgmt') . '</p>';
        }
        
        ob_start();
        include HRM_PLUGIN_DIR . 'public/partials/pay-rent.php';
        return ob_get_clean();
    }
    
    public function maintenance_request_form($atts) {
        if (!is_user_logged_in()) {
            return '<p>' . __('Please login to submit a maintenance request.', 'housing-rent-mgmt') . '</p>';
        }
        
        ob_start();
        include HRM_PLUGIN_DIR . 'public/partials/maintenance-request.php';
        return ob_get_clean();
    }
    
    public function properties_list($atts) {
        $atts = shortcode_atts([
            'status' => 'available',
            'limit' => 10,
            'city' => ''
        ], $atts);
        
        $properties = HRM_Functions::get_properties($atts);
        
        ob_start();
        ?>
        <div class="hrm-properties-list">
            <?php if (empty($properties)): ?>
                <p><?php _e('No properties found.', 'housing-rent-mgmt'); ?></p>
            <?php else: ?>
                <?php foreach ($properties as $property): ?>
                    <div class="hrm-property-card">
                        <h3><?php echo esc_html($property->property_name); ?></h3>
                        <p><strong><?php _e('Address:', 'housing-rent-mgmt'); ?></strong> <?php echo esc_html($property->address_line1); ?></p>
                        <p><strong><?php _e('City:', 'housing-rent-mgmt'); ?></strong> <?php echo esc_html($property->city); ?></p>
                        <p><strong><?php _e('Bedrooms:', 'housing-rent-mgmt'); ?></strong> <?php echo esc_html($property->bedrooms); ?></p>
                        <p><strong><?php _e('Bathrooms:', 'housing-rent-mgmt'); ?></strong> <?php echo esc_html($property->bathrooms); ?></p>
                        <p><strong><?php _e('Status:', 'housing-rent-mgmt'); ?></strong> <?php echo esc_html($property->status); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    public function rental_history($atts) {
        if (!is_user_logged_in()) {
            return '<p>' . __('Please login to view your rental history.', 'housing-rent-mgmt') . '</p>';
        }
        
        $current_user = wp_get_current_user();
        $tenant_email = $current_user->user_email;
        
        global $wpdb;
        $tenant_table = HRM_Functions::get_table_name('tenants');
        $tenant = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $tenant_table WHERE email = %s",
            $tenant_email
        ));
        
        if (!$tenant) {
            return '<p>' . __('No rental history found.', 'housing-rent-mgmt') . '</p>';
        }
        
        $payments_table = HRM_Functions::get_table_name('rent_payments');
        $payments = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $payments_table WHERE tenant_id = %d ORDER BY payment_date DESC",
            $tenant->id
        ));
        
        ob_start();
        ?>
        <div class="hrm-rental-history">
            <h3><?php _e('Your Payment History', 'housing-rent-mgmt'); ?></h3>
            
            <?php if (empty($payments)): ?>
                <p><?php _e('No payment history found.', 'housing-rent-mgmt'); ?></p>
            <?php else: ?>
                <table class="hrm-table">
                    <thead>
                        <tr>
                            <th><?php _e('Date', 'housing-rent-mgmt'); ?></th>
                            <th><?php _e('Amount', 'housing-rent-mgmt'); ?></th>
                            <th><?php _e('Status', 'housing-rent-mgmt'); ?></th>
                            <th><?php _e('Method', 'housing-rent-mgmt'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td><?php echo date_i18n(get_option('date_format'), strtotime($payment->payment_date)); ?></td>
                                <td><?php echo HRM_Functions::format_currency($payment->amount); ?></td>
                                <td><span class="hrm-status hrm-status-<?php echo $payment->status; ?>"><?php echo ucfirst($payment->status); ?></span></td>
                                <td><?php echo ucfirst(str_replace('_', ' ', $payment->payment_method)); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}