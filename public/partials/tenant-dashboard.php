<?php
// Get current user
$current_user = wp_get_current_user();

// Get tenant details
global $wpdb;
$tenant_table = HRM_Functions::get_table_name('tenants');
$tenant = $wpdb->get_row($wpdb->prepare(
    "SELECT * FROM $tenant_table WHERE email = %s",
    $current_user->user_email
));

if (!$tenant) {
    echo '<div class="hrm-alert hrm-alert-error">' . __('No tenant record found for your account.', 'housing-rent-mgmt') . '</div>';
    return;
}

// Get active agreement
$agreement_table = HRM_Functions::get_table_name('rent_agreements');
$agreement = $wpdb->get_row($wpdb->prepare(
    "SELECT ra.*, p.property_name, p.address_line1, p.address_line2, p.city, p.state, p.zip_code,
            p.bedrooms, p.bathrooms, p.square_feet
     FROM $agreement_table ra
     JOIN " . HRM_Functions::get_table_name('properties') . " p ON ra.property_id = p.id
     WHERE ra.tenant_id = %d AND ra.status = 'active'
     ORDER BY ra.start_date DESC
     LIMIT 1",
    $tenant->id
));

// Get recent payments
$payments_table = HRM_Functions::get_table_name('rent_payments');
$recent_payments = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM $payments_table 
     WHERE tenant_id = %d 
     ORDER BY payment_date DESC 
     LIMIT 5",
    $tenant->id
));

// Get pending maintenance
$maintenance_table = HRM_Functions::get_table_name('maintenance_requests');
$maintenance_requests = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM $maintenance_table 
     WHERE tenant_id = %d AND status != 'completed'
     ORDER BY request_date DESC",
    $tenant->id
));

// Calculate next payment due
$next_payment_due = null;
if ($agreement) {
    $current_month = date('Y-m-01');
    $existing_payment = $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM $payments_table 
         WHERE tenant_id = %d AND for_month = %s AND status = 'paid'",
        $tenant->id,
        $current_month
    ));
    
    if (!$existing_payment) {
        $next_payment_due = $agreement;
    }
}
?>

