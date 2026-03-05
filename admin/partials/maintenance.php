<?php
global $wpdb;
$maintenance_table = HRM_Functions::get_table_name('maintenance_requests');

// Handle status update
if (isset($_POST['action']) && $_POST['action'] === 'update_maintenance_status' && isset($_POST['request_id'])) {
    if (!wp_verify_nonce($_POST['_wpnonce'], 'update_maintenance_' . $_POST['request_id'])) {
        wp_die('Security check failed');
    }
    
    $wpdb->update(
        $maintenance_table,
        [
            'status' => sanitize_text_field($_POST['status']),
            'assigned_to' => sanitize_text_field($_POST['assigned_to']),
            'estimated_cost' => floatval($_POST['estimated_cost']),
            'actual_cost' => floatval($_POST['actual_cost']),
            'notes' => sanitize_textarea_field($_POST['notes']),
            'completion_date' => $_POST['status'] === 'completed' ? current_time('mysql') : null
        ],
        ['id' => intval($_POST['request_id'])]
    );
    
    echo '<div class="notice notice-success"><p>' . __('Maintenance request updated successfully.', 'housing-rent-mgmt') . '</p></div>';
}

// Get filter parameters
$status_filter = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
$priority_filter = isset($_GET['priority']) ? sanitize_text_field($_GET['priority']) : '';

// Build query
$where_clauses = ['1=1'];

if ($status_filter) {
    $where_clauses[] = $wpdb->prepare("mr.status = %s", $status_filter);
}

if ($priority_filter) {
    $where_clauses[] = $wpdb->prepare("mr.priority = %s", $priority_filter);
}

$where_sql = implode(' AND ', $where_clauses);

