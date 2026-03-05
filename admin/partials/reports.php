<?php
// Get date range from request or default to current month
$start_date = isset($_GET['start_date']) ? sanitize_text_field($_GET['start_date']) : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? sanitize_text_field($_GET['end_date']) : date('Y-m-t');
$report_type = isset($_GET['report_type']) ? sanitize_text_field($_GET['report_type']) : 'financial';

global $wpdb;

// Get financial summary
$payments_table = HRM_Functions::get_table_name('rent_payments');
$maintenance_table = HRM_Functions::get_table_name('monthly_maintenance');

// Total rent collected
$total_rent = $wpdb->get_var($wpdb->prepare(
    "SELECT SUM(amount) FROM $payments_table 
     WHERE status = 'paid' AND payment_date BETWEEN %s AND %s",
    $start_date,
    $end_date
));

// Total maintenance expenses
$total_maintenance = $wpdb->get_var($wpdb->prepare(
    "SELECT SUM(amount) FROM $maintenance_table 
     WHERE status = 'paid' AND payment_date BETWEEN %s AND %s",
    $start_date,
    $end_date
));

// Pending payments
$pending_payments = $wpdb->get_var($wpdb->prepare(
    "SELECT SUM(amount) FROM $payments_table 
     WHERE status = 'pending' AND due_date < %s",
    date('Y-m-d')
));