<div class="hrm-container">
    <div class="hrm-dashboard">
        <div class="hrm-dashboard-header">
            <h2><?php printf(__('Welcome, %s!', 'housing-rent-mgmt'), $tenant->first_name . ' ' . $tenant->last_name); ?></h2>
            <p><?php _e('Manage your rental information, payments, and maintenance requests.', 'housing-rent-mgmt'); ?></p>
        </div>
        
        <div class="hrm-dashboard-grid">
            <!-- Current Rental Card -->
            <div class="hrm-dashboard-card">
                <h3><?php _e('Current Rental', 'housing-rent-mgmt'); ?></h3>
                <?php if ($agreement): ?>
                    <p><strong><?php _e('Property:', 'housing-rent-mgmt'); ?></strong> <?php echo esc_html($agreement->property_name); ?></p>
                    <p><strong><?php _e('Address:', 'housing-rent-mgmt'); ?></strong> 
                        <?php 
                        echo esc_html($agreement->address_line1);
                        if ($agreement->address_line2) {
                            echo ', ' . esc_html($agreement->address_line2);
                        }
                        ?><br>
                        <?php echo esc_html($agreement->city . ', ' . $agreement->state . ' ' . $agreement->zip_code); ?>
                    </p>
                    <p><strong><?php _e('Monthly Rent:', 'housing-rent-mgmt'); ?></strong> 
                        <span class="amount"><?php echo HRM_Functions::format_currency($agreement->monthly_rent); ?></span>
                    </p>
                    <p><strong><?php _e('Lease Period:', 'housing-rent-mgmt'); ?></strong> 
                        <?php echo date_i18n(get_option('date_format'), strtotime($agreement->start_date)); ?> - 
                        <?php echo date_i18n(get_option('date_format'), strtotime($agreement->end_date)); ?>
                    </p>
                    <?php if ($agreement->security_deposit): ?>
                        <p><strong><?php _e('Security Deposit:', 'housing-rent-mgmt'); ?></strong> 
                            <?php echo HRM_Functions::format_currency($agreement->security_deposit); ?>
                        </p>
                    <?php endif; ?>
                <?php else: ?>
                    <p><?php _e('No active rental agreement found.', 'housing-rent-mgmt'); ?></p>
                <?php endif; ?>
            </div>
            
            <!-- Quick Actions Card -->
            <div class="hrm-dashboard-card">
                <h3><?php _e('Quick Actions', 'housing-rent-mgmt'); ?></h3>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <?php if ($next_payment_due): ?>
                        <a href="[hrm_pay_rent]" class="hrm-button" style="text-align: center;"><?php _e('Pay This Month\'s Rent', 'housing-rent-mgmt'); ?></a>
                    <?php endif; ?>
                    <a href="[hrm_maintenance_request]" class="hrm-button hrm-button-secondary" style="text-align: center;"><?php _e('Submit Maintenance Request', 'housing-rent-mgmt'); ?></a>
                    <a href="#payment-history" class="hrm-button hrm-button-secondary" style="text-align: center;"><?php _e('View Payment History', 'housing-rent-mgmt'); ?></a>
                </div>
            </div>
            
            <!-- Payment Status Card -->
            <div class="hrm-dashboard-card">
                <h3><?php _e('Payment Status', 'housing-rent-mgmt'); ?></h3>
                <?php if ($next_payment_due): ?>
                    <p style="color: #dc3232;"><?php _e('Payment due for this month', 'housing-rent-mgmt'); ?></p>
                    <p><strong><?php _e('Amount Due:', 'housing-rent-mgmt'); ?></strong> 
                        <span class="amount"><?php echo HRM_Functions::format_currency($agreement->monthly_rent); ?></span>
                    </p>
                    <p><strong><?php _e('Due Date:', 'housing-rent-mgmt'); ?></strong> 
                        <?php echo date_i18n(get_option('date_format'), strtotime($agreement->start_date . ' + ' . ($agreement->rent_due_day - 1) . ' days')); ?>
                    </p>
                    <?php if ($agreement->late_fee > 0): ?>
                        <p><small><?php printf(__('Late fee of %s applies after %d days', 'housing-rent-mgmt'), 
                            HRM_Functions::format_currency($agreement->late_fee), 
                            $agreement->late_fee_after_days); ?></small></p>
                    <?php endif; ?>
                <?php else: ?>
                    <p style="color: #46b450;"><?php _e('All payments are up to date', 'housing-rent-mgmt'); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Payment History -->
        <div id="payment-history" class="hrm-dashboard-card" style="margin-top: 20px;">
            <h3><?php _e('Recent Payments', 'housing-rent-mgmt'); ?></h3>
            <?php if (empty($recent_payments)): ?>
                <p><?php _e('No payment history found.', 'housing-rent-mgmt'); ?></p>
            <?php else: ?>
                <table class="hrm-table">
                    <thead>
                        <tr>
                            <th><?php _e('Date', 'housing-rent-mgmt'); ?></th>
                            <th><?php _e('For Month', 'housing-rent-mgmt'); ?></th>
                            <th><?php _e('Amount', 'housing-rent-mgmt'); ?></th>
                            <th><?php _e('Method', 'housing-rent-mgmt'); ?></th>
                            <th><?php _e('Status', 'housing-rent-mgmt'); ?></th>
                            <th><?php _e('Receipt', 'housing-rent-mgmt'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_payments as $payment): ?>
                            <tr>
                                <td><?php echo date_i18n(get_option('date_format'), strtotime($payment->payment_date)); ?></td>
                                <td><?php echo date_i18n('F Y', strtotime($payment->for_month)); ?></td>
                                <td><strong><?php echo HRM_Functions::format_currency($payment->amount); ?></strong></td>
                                <td><?php echo ucfirst(str_replace('_', ' ', $payment->payment_method)); ?></td>
                                <td><span class="hrm-status hrm-status-<?php echo $payment->status; ?>"><?php echo ucfirst($payment->status); ?></span></td>
                                <td>
                                    <?php if ($payment->receipt_number): ?>
                                        <a href="#" class="hrm-view-receipt" data-id="<?php echo $payment->id; ?>"><?php _e('View', 'housing-rent-mgmt'); ?></a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if (count($recent_payments) >= 5): ?>
                    <p style="text-align: right; margin-top: 10px;">
                        <a href="#" class="hrm-view-all-payments"><?php _e('View All Payments →', 'housing-rent-mgmt'); ?></a>
                    </p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        
        <!-- Maintenance Requests -->
        <div class="hrm-dashboard-card" style="margin-top: 20px;">
            <h3><?php _e('Maintenance Requests', 'housing-rent-mgmt'); ?></h3>
            <?php if (empty($maintenance_requests)): ?>
                <p><?php _e('No maintenance requests found.', 'housing-rent-mgmt'); ?></p>
            <?php else: ?>
                <table class="hrm-table">
                    <thead>
                        <tr>
                            <th><?php _e('Date', 'housing-rent-mgmt'); ?></th>
                            <th><?php _e('Issue Type', 'housing-rent-mgmt'); ?></th>
                            <th><?php _e('Description', 'housing-rent-mgmt'); ?></th>
                            <th><?php _e('Priority', 'housing-rent-mgmt'); ?></th>
                            <th><?php _e('Status', 'housing-rent-mgmt'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($maintenance_requests as $request): ?>
                            <tr>
                                <td><?php echo date_i18n(get_option('date_format'), strtotime($request->request_date)); ?></td>
                                <td><?php echo ucfirst($request->issue_type); ?></td>
                                <td><?php echo wp_trim_words($request->description, 10); ?></td>
                                <td><span class="hrm-status hrm-status-<?php echo $request->priority; ?>"><?php echo ucfirst($request->priority); ?></span></td>
                                <td><span class="hrm-status hrm-status-<?php echo $request->status; ?>"><?php echo ucfirst($request->status); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <!-- Tenant Information -->
        <div class="hrm-dashboard-card" style="margin-top: 20px;">
            <h3><?php _e('Your Information', 'housing-rent-mgmt'); ?></h3>
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                <div>
                    <p><strong><?php _e('Full Name:', 'housing-rent-mgmt'); ?></strong> <?php echo esc_html($tenant->first_name . ' ' . $tenant->last_name); ?></p>
                    <p><strong><?php _e('Email:', 'housing-rent-mgmt'); ?></strong> <?php echo esc_html($tenant->email); ?></p>
                    <p><strong><?php _e('Phone:', 'housing-rent-mgmt'); ?></strong> <?php echo esc_html($tenant->phone); ?></p>
                    <?php if ($tenant->alternate_phone): ?>
                        <p><strong><?php _e('Alt. Phone:', 'housing-rent-mgmt'); ?></strong> <?php echo esc_html($tenant->alternate_phone); ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <?php if ($tenant->emergency_contact_name): ?>
                        <p><strong><?php _e('Emergency Contact:', 'housing-rent-mgmt'); ?></strong> <?php echo esc_html($tenant->emergency_contact_name); ?></p>
                        <p><strong><?php _e('Emergency Phone:', 'housing-rent-mgmt'); ?></strong> <?php echo esc_html($tenant->emergency_contact_phone); ?></p>
                    <?php endif; ?>
                    <?php if ($tenant->employer_name): ?>
                        <p><strong><?php _e('Employer:', 'housing-rent-mgmt'); ?></strong> <?php echo esc_html($tenant->employer_name); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>