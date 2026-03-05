<?php
// Get dashboard statistics
$stats = HRM_Functions::get_dashboard_stats();

// Get recent payments
global $wpdb;
$recent_payments = $wpdb->get_results("
    SELECT rp.*, t.first_name, t.last_name, p.property_name 
    FROM " . HRM_Functions::get_table_name('rent_payments') . " rp
    JOIN " . HRM_Functions::get_table_name('tenants') . " t ON rp.tenant_id = t.id
    JOIN " . HRM_Functions::get_table_name('properties') . " p ON rp.property_id = p.id
    ORDER BY rp.created_at DESC 
    LIMIT 5
");

// Get recent maintenance requests
$recent_maintenance = $wpdb->get_results("
    SELECT mr.*, t.first_name, t.last_name, p.property_name 
    FROM " . HRM_Functions::get_table_name('maintenance_requests') . " mr
    JOIN " . HRM_Functions::get_table_name('tenants') . " t ON mr.tenant_id = t.id
    JOIN " . HRM_Functions::get_table_name('properties') . " p ON mr.property_id = p.id
    WHERE mr.status != 'completed'
    ORDER BY mr.request_date DESC 
    LIMIT 5
");
?>

<div class="wrap">
    <h1><?php _e('Housing Rent Dashboard', 'housing-rent-mgmt'); ?></h1>
    
    <div class="hrm-notices"></div>
    
    <!-- Statistics Widgets -->
    <div class="hrm-dashboard-widgets">
        <div class="hrm-widget">
            <h3><?php _e('Total Properties', 'housing-rent-mgmt'); ?></h3>
            <div class="hrm-widget-number" id="hrm-stat-total_properties"><?php echo $stats['total_properties']; ?></div>
            <div class="hrm-widget-label"><?php _e('Active rentals', 'housing-rent-mgmt'); ?></div>
        </div>
        
        <div class="hrm-widget">
            <h3><?php _e('Active Tenants', 'housing-rent-mgmt'); ?></h3>
            <div class="hrm-widget-number" id="hrm-stat-active_tenants"><?php echo $stats['active_tenants']; ?></div>
            <div class="hrm-widget-label"><?php _e('Currently renting', 'housing-rent-mgmt'); ?></div>
        </div>
        
        <div class="hrm-widget">
            <h3><?php _e('Active Agreements', 'housing-rent-mgmt'); ?></h3>
            <div class="hrm-widget-number" id="hrm-stat-active_agreements"><?php echo $stats['active_agreements']; ?></div>
            <div class="hrm-widget-label"><?php _e('Current leases', 'housing-rent-mgmt'); ?></div>
        </div>
        
        <div class="hrm-widget">
            <h3><?php _e('Total Owners', 'housing-rent-mgmt'); ?></h3>
            <div class="hrm-widget-number" id="hrm-stat-total_owners"><?php echo $stats['total_owners']; ?></div>
            <div class="hrm-widget-label"><?php _e('Property owners', 'housing-rent-mgmt'); ?></div>
        </div>
        
        <div class="hrm-widget">
            <h3><?php _e('Pending Maintenance', 'housing-rent-mgmt'); ?></h3>
            <div class="hrm-widget-number" id="hrm-stat-pending_maintenance"><?php echo $stats['pending_maintenance']; ?></div>
            <div class="hrm-widget-label"><?php _e('Requests to handle', 'housing-rent-mgmt'); ?></div>
        </div>
        
        <div class="hrm-widget">
            <h3><?php _e('Overdue Payments', 'housing-rent-mgmt'); ?></h3>
            <div class="hrm-widget-number" id="hrm-stat-overdue_payments"><?php echo $stats['overdue_payments']; ?></div>
            <div class="hrm-widget-label"><?php _e('Late payments', 'housing-rent-mgmt'); ?></div>
        </div>
        
        <div class="hrm-widget">
            <h3><?php _e('Monthly Rent', 'housing-rent-mgmt'); ?></h3>
            <div class="hrm-widget-number" id="hrm-stat-monthly_rent_collected"><?php echo HRM_Functions::format_currency($stats['monthly_rent_collected']); ?></div>
            <div class="hrm-widget-label"><?php _e('Collected this month', 'housing-rent-mgmt'); ?></div>
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div class="hrm-row" style="display: flex; gap: 20px; margin-top: 30px;">
        <!-- Recent Payments -->
        <div class="hrm-col" style="flex: 1;">
            <div class="hrm-widget">
                <h3><?php _e('Recent Payments', 'housing-rent-mgmt'); ?></h3>
                <?php if (empty($recent_payments)): ?>
                    <p><?php _e('No recent payments.', 'housing-rent-mgmt'); ?></p>
                <?php else: ?>
                    <table class="hrm-table">
                        <thead>
                            <tr>
                                <th><?php _e('Date', 'housing-rent-mgmt'); ?></th>
                                <th><?php _e('Tenant', 'housing-rent-mgmt'); ?></th>
                                <th><?php _e('Property', 'housing-rent-mgmt'); ?></th>
                                <th><?php _e('Amount', 'housing-rent-mgmt'); ?></th>
                                <th><?php _e('Status', 'housing-rent-mgmt'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_payments as $payment): ?>
                                <tr>
                                    <td><?php echo date_i18n(get_option('date_format'), strtotime($payment->payment_date)); ?></td>
                                    <td><?php echo $payment->first_name . ' ' . $payment->last_name; ?></td>
                                    <td><?php echo $payment->property_name; ?></td>
                                    <td><?php echo HRM_Functions::format_currency($payment->amount); ?></td>
                                    <td><span class="hrm-status hrm-status-<?php echo $payment->status; ?>"><?php echo ucfirst($payment->status); ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
                <p style="text-align: right; margin-top: 10px;">
                    <a href="<?php echo admin_url('admin.php?page=hrm-payments'); ?>" class="hrm-button hrm-button-secondary"><?php _e('View All Payments', 'housing-rent-mgmt'); ?></a>
                </p>
            </div>
        </div>
        
        <!-- Recent Maintenance -->
        <div class="hrm-col" style="flex: 1;">
            <div class="hrm-widget">
                <h3><?php _e('Pending Maintenance', 'housing-rent-mgmt'); ?></h3>
                <?php if (empty($recent_maintenance)): ?>
                    <p><?php _e('No pending maintenance requests.', 'housing-rent-mgmt'); ?></p>
                <?php else: ?>
                    <table class="hrm-table">
                        <thead>
                            <tr>
                                <th><?php _e('Date', 'housing-rent-mgmt'); ?></th>
                                <th><?php _e('Property', 'housing-rent-mgmt'); ?></th>
                                <th><?php _e('Issue', 'housing-rent-mgmt'); ?></th>
                                <th><?php _e('Priority', 'housing-rent-mgmt'); ?></th>
                                <th><?php _e('Status', 'housing-rent-mgmt'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_maintenance as $request): ?>
                                <tr>
                                    <td><?php echo date_i18n(get_option('date_format'), strtotime($request->request_date)); ?></td>
                                    <td><?php echo $request->property_name; ?></td>
                                    <td><?php echo $request->issue_type; ?></td>
                                    <td><span class="hrm-status hrm-status-<?php echo $request->priority; ?>"><?php echo ucfirst($request->priority); ?></span></td>
                                    <td><span class="hrm-status hrm-status-<?php echo $request->status; ?>"><?php echo ucfirst($request->status); ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
                <p style="text-align: right; margin-top: 10px;">
                    <a href="<?php echo admin_url('admin.php?page=hrm-maintenance'); ?>" class="hrm-button hrm-button-secondary"><?php _e('View All Maintenance', 'housing-rent-mgmt'); ?></a>
                </p>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="hrm-quick-actions" style="margin-top: 30px;">
        <h3><?php _e('Quick Actions', 'housing-rent-mgmt'); ?></h3>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <button class="hrm-button hrm-open-modal" data-modal="hrm-add-property-modal"><?php _e('Add Property', 'housing-rent-mgmt'); ?></button>
            <button class="hrm-button hrm-open-modal" data-modal="hrm-add-owner-modal"><?php _e('Add Owner', 'housing-rent-mgmt'); ?></button>
            <button class="hrm-button hrm-open-modal" data-modal="hrm-add-tenant-modal"><?php _e('Add Tenant', 'housing-rent-mgmt'); ?></button>
            <button class="hrm-button hrm-open-modal" data-modal="hrm-add-agreement-modal"><?php _e('New Agreement', 'housing-rent-mgmt'); ?></button>
            <button class="hrm-button hrm-open-modal" data-modal="hrm-add-payment-modal"><?php _e('Record Payment', 'housing-rent-mgmt'); ?></button>
        </div>
    </div>
</div>

<!-- Include modals -->
<?php include_once HRM_PLUGIN_DIR . 'admin/partials/modals/add-property.php'; ?>
<?php include_once HRM_PLUGIN_DIR . 'admin/partials/modals/add-owner.php'; ?>
<?php include_once HRM_PLUGIN_DIR . 'admin/partials/modals/add-tenant.php'; ?>
<?php include_once HRM_PLUGIN_DIR . 'admin/partials/modals/add-agreement.php'; ?>
<?php include_once HRM_PLUGIN_DIR . 'admin/partials/modals/add-payment.php'; ?>