// Get payment by method
$payments_by_method = $wpdb->get_results($wpdb->prepare("
    SELECT payment_method, COUNT(*) as count, SUM(amount) as total
    FROM $payments_table
    WHERE payment_date BETWEEN %s AND %s AND status = 'paid'
    GROUP BY payment_method
", $start_date, $end_date));

// Get monthly trend
$monthly_trend = $wpdb->get_results("
    SELECT 
        DATE_FORMAT(payment_date, '%Y-%m') as month,
        COUNT(*) as payments_count,
        SUM(amount) as total_amount
    FROM $payments_table
    WHERE status = 'paid'
    GROUP BY DATE_FORMAT(payment_date, '%Y-%m')
    ORDER BY month DESC
    LIMIT 12
");

// Get top properties by revenue
$top_properties = $wpdb->get_results($wpdb->prepare("
    SELECT 
        p.id,
        p.property_name,
        p.address_line1,
        p.city,
        COUNT(rp.id) as payment_count,
        SUM(rp.amount) as total_revenue
    FROM $payments_table rp
    JOIN " . HRM_Functions::get_table_name('properties') . " p ON rp.property_id = p.id
    WHERE rp.status = 'paid' AND rp.payment_date BETWEEN %s AND %s
    GROUP BY p.id
    ORDER BY total_revenue DESC
    LIMIT 5
", $start_date, $end_date));

// Get occupancy rate
$properties_table = HRM_Functions::get_table_name('properties');
$total_properties = $wpdb->get_var("SELECT COUNT(*) FROM $properties_table");
$rented_properties = $wpdb->get_var("SELECT COUNT(*) FROM $properties_table WHERE status = 'rented'");
$occupancy_rate = $total_properties > 0 ? round(($rented_properties / $total_properties) * 100, 2) : 0;

// Get maintenance summary
$maintenance_by_type = $wpdb->get_results($wpdb->prepare("
    SELECT 
        maintenance_type,
        COUNT(*) as count,
        SUM(amount) as total_cost
    FROM $maintenance_table
    WHERE payment_date BETWEEN %s AND %s
    GROUP BY maintenance_type
", $start_date, $end_date));
?>

<div class="wrap hrm-wrap">
    <h1><?php _e('Reports', 'housing-rent-mgmt'); ?></h1>
    
    <!-- Report Filters -->
    <div class="hrm-filters">
        <form method="get" action="">
            <input type="hidden" name="page" value="hrm-reports">
            
            <div style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
                <div>
                    <label for="report_type"><?php _e('Report Type:', 'housing-rent-mgmt'); ?></label><br>
                    <select name="report_type" id="report_type">
                        <option value="financial" <?php selected($report_type, 'financial'); ?>><?php _e('Financial Report', 'housing-rent-mgmt'); ?></option>
                        <option value="occupancy" <?php selected($report_type, 'occupancy'); ?>><?php _e('Occupancy Report', 'housing-rent-mgmt'); ?></option>
                        <option value="maintenance" <?php selected($report_type, 'maintenance'); ?>><?php _e('Maintenance Report', 'housing-rent-mgmt'); ?></option>
                        <option value="tenant" <?php selected($report_type, 'tenant'); ?>><?php _e('Tenant Report', 'housing-rent-mgmt'); ?></option>
                    </select>
                </div>
                
                <div>
                    <label for="start_date"><?php _e('Start Date:', 'housing-rent-mgmt'); ?></label><br>
                    <input type="date" name="start_date" id="start_date" value="<?php echo $start_date; ?>" class="hrm-datepicker">
                </div>
                
                <div>
                    <label for="end_date"><?php _e('End Date:', 'housing-rent-mgmt'); ?></label><br>
                    <input type="date" name="end_date" id="end_date" value="<?php echo $end_date; ?>" class="hrm-datepicker">
                </div>
                
                <div>
                    <button type="submit" class="hrm-button"><?php _e('Generate Report', 'housing-rent-mgmt'); ?></button>
                    <button type="button" class="hrm-button hrm-button-secondary" onclick="window.print()"><?php _e('Print Report', 'housing-rent-mgmt'); ?></button>
                    <button type="button" class="hrm-button hrm-button-secondary" id="export-csv"><?php _e('Export CSV', 'housing-rent-mgmt'); ?></button>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Report Summary Cards -->
    <div class="hrm-dashboard-widgets">
        <div class="hrm-widget">
            <h3><?php _e('Total Revenue', 'housing-rent-mgmt'); ?></h3>
            <div class="hrm-widget-number"><?php echo HRM_Functions::format_currency($total_rent ?: 0); ?></div>
            <div class="hrm-widget-label"><?php printf(__('For period: %s to %s', 'housing-rent-mgmt'), date_i18n(get_option('date_format'), strtotime($start_date)), date_i18n(get_option('date_format'), strtotime($end_date))); ?></div>
        </div>
        
        <div class="hrm-widget">
            <h3><?php _e('Maintenance Costs', 'housing-rent-mgmt'); ?></h3>
            <div class="hrm-widget-number"><?php echo HRM_Functions::format_currency($total_maintenance ?: 0); ?></div>
            <div class="hrm-widget-label"><?php _e('Total maintenance expenses', 'housing-rent-mgmt'); ?></div>
        </div>
        
        <div class="hrm-widget">
            <h3><?php _e('Net Income', 'housing-rent-mgmt'); ?></h3>
            <div class="hrm-widget-number"><?php echo HRM_Functions::format_currency(($total_rent ?: 0) - ($total_maintenance ?: 0)); ?></div>
            <div class="hrm-widget-label"><?php _e('Revenue minus expenses', 'housing-rent-mgmt'); ?></div>
        </div>
        
        <div class="hrm-widget">
            <h3><?php _e('Pending Payments', 'housing-rent-mgmt'); ?></h3>
            <div class="hrm-widget-number"><?php echo HRM_Functions::format_currency($pending_payments ?: 0); ?></div>
            <div class="hrm-widget-label"><?php _e('Overdue and pending', 'housing-rent-mgmt'); ?></div>
        </div>
        
        <div class="hrm-widget">
            <h3><?php _e('Occupancy Rate', 'housing-rent-mgmt'); ?></h3>
            <div class="hrm-widget-number"><?php echo $occupancy_rate; ?>%</div>
            <div class="hrm-widget-label"><?php echo $rented_properties; ?>/<?php echo $total_properties; ?> <?php _e('properties rented', 'housing-rent-mgmt'); ?></div>
        </div>
    </div>
    
    <!-- Charts Section -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 30px 0;">
        <div class="hrm-card">
            <h3><?php _e('Monthly Revenue Trend', 'housing-rent-mgmt'); ?></h3>
            <div class="hrm-chart-container" id="monthly-trend-chart"></div>
        </div>
        
        <div class="hrm-card">
            <h3><?php _e('Payment Methods', 'housing-rent-mgmt'); ?></h3>
            <div class="hrm-chart-container" id="payment-methods-chart"></div>
        </div>
    </div>
    
    <!-- Payment Methods Table -->
    <div class="hrm-card">
        <h3><?php _e('Payment Methods Breakdown', 'housing-rent-mgmt'); ?></h3>
        <table class="hrm-table">
            <thead>
                <tr>
                    <th><?php _e('Payment Method', 'housing-rent-mgmt'); ?></th>
                    <th><?php _e('Number of Payments', 'housing-rent-mgmt'); ?></th>
                    <th><?php _e('Total Amount', 'housing-rent-mgmt'); ?></th>
                    <th><?php _e('Percentage', 'housing-rent-mgmt'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($payments_by_method)): ?>
                    <tr>
                        <td colspan="4" style="text-align: center;"><?php _e('No payment data available', 'housing-rent-mgmt'); ?></td>
                    </tr>
                <?php else: ?>
                    <?php 
                    $grand_total = array_sum(array_column($payments_by_method, 'total'));
                    foreach ($payments_by_method as $method): 
                        $percentage = $grand_total > 0 ? round(($method->total / $grand_total) * 100, 2) : 0;
                    ?>
                        <tr>
                            <td><strong><?php echo ucfirst(str_replace('_', ' ', $method->payment_method ?: 'Other')); ?></strong></td>
                            <td><?php echo $method->count; ?></td>
                            <td><?php echo HRM_Functions::format_currency($method->total); ?></td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <span><?php echo $percentage; ?>%</span>
                                    <div class="hrm-progress" style="flex: 1;">
                                        <div class="hrm-progress-bar" style="width: <?php echo $percentage; ?>%;"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Top Properties -->
    <div class="hrm-card">
        <h3><?php _e('Top Performing Properties', 'housing-rent-mgmt'); ?></h3>
        <table class="hrm-table">
            <thead>
                <tr>
                    <th><?php _e('Property', 'housing-rent-mgmt'); ?></th>
                    <th><?php _e('Location', 'housing-rent-mgmt'); ?></th>
                    <th><?php _e('Payments', 'housing-rent-mgmt'); ?></th>
                    <th><?php _e('Total Revenue', 'housing-rent-mgmt'); ?></th>
                    <th><?php _e('Average Payment', 'housing-rent-mgmt'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($top_properties)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center;"><?php _e('No property data available', 'housing-rent-mgmt'); ?></td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($top_properties as $property): ?>
                        <tr>
                            <td><strong><?php echo esc_html($property->property_name); ?></strong></td>
                            <td><?php echo esc_html($property->address_line1 . ', ' . $property->city); ?></td>
                            <td><?php echo $property->payment_count; ?></td>
                            <td><?php echo HRM_Functions::format_currency($property->total_revenue); ?></td>
                            <td><?php echo HRM_Functions::format_currency($property->payment_count > 0 ? $property->total_revenue / $property->payment_count : 0); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Maintenance Summary -->
    <div class="hrm-card">
        <h3><?php _e('Maintenance Summary', 'housing-rent-mgmt'); ?></h3>
        <table class="hrm-table">
            <thead>
                <tr>
                    <th><?php _e('Maintenance Type', 'housing-rent-mgmt'); ?></th>
                    <th><?php _e('Number of Requests', 'housing-rent-mgmt'); ?></th>
                    <th><?php _e('Total Cost', 'housing-rent-mgmt'); ?></th>
                    <th><?php _e('Average Cost', 'housing-rent-mgmt'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($maintenance_by_type)): ?>
                    <tr>
                        <td colspan="4" style="text-align: center;"><?php _e('No maintenance data available', 'housing-rent-mgmt'); ?></td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($maintenance_by_type as $maintenance): ?>
                        <tr>
                            <td><strong><?php echo ucfirst($maintenance->maintenance_type); ?></strong></td>
                            <td><?php echo $maintenance->count; ?></td>
                            <td><?php echo HRM_Functions::format_currency($maintenance->total_cost); ?></td>
                            <td><?php echo HRM_Functions::format_currency($maintenance->count > 0 ? $maintenance->total_cost / $maintenance->count : 0); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
jQuery(document).ready(function($) {
    // Monthly Trend Chart
    var monthlyData = <?php echo json_encode($monthly_trend); ?>;
    
    if (document.getElementById('monthly-trend-chart')) {
        new Chart(document.getElementById('monthly-trend-chart'), {
            type: 'line',
            data: {
                labels: monthlyData.map(d => d.month),
                datasets: [{
                    label: '<?php _e('Monthly Revenue', 'housing-rent-mgmt'); ?>',
                    data: monthlyData.map(d => d.total_amount),
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Payment Methods Chart
    var paymentMethods = <?php echo json_encode($payments_by_method); ?>;
    
    if (document.getElementById('payment-methods-chart') && paymentMethods.length > 0) {
        new Chart(document.getElementById('payment-methods-chart'), {
            type: 'doughnut',
            data: {
                labels: paymentMethods.map(m => m.payment_method ? m.payment_method.replace('_', ' ') : 'Other'),
                datasets: [{
                    data: paymentMethods.map(m => m.total),
                    backgroundColor: [
                        '#667eea',
                        '#764ba2',
                        '#f093fb',
                        '#f5576c',
                        '#4facfe'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.raw;
                                let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                let percentage = Math.round((value / total) * 100);
                                return label + ': ' + formatCurrency(value) + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Export to CSV
    $('#export-csv').on('click', function() {
        var csv = [];
        
        // Add headers
        csv.push('Report Type,Fiscal Period,Start Date,End Date');
        csv.push('<?php echo ucfirst($report_type); ?> Report,<?php echo date('Y-m-d', strtotime($start_date)); ?> to <?php echo date('Y-m-d', strtotime($end_date)); ?>,<?php echo $start_date; ?>,<?php echo $end_date; ?>');
        csv.push('');
        
        // Summary
        csv.push('Summary');
        csv.push('Metric,Value');
        csv.push('Total Revenue,<?php echo $total_rent ?: 0; ?>');
        csv.push('Maintenance Costs,<?php echo $total_maintenance ?: 0; ?>');
        csv.push('Net Income,<?php echo ($total_rent ?: 0) - ($total_maintenance ?: 0); ?>');
        csv.push('Pending Payments,<?php echo $pending_payments ?: 0; ?>');
        csv.push('Occupancy Rate,<?php echo $occupancy_rate; ?>%');
        csv.push('');
        
        // Payment Methods
        csv.push('Payment Methods Breakdown');
        csv.push('Payment Method,Count,Total Amount');
        <?php foreach ($payments_by_method as $method): ?>
        csv.push('<?php echo $method->payment_method; ?>,<?php echo $method->count; ?>,<?php echo $method->total; ?>');
        <?php endforeach; ?>
        csv.push('');
        
        // Top Properties
        csv.push('Top Properties');
        csv.push('Property,Location,Payments,Total Revenue');
        <?php foreach ($top_properties as $property): ?>
        csv.push('<?php echo $property->property_name; ?>,<?php echo $property->address_line1 . ' ' . $property->city; ?>,<?php echo $property->payment_count; ?>,<?php echo $property->total_revenue; ?>');
        <?php endforeach; ?>
        
        // Download CSV
        var blob = new Blob([csv.join('\n')], { type: 'text/csv' });
        var url = window.URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = 'hrm-report-<?php echo date('Y-m-d'); ?>.csv';
        a.click();
    });
    
    function formatCurrency(value) {
        return '$' + parseFloat(value).toFixed(2);
    }
});
</script>

<style>
@media print {
    .hrm-filters,
    .hrm-button,
    #wpadminbar,
    #adminmenumain,
    #wpfooter {
        display: none !important;
    }
    
    #wpcontent {
        margin-left: 0 !important;
        padding-left: 20px !important;
    }
    
    .hrm-widget {
        break-inside: avoid;
        background: #f8f9fa !important;
        color: #333 !important;
        border: 1px solid #ddd;
    }
    
    .hrm-card {
        break-inside: avoid;
        page-break-inside: avoid;
    }
}
</style>