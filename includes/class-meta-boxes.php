<?php
/**
 * Meta boxes for custom post types
 */

class HRM_Meta_Boxes {
    
    public function __construct() {
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post', [$this, 'save_meta_boxes']);
    }
    
    public function add_meta_boxes() {
        // Agreement details meta box
        add_meta_box(
            'hrm_agreement_details',
            __('Agreement Details', 'housing-rent-mgmt'),
            [$this, 'render_agreement_meta_box'],
            'hrm_agreement',
            'normal',
            'high'
        );
        
        // Payment details meta box
        add_meta_box(
            'hrm_payment_details',
            __('Payment Details', 'housing-rent-mgmt'),
            [$this, 'render_payment_meta_box'],
            'hrm_receipt',
            'normal',
            'high'
        );
        
        // Maintenance details meta box
        add_meta_box(
            'hrm_maintenance_details',
            __('Maintenance Details', 'housing-rent-mgmt'),
            [$this, 'render_maintenance_meta_box'],
            'hrm_maintenance',
            'normal',
            'high'
        );
    }
    
    public function render_agreement_meta_box($post) {
        wp_nonce_field('hrm_agreement_meta_box', 'hrm_agreement_meta_box_nonce');
        
        $property_id = get_post_meta($post->ID, '_hrm_property_id', true);
        $tenant_id = get_post_meta($post->ID, '_hrm_tenant_id', true);
        $owner_id = get_post_meta($post->ID, '_hrm_owner_id', true);
        $start_date = get_post_meta($post->ID, '_hrm_start_date', true);
        $end_date = get_post_meta($post->ID, '_hrm_end_date', true);
        $monthly_rent = get_post_meta($post->ID, '_hrm_monthly_rent', true);
        $security_deposit = get_post_meta($post->ID, '_hrm_security_deposit', true);
        ?>
        <table class="form-table">
            <tr>
                <th><label for="hrm_property_id"><?php _e('Property', 'housing-rent-mgmt'); ?></label></th>
                <td>
                    <select name="hrm_property_id" id="hrm_property_id" class="regular-text">
                        <option value=""><?php _e('Select Property', 'housing-rent-mgmt'); ?></option>
                        <?php
                        $properties = HRM_Functions::get_properties();
                        foreach ($properties as $property) {
                            echo '<option value="' . $property->id . '" ' . selected($property_id, $property->id, false) . '>';
                            echo $property->property_name . ' - ' . $property->address_line1;
                            echo '</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="hrm_tenant_id"><?php _e('Tenant', 'housing-rent-mgmt'); ?></label></th>
                <td>
                    <select name="hrm_tenant_id" id="hrm_tenant_id" class="regular-text">
                        <option value=""><?php _e('Select Tenant', 'housing-rent-mgmt'); ?></option>
                        <?php
                        global $wpdb;
                        $tenants = $wpdb->get_results("SELECT id, first_name, last_name FROM " . HRM_Functions::get_table_name('tenants') . " WHERE status = 'active'");
                        foreach ($tenants as $tenant) {
                            echo '<option value="' . $tenant->id . '" ' . selected($tenant_id, $tenant->id, false) . '>';
                            echo $tenant->first_name . ' ' . $tenant->last_name;
                            echo '</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="hrm_owner_id"><?php _e('Owner', 'housing-rent-mgmt'); ?></label></th>
                <td>
                    <select name="hrm_owner_id" id="hrm_owner_id" class="regular-text">
                        <option value=""><?php _e('Select Owner', 'housing-rent-mgmt'); ?></option>
                        <?php
                        $owners = $wpdb->get_results("SELECT id, first_name, last_name FROM " . HRM_Functions::get_table_name('property_owners'));
                        foreach ($owners as $owner) {
                            echo '<option value="' . $owner->id . '" ' . selected($owner_id, $owner->id, false) . '>';
                            echo $owner->first_name . ' ' . $owner->last_name;
                            echo '</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="hrm_start_date"><?php _e('Start Date', 'housing-rent-mgmt'); ?></label></th>
                <td><input type="date" name="hrm_start_date" id="hrm_start_date" value="<?php echo $start_date; ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="hrm_end_date"><?php _e('End Date', 'housing-rent-mgmt'); ?></label></th>
                <td><input type="date" name="hrm_end_date" id="hrm_end_date" value="<?php echo $end_date; ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="hrm_monthly_rent"><?php _e('Monthly Rent', 'housing-rent-mgmt'); ?></label></th>
                <td><input type="number" step="0.01" name="hrm_monthly_rent" id="hrm_monthly_rent" value="<?php echo $monthly_rent; ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="hrm_security_deposit"><?php _e('Security Deposit', 'housing-rent-mgmt'); ?></label></th>
                <td><input type="number" step="0.01" name="hrm_security_deposit" id="hrm_security_deposit" value="<?php echo $security_deposit; ?>" class="regular-text"></td>
            </tr>
        </table>
        <?php
    }
    
    public function render_payment_meta_box($post) {
        wp_nonce_field('hrm_payment_meta_box', 'hrm_payment_meta_box_nonce');
        
        $agreement_id = get_post_meta($post->ID, '_hrm_agreement_id', true);
        $amount = get_post_meta($post->ID, '_hrm_amount', true);
        $payment_date = get_post_meta($post->ID, '_hrm_payment_date', true);
        $payment_method = get_post_meta($post->ID, '_hrm_payment_method', true);
        $transaction_id = get_post_meta($post->ID, '_hrm_transaction_id', true);
        ?>
        <table class="form-table">
            <tr>
                <th><label for="hrm_agreement_id"><?php _e('Agreement', 'housing-rent-mgmt'); ?></label></th>
                <td>
                    <select name="hrm_agreement_id" id="hrm_agreement_id" class="regular-text">
                        <option value=""><?php _e('Select Agreement', 'housing-rent-mgmt'); ?></option>
                        <?php
                        $agreements = HRM_Functions::get_active_agreements();
                        foreach ($agreements as $agreement) {
                            echo '<option value="' . $agreement->id . '" ' . selected($agreement_id, $agreement->id, false) . '>';
                            echo $agreement->agreement_number;
                            echo '</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="hrm_amount"><?php _e('Amount', 'housing-rent-mgmt'); ?></label></th>
                <td><input type="number" step="0.01" name="hrm_amount" id="hrm_amount" value="<?php echo $amount; ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="hrm_payment_date"><?php _e('Payment Date', 'housing-rent-mgmt'); ?></label></th>
                <td><input type="date" name="hrm_payment_date" id="hrm_payment_date" value="<?php echo $payment_date; ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="hrm_payment_method"><?php _e('Payment Method', 'housing-rent-mgmt'); ?></label></th>
                <td>
                    <select name="hrm_payment_method" id="hrm_payment_method" class="regular-text">
                        <option value="cash" <?php selected($payment_method, 'cash'); ?>><?php _e('Cash', 'housing-rent-mgmt'); ?></option>
                        <option value="check" <?php selected($payment_method, 'check'); ?>><?php _e('Check', 'housing-rent-mgmt'); ?></option>
                        <option value="bank_transfer" <?php selected($payment_method, 'bank_transfer'); ?>><?php _e('Bank Transfer', 'housing-rent-mgmt'); ?></option>
                        <option value="credit_card" <?php selected($payment_method, 'credit_card'); ?>><?php _e('Credit Card', 'housing-rent-mgmt'); ?></option>
                        <option value="online" <?php selected($payment_method, 'online'); ?>><?php _e('Online Payment', 'housing-rent-mgmt'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="hrm_transaction_id"><?php _e('Transaction ID', 'housing-rent-mgmt'); ?></label></th>
                <td><input type="text" name="hrm_transaction_id" id="hrm_transaction_id" value="<?php echo $transaction_id; ?>" class="regular-text"></td>
            </tr>
        </table>
        <?php
    }
    
    public function render_maintenance_meta_box($post) {
        wp_nonce_field('hrm_maintenance_meta_box', 'hrm_maintenance_meta_box_nonce');
        
        $property_id = get_post_meta($post->ID, '_hrm_property_id', true);
        $tenant_id = get_post_meta($post->ID, '_hrm_tenant_id', true);
        $issue_type = get_post_meta($post->ID, '_hrm_issue_type', true);
        $priority = get_post_meta($post->ID, '_hrm_priority', true);
        $estimated_cost = get_post_meta($post->ID, '_hrm_estimated_cost', true);
        $status = get_post_meta($post->ID, '_hrm_status', true);
        ?>
        <table class="form-table">
            <tr>
                <th><label for="hrm_property_id"><?php _e('Property', 'housing-rent-mgmt'); ?></label></th>
                <td>
                    <select name="hrm_property_id" id="hrm_property_id" class="regular-text">
                        <option value=""><?php _e('Select Property', 'housing-rent-mgmt'); ?></option>
                        <?php
                        $properties = HRM_Functions::get_properties();
                        foreach ($properties as $property) {
                            echo '<option value="' . $property->id . '" ' . selected($property_id, $property->id, false) . '>';
                            echo $property->property_name;
                            echo '</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="hrm_tenant_id"><?php _e('Reported by', 'housing-rent-mgmt'); ?></label></th>
                <td>
                    <select name="hrm_tenant_id" id="hrm_tenant_id" class="regular-text">
                        <option value=""><?php _e('Select Tenant', 'housing-rent-mgmt'); ?></option>
                        <?php
                        global $wpdb;
                        $tenants = $wpdb->get_results("SELECT id, first_name, last_name FROM " . HRM_Functions::get_table_name('tenants'));
                        foreach ($tenants as $tenant) {
                            echo '<option value="' . $tenant->id . '" ' . selected($tenant_id, $tenant->id, false) . '>';
                            echo $tenant->first_name . ' ' . $tenant->last_name;
                            echo '</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="hrm_issue_type"><?php _e('Issue Type', 'housing-rent-mgmt'); ?></label></th>
                <td>
                    <select name="hrm_issue_type" id="hrm_issue_type" class="regular-text">
                        <option value="plumbing" <?php selected($issue_type, 'plumbing'); ?>><?php _e('Plumbing', 'housing-rent-mgmt'); ?></option>
                        <option value="electrical" <?php selected($issue_type, 'electrical'); ?>><?php _e('Electrical', 'housing-rent-mgmt'); ?></option>
                        <option value="hvac" <?php selected($issue_type, 'hvac'); ?>><?php _e('HVAC', 'housing-rent-mgmt'); ?></option>
                        <option value="appliance" <?php selected($issue_type, 'appliance'); ?>><?php _e('Appliance', 'housing-rent-mgmt'); ?></option>
                        <option value="structural" <?php selected($issue_type, 'structural'); ?>><?php _e('Structural', 'housing-rent-mgmt'); ?></option>
                        <option value="other" <?php selected($issue_type, 'other'); ?>><?php _e('Other', 'housing-rent-mgmt'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="hrm_priority"><?php _e('Priority', 'housing-rent-mgmt'); ?></label></th>
                <td>
                    <select name="hrm_priority" id="hrm_priority" class="regular-text">
                        <option value="low" <?php selected($priority, 'low'); ?>><?php _e('Low', 'housing-rent-mgmt'); ?></option>
                        <option value="medium" <?php selected($priority, 'medium'); ?>><?php _e('Medium', 'housing-rent-mgmt'); ?></option>
                        <option value="high" <?php selected($priority, 'high'); ?>><?php _e('High', 'housing-rent-mgmt'); ?></option>
                        <option value="emergency" <?php selected($priority, 'emergency'); ?>><?php _e('Emergency', 'housing-rent-mgmt'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="hrm_estimated_cost"><?php _e('Estimated Cost', 'housing-rent-mgmt'); ?></label></th>
                <td><input type="number" step="0.01" name="hrm_estimated_cost" id="hrm_estimated_cost" value="<?php echo $estimated_cost; ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="hrm_status"><?php _e('Status', 'housing-rent-mgmt'); ?></label></th>
                <td>
                    <select name="hrm_status" id="hrm_status" class="regular-text">
                        <option value="pending" <?php selected($status, 'pending'); ?>><?php _e('Pending', 'housing-rent-mgmt'); ?></option>
                        <option value="in_progress" <?php selected($status, 'in_progress'); ?>><?php _e('In Progress', 'housing-rent-mgmt'); ?></option>
                        <option value="completed" <?php selected($status, 'completed'); ?>><?php _e('Completed', 'housing-rent-mgmt'); ?></option>
                        <option value="cancelled" <?php selected($status, 'cancelled'); ?>><?php _e('Cancelled', 'housing-rent-mgmt'); ?></option>
                    </select>
                </td>
            </tr>
        </table>
        <?php
    }
    
    public function save_meta_boxes($post_id) {
        // Save agreement meta box
        if (isset($_POST['hrm_agreement_meta_box_nonce']) && wp_verify_nonce($_POST['hrm_agreement_meta_box_nonce'], 'hrm_agreement_meta_box')) {
            if (isset($_POST['hrm_property_id'])) {
                update_post_meta($post_id, '_hrm_property_id', sanitize_text_field($_POST['hrm_property_id']));
            }
            if (isset($_POST['hrm_tenant_id'])) {
                update_post_meta($post_id, '_hrm_tenant_id', sanitize_text_field($_POST['hrm_tenant_id']));
            }
            if (isset($_POST['hrm_owner_id'])) {
                update_post_meta($post_id, '_hrm_owner_id', sanitize_text_field($_POST['hrm_owner_id']));
            }
            if (isset($_POST['hrm_start_date'])) {
                update_post_meta($post_id, '_hrm_start_date', sanitize_text_field($_POST['hrm_start_date']));
            }
            if (isset($_POST['hrm_end_date'])) {
                update_post_meta($post_id, '_hrm_end_date', sanitize_text_field($_POST['hrm_end_date']));
            }
            if (isset($_POST['hrm_monthly_rent'])) {
                update_post_meta($post_id, '_hrm_monthly_rent', floatval($_POST['hrm_monthly_rent']));
            }
            if (isset($_POST['hrm_security_deposit'])) {
                update_post_meta($post_id, '_hrm_security_deposit', floatval($_POST['hrm_security_deposit']));
            }
        }
        
        // Save payment meta box
        if (isset($_POST['hrm_payment_meta_box_nonce']) && wp_verify_nonce($_POST['hrm_payment_meta_box_nonce'], 'hrm_payment_meta_box')) {
            if (isset($_POST['hrm_agreement_id'])) {
                update_post_meta($post_id, '_hrm_agreement_id', sanitize_text_field($_POST['hrm_agreement_id']));
            }
            if (isset($_POST['hrm_amount'])) {
                update_post_meta($post_id, '_hrm_amount', floatval($_POST['hrm_amount']));
            }
            if (isset($_POST['hrm_payment_date'])) {
                update_post_meta($post_id, '_hrm_payment_date', sanitize_text_field($_POST['hrm_payment_date']));
            }
            if (isset($_POST['hrm_payment_method'])) {
                update_post_meta($post_id, '_hrm_payment_method', sanitize_text_field($_POST['hrm_payment_method']));
            }
            if (isset($_POST['hrm_transaction_id'])) {
                update_post_meta($post_id, '_hrm_transaction_id', sanitize_text_field($_POST['hrm_transaction_id']));
            }
        }
        
        // Save maintenance meta box
        if (isset($_POST['hrm_maintenance_meta_box_nonce']) && wp_verify_nonce($_POST['hrm_maintenance_meta_box_nonce'], 'hrm_maintenance_meta_box')) {
            if (isset($_POST['hrm_property_id'])) {
                update_post_meta($post_id, '_hrm_property_id', sanitize_text_field($_POST['hrm_property_id']));
            }
            if (isset($_POST['hrm_tenant_id'])) {
                update_post_meta($post_id, '_hrm_tenant_id', sanitize_text_field($_POST['hrm_tenant_id']));
            }
            if (isset($_POST['hrm_issue_type'])) {
                update_post_meta($post_id, '_hrm_issue_type', sanitize_text_field($_POST['hrm_issue_type']));
            }
            if (isset($_POST['hrm_priority'])) {
                update_post_meta($post_id, '_hrm_priority', sanitize_text_field($_POST['hrm_priority']));
            }
            if (isset($_POST['hrm_estimated_cost'])) {
                update_post_meta($post_id, '_hrm_estimated_cost', floatval($_POST['hrm_estimated_cost']));
            }
            if (isset($_POST['hrm_status'])) {
                update_post_meta($post_id, '_hrm_status', sanitize_text_field($_POST['hrm_status']));
            }
        }
    }
}