// Get maintenance requests with details
$requests = $wpdb->get_results("
    SELECT mr.*, 
           CONCAT(t.first_name, ' ', t.last_name) as tenant_name,
           t.phone as tenant_phone,
           p.property_name, p.address_line1, p.city
    FROM $maintenance_table mr
    JOIN " . HRM_Functions::get_table_name('tenants') . " t ON mr.tenant_id = t.id
    JOIN " . HRM_Functions::get_table_name('properties') . " p ON mr.property_id = p.id
    WHERE $where_sql
    ORDER BY 
        CASE mr.priority
            WHEN 'emergency' THEN 1
            WHEN 'high' THEN 2
            WHEN 'medium' THEN 3
            WHEN 'low' THEN 4
        END,
        mr.request_date DESC
");
?>

<div class="wrap">
    <h1><?php _e('Maintenance Requests', 'housing-rent-mgmt'); ?></h1>
    
    <div class="hrm-notices"></div>
    
    <!-- Filters -->
    <div class="hrm-filters" style="margin-bottom: 20px; padding: 15px; background: #fff; border: 1px solid #ccd0d4;">
        <form method="get" action="">
            <input type="hidden" name="page" value="hrm-maintenance">
            
            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <select name="status">
                    <option value=""><?php _e('All Status', 'housing-rent-mgmt'); ?></option>
                    <option value="pending" <?php selected($status_filter, 'pending'); ?>><?php _e('Pending', 'housing-rent-mgmt'); ?></option>
                    <option value="in_progress" <?php selected($status_filter, 'in_progress'); ?>><?php _e('In Progress', 'housing-rent-mgmt'); ?></option>
                    <option value="completed" <?php selected($status_filter, 'completed'); ?>><?php _e('Completed', 'housing-rent-mgmt'); ?></option>
                    <option value="cancelled" <?php selected($status_filter, 'cancelled'); ?>><?php _e('Cancelled', 'housing-rent-mgmt'); ?></option>
                </select>
                
                <select name="priority">
                    <option value=""><?php _e('All Priority', 'housing-rent-mgmt'); ?></option>
                    <option value="low" <?php selected($priority_filter, 'low'); ?>><?php _e('Low', 'housing-rent-mgmt'); ?></option>
                    <option value="medium" <?php selected($priority_filter, 'medium'); ?>><?php _e('Medium', 'housing-rent-mgmt'); ?></option>
                    <option value="high" <?php selected($priority_filter, 'high'); ?>><?php _e('High', 'housing-rent-mgmt'); ?></option>
                    <option value="emergency" <?php selected($priority_filter, 'emergency'); ?>><?php _e('Emergency', 'housing-rent-mgmt'); ?></option>
                </select>
                
                <button type="submit" class="hrm-button"><?php _e('Filter', 'housing-rent-mgmt'); ?></button>
                
                <?php if ($status_filter || $priority_filter): ?>
                    <a href="<?php echo admin_url('admin.php?page=hrm-maintenance'); ?>" class="hrm-button hrm-button-secondary"><?php _e('Clear Filters', 'housing-rent-mgmt'); ?></a>
                <?php endif; ?>
            </div>
        </form>
    </div>
    
    <!-- Summary Cards -->
    <div class="hrm-dashboard-widgets" style="margin-bottom: 20px;">
        <?php
        $counts = $wpdb->get_row("
            SELECT 
                SUM(CASE WHEN priority = 'emergency' AND status != 'completed' THEN 1 ELSE 0 END) as emergency,
                SUM(CASE WHEN priority = 'high' AND status != 'completed' THEN 1 ELSE 0 END) as high,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress
            FROM $maintenance_table
        ");
        ?>
        
        <div class="hrm-widget">
            <h3><?php _e('Emergency', 'housing-rent-mgmt'); ?></h3>
            <div class="hrm-widget-number" style="color: #dc3232;"><?php echo intval($counts->emergency); ?></div>
            <div class="hrm-widget-label"><?php _e('Urgent attention needed', 'housing-rent-mgmt'); ?></div>
        </div>
        
        <div class="hrm-widget">
            <h3><?php _e('High Priority', 'housing-rent-mgmt'); ?></h3>
            <div class="hrm-widget-number" style="color: #f56e28;"><?php echo intval($counts->high); ?></div>
            <div class="hrm-widget-label"><?php _e('Should be addressed soon', 'housing-rent-mgmt'); ?></div>
        </div>
        
        <div class="hrm-widget">
            <h3><?php _e('Pending', 'housing-rent-mgmt'); ?></h3>
            <div class="hrm-widget-number"><?php echo intval($counts->pending); ?></div>
            <div class="hrm-widget-label"><?php _e('Awaiting assignment', 'housing-rent-mgmt'); ?></div>
        </div>
        
        <div class="hrm-widget">
            <h3><?php _e('In Progress', 'housing-rent-mgmt'); ?></h3>
            <div class="hrm-widget-number"><?php echo intval($counts->in_progress); ?></div>
            <div class="hrm-widget-label"><?php _e('Currently being worked on', 'housing-rent-mgmt'); ?></div>
        </div>
    </div>
    
    <!-- Maintenance Requests Table -->
    <table class="hrm-table">
        <thead>
            <tr>
                <th><?php _e('ID', 'housing-rent-mgmt'); ?></th>
                <th><?php _e('Date', 'housing-rent-mgmt'); ?></th>
                <th><?php _e('Property', 'housing-rent-mgmt'); ?></th>
                <th><?php _e('Tenant', 'housing-rent-mgmt'); ?></th>
                <th><?php _e('Issue Type', 'housing-rent-mgmt'); ?></th>
                <th><?php _e('Priority', 'housing-rent-mgmt'); ?></th>
                <th><?php _e('Assigned To', 'housing-rent-mgmt'); ?></th>
                <th><?php _e('Cost', 'housing-rent-mgmt'); ?></th>
                <th><?php _e('Status', 'housing-rent-mgmt'); ?></th>
                <th><?php _e('Actions', 'housing-rent-mgmt'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($requests)): ?>
                <tr>
                    <td colspan="10" style="text-align: center;"><?php _e('No maintenance requests found.', 'housing-rent-mgmt'); ?></td>
                </tr>
            <?php else: ?>
                <?php foreach ($requests as $request): ?>
                    <tr>
                        <td>#<?php echo $request->id; ?></td>
                        <td><?php echo date_i18n(get_option('date_format'), strtotime($request->request_date)); ?></td>
                        <td>
                            <?php echo esc_html($request->property_name); ?><br>
                            <small><?php echo esc_html($request->city); ?></small>
                        </td>
                        <td>
                            <?php echo esc_html($request->tenant_name); ?><br>
                            <small><?php echo esc_html($request->tenant_phone); ?></small>
                        </td>
                        <td><?php echo ucfirst($request->issue_type); ?></td>
                        <td>
                            <span class="hrm-status hrm-status-<?php echo $request->priority; ?>">
                                <?php echo ucfirst($request->priority); ?>
                            </span>
                        </td>
                        <td><?php echo $request->assigned_to ?: '—'; ?></td>
                        <td>
                            <?php if ($request->estimated_cost): ?>
                                <small><?php _e('Est:', 'housing-rent-mgmt'); ?> <?php echo HRM_Functions::format_currency($request->estimated_cost); ?></small><br>
                            <?php endif; ?>
                            <?php if ($request->actual_cost): ?>
                                <strong><?php _e('Act:', 'housing-rent-mgmt'); ?> <?php echo HRM_Functions::format_currency($request->actual_cost); ?></strong>
                            <?php endif; ?>
                        </td>
                        <td><span class="hrm-status hrm-status-<?php echo $request->status; ?>"><?php echo ucfirst($request->status); ?></span></td>
                        <td>
                            <button class="hrm-button hrm-button-secondary hrm-update-maintenance" data-id="<?php echo $request->id; ?>"><?php _e('Update', 'housing-rent-mgmt'); ?></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Update Maintenance Modal -->
<div id="hrm-update-maintenance-modal" class="hrm-modal">
    <div class="hrm-modal-content">
        <span class="hrm-modal-close">&times;</span>
        <h2><?php _e('Update Maintenance Request', 'housing-rent-mgmt'); ?></h2>
        
        <form method="post">
            <?php wp_nonce_field('update_maintenance', '_wpnonce'); ?>
            <input type="hidden" name="action" value="update_maintenance_status">
            <input type="hidden" name="request_id" id="update_request_id" value="">
            
            <div class="hrm-form-row">
                <label for="status"><?php _e('Status', 'housing-rent-mgmt'); ?></label>
                <select name="status" id="update_status" required>
                    <option value="pending"><?php _e('Pending', 'housing-rent-mgmt'); ?></option>
                    <option value="in_progress"><?php _e('In Progress', 'housing-rent-mgmt'); ?></option>
                    <option value="completed"><?php _e('Completed', 'housing-rent-mgmt'); ?></option>
                    <option value="cancelled"><?php _e('Cancelled', 'housing-rent-mgmt'); ?></option>
                </select>
            </div>
            
            <div class="hrm-form-row">
                <label for="assigned_to"><?php _e('Assigned To', 'housing-rent-mgmt'); ?></label>
                <input type="text" name="assigned_to" id="update_assigned_to">
            </div>
            
            <div class="hrm-form-row">
                <label for="estimated_cost"><?php _e('Estimated Cost', 'housing-rent-mgmt'); ?></label>
                <input type="number" name="estimated_cost" id="update_estimated_cost" step="0.01" min="0">
            </div>
            
            <div class="hrm-form-row">
                <label for="actual_cost"><?php _e('Actual Cost', 'housing-rent-mgmt'); ?></label>
                <input type="number" name="actual_cost" id="update_actual_cost" step="0.01" min="0">
            </div>
            
            <div class="hrm-form-row">
                <label for="notes"><?php _e('Notes', 'housing-rent-mgmt'); ?></label>
                <textarea name="notes" id="update_notes" rows="4"></textarea>
            </div>
            
            <div class="hrm-form-row">
                <button type="submit" class="hrm-button"><?php _e('Update Request', 'housing-rent-mgmt'); ?></button>
                <button type="button" class="hrm-button hrm-button-secondary hrm-modal-close"><?php _e('Cancel', 'housing-rent-mgmt'); ?></button>
            </div>
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('.hrm-update-maintenance').on('click', function() {
        var requestId = $(this).data('id');
        
        // Load request data via AJAX
        $.ajax({
            url: hrm_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'hrm_get_maintenance_details',
                request_id: requestId,
                nonce: hrm_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    var data = response.data;
                    $('#update_request_id').val(requestId);
                    $('#update_status').val(data.status);
                    $('#update_assigned_to').val(data.assigned_to);
                    $('#update_estimated_cost').val(data.estimated_cost);
                    $('#update_actual_cost').val(data.actual_cost);
                    $('#update_notes').val(data.notes);
                    
                    $('#hrm-update-maintenance-modal').show();
                }
            }
        });
    });
});
</script>