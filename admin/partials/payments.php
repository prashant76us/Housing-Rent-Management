<?php
global $wpdb;
$payments_table = HRM_Functions::get_table_name('rent_payments');

// Get filter parameters
$status_filter = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
$month_filter = isset($_GET['month']) ? sanitize_text_field($_GET['month']) : date('Y-m');

// Build query
$where_clauses = ['1=1'];

if ($status_filter) {
    $where_clauses[] = $wpdb->prepare("rp.status = %s", $status_filter);
}

if ($month_filter) {
    $where_clauses[] = $wpdb->prepare("DATE_FORMAT(rp.for_month, '%%Y-%%m') = %s", $month_filter);
}

$where_sql = implode(' AND ', $where_clauses);

// Get payments with details
$payments = $wpdb->get_results("
    SELECT rp.*, 
           CONCAT(t.first_name, ' ', t.last_name) as tenant_name,
           p.property_name,
           ra.agreement_number
    FROM $payments_table rp
    JOIN " . HRM_Functions::get_table_name('tenants') . " t ON rp.tenant_id = t.id
    JOIN " . HRM_Functions::get_table_name('properties') . " p ON rp.property_id = p.id
    LEFT JOIN " . HRM_Functions::get_table_name('rent_agreements') . " ra ON rp.agreement_id = ra.id
    WHERE $where_sql
    ORDER BY rp.payment_date DESC
");

// Calculate totals
$total_collected = 0;
$total_pending = 0;
foreach ($payments as $payment) {
    if ($payment->status === 'paid') {
        $total_collected += $payment->amount;
    } else {
        $total_pending += $payment->amount;
    }
}
?>

<div class="wrap">
    <h1><?php _e('Rent Payments', 'housing-rent-mgmt'); ?>
        <button class="hrm-button hrm-open-modal" data-modal="hrm-add-payment-modal" style="float: right;"><?php _e('Record Payment', 'housing-rent-mgmt'); ?></button>
    </h1>
    
    <div class="hrm-notices"></div>
    
    <!-- Summary Cards -->
    <div class="hrm-dashboard-widgets" style="margin-bottom: 20px;">
        <div class="hrm-widget">
            <h3><?php _e('Total Collected', 'housing-rent-mgmt'); ?></h3>
            <div class="hrm-widget-number"><?php echo HRM_Functions::format_currency($total_collected); ?></div>
            <div class="hrm-widget-label"><?php _e('For selected period', 'housing-rent-mgmt'); ?></div>
        </div>
        
        <div class="hrm-widget">
            <h3><?php _e('Pending Amount', 'housing-rent-mgmt'); ?></h3>
            <div class="hrm-widget-number"><?php echo HRM_Functions::format_currency($total_pending); ?></div>
            <div class="hrm-widget-label"><?php _e('Unpaid rent', 'housing-rent-mgmt'); ?></div>
        </div>
        
        <div class="hrm-widget">
            <h3><?php _e('Transactions', 'housing-rent-mgmt'); ?></h3>
            <div class="hrm-widget-number"><?php echo count($payments); ?></div>
            <div class="hrm-widget-label"><?php _e('Total transactions', 'housing-rent-mgmt'); ?></div>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="hrm-filters" style="margin-bottom: 20px; padding: 15px; background: #fff; border: 1px solid #ccd0d4;">
        <form method="get" action="">
            <input type="hidden" name="page" value="hrm-payments">
            
            <div style="display: flex; gap: 10px; align-items: center;">
                <select name="status">
                    <option value=""><?php _e('All Status', 'housing-rent-mgmt'); ?></option>
                    <option value="paid" <?php selected($status_filter, 'paid'); ?>><?php _e('Paid', 'housing-rent-mgmt'); ?></option>
                    <option value="pending" <?php selected($status_filter, 'pending'); ?>><?php _e('Pending', 'housing-rent-mgmt'); ?></option>
                    <option value="overdue" <?php selected($status_filter, 'overdue'); ?>><?php _e('Overdue', 'housing-rent-mgmt'); ?></option>
                </select>
                
                <input type="month" name="month" value="<?php echo $month_filter; ?>">
                
                <button type="submit" class="hrm-button"><?php _e('Filter', 'housing-rent-mgmt'); ?></button>
                
                <?php if ($status_filter || $month_filter !== date('Y-m')): ?>
                    <a href="<?php echo admin_url('admin.php?page=hrm-payments'); ?>" class="hrm-button hrm-button-secondary"><?php _e('Clear Filters', 'housing-rent-mgmt'); ?></a>
                <?php endif; ?>
            </div>
        </form>
    </div>
    
    <!-- Payments Table -->
    <table class="hrm-table">
        <thead>
            <tr>
                <th><?php _e('Receipt #', 'housing-rent-mgmt'); ?></th>
                <th><?php _e('Date', 'housing-rent-mgmt'); ?></th>
                <th><?php _e('Tenant', 'housing-rent-mgmt'); ?></th>
                <th><?php _e('Property', 'housing-rent-mgmt'); ?></th>
                <th><?php _e('Agreement', 'housing-rent-mgmt'); ?></th>
                <th><?php _e('For Month', 'housing-rent-mgmt'); ?></th>
                <th><?php _e('Amount', 'housing-rent-mgmt'); ?></th>
                <th><?php _e('Method', 'housing-rent-mgmt'); ?></th>
                <th><?php _e('Status', 'housing-rent-mgmt'); ?></th>
                <th><?php _e('Actions', 'housing-rent-mgmt'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($payments)): ?>
                <tr>
                    <td colspan="10" style="text-align: center;"><?php _e('No payments found.', 'housing-rent-mgmt'); ?></td>
                </tr>
            <?php else: ?>
                <?php foreach ($payments as $payment): ?>
                    <tr>
                        <td><strong><?php echo esc_html($payment->receipt_number); ?></strong></td>
                        <td><?php echo date_i18n(get_option('date_format'), strtotime($payment->payment_date)); ?></td>
                        <td><?php echo esc_html($payment->tenant_name); ?></td>
                        <td><?php echo esc_html($payment->property_name); ?></td>
                        <td><?php echo esc_html($payment->agreement_number); ?></td>
                        <td><?php echo date_i18n('F Y', strtotime($payment->for_month)); ?></td>
                        <td><?php echo HRM_Functions::format_currency($payment->amount); ?></td>
                        <td><?php echo ucfirst(str_replace('_', ' ', $payment->payment_method)); ?></td>
                        <td><span class="hrm-status hrm-status-<?php echo $payment->status; ?>"><?php echo ucfirst($payment->status); ?></span></td>
                        <td>
                            <button class="hrm-button hrm-button-secondary hrm-view-receipt" data-id="<?php echo $payment->id; ?>"><?php _e('View', 'housing-rent-mgmt'); ?></button>
                            <?php if ($payment->status === 'pending'): ?>
                                <button class="hrm-button hrm-button-secondary hrm-mark-paid" data-id="<?php echo $payment->id; ?>"><?php _e('Mark Paid', 'housing-rent-mgmt'); ?></button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Add Payment Modal -->
<div id="hrm-add-payment-modal" class="hrm-modal">
    <div class="hrm-modal-content">
        <span class="hrm-modal-close">&times;</span>
        <h2><?php _e('Record Rent Payment', 'housing-rent-mgmt'); ?></h2>
        
        <form id="hrm-add-payment-form" class="hrm-form">
            <div class="hrm-form-row">
                <label for="agreement_id"><?php _e('Agreement *', 'housing-rent-mgmt'); ?></label>
                <select name="agreement_id" id="agreement_id" required>
                    <option value=""><?php _e('Select Agreement', 'housing-rent-mgmt'); ?></option>
                    <?php
                    $agreements = HRM_Functions::get_active_agreements();
                    foreach ($agreements as $agreement) {
                        $property = HRM_Functions::get_property($agreement->property_id);
                        $tenant = HRM_Functions::get_tenant($agreement->tenant_id);
                        echo '<option value="' . $agreement->id . '" data-rent="' . $agreement->monthly_rent . '">';
                        echo $agreement->agreement_number . ' - ' . $tenant->first_name . ' ' . $tenant->last_name . ' - ' . $property->property_name;
                        echo '</option>';
                    }
                    ?>
                </select>
            </div>
            
            <div class="hrm-form-row">
                <label for="amount"><?php _e('Amount *', 'housing-rent-mgmt'); ?></label>
                <input type="number" name="amount" id="amount" step="0.01" min="0" required>
            </div>
            
            <div class="hrm-form-row">
                <label for="payment_date"><?php _e('Payment Date *', 'housing-rent-mgmt'); ?></label>
                <input type="date" name="payment_date" id="payment_date" class="hrm-datepicker" value="<?php echo current_time('Y-m-d'); ?>" required>
            </div>
            
            <div class="hrm-form-row">
                <label for="for_month"><?php _e('For Month *', 'housing-rent-mgmt'); ?></label>
                <input type="month" name="for_month" id="for_month" value="<?php echo current_time('Y-m'); ?>" required>
            </div>
            
            <div class="hrm-form-row">
                <label for="payment_method"><?php _e('Payment Method *', 'housing-rent-mgmt'); ?></label>
                <select name="payment_method" id="payment_method" required>
                    <option value=""><?php _e('Select Method', 'housing-rent-mgmt'); ?></option>
                    <option value="cash"><?php _e('Cash', 'housing-rent-mgmt'); ?></option>
                    <option value="check"><?php _e('Check', 'housing-rent-mgmt'); ?></option>
                    <option value="bank_transfer"><?php _e('Bank Transfer', 'housing-rent-mgmt'); ?></option>
                    <option value="credit_card"><?php _e('Credit Card', 'housing-rent-mgmt'); ?></option>
                    <option value="online"><?php _e('Online Payment', 'housing-rent-mgmt'); ?></option>
                </select>
            </div>
            
            <div class="hrm-form-row">
                <label for="transaction_id"><?php _e('Transaction ID / Check #', 'housing-rent-mgmt'); ?></label>
                <input type="text" name="transaction_id" id="transaction_id">
            </div>
            
            <div class="hrm-form-row">
                <label for="notes"><?php _e('Notes', 'housing-rent-mgmt'); ?></label>
                <textarea name="notes" id="notes" rows="3"></textarea>
            </div>
            
            <div class="hrm-form-row">
                <button type="submit" class="hrm-button"><?php _e('Record Payment', 'housing-rent-mgmt'); ?></button>
            </div>
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('#agreement_id').on('change', function() {
        var selected = $(this).find(':selected');
        var rent = selected.data('rent');
        if (rent) {
            $('#amount').val(rent);
        }
    });
});
</script>