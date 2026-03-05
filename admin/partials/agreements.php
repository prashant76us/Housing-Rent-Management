<?php
global $wpdb;
$agreements_table = HRM_Functions::get_table_name('rent_agreements');

// Handle agreement termination
if (isset($_POST['action']) && $_POST['action'] === 'terminate_agreement' && isset($_POST['agreement_id'])) {
    if (!wp_verify_nonce($_POST['_wpnonce'], 'terminate_agreement_' . $_POST['agreement_id'])) {
        wp_die('Security check failed');
    }
    
    $wpdb->update(
        $agreements_table,
        [
            'status' => 'terminated',
            'termination_date' => current_time('Y-m-d'),
            'termination_reason' => sanitize_textarea_field($_POST['termination_reason'])
        ],
        ['id' => intval($_POST['agreement_id'])]
    );
    
    echo '<div class="notice notice-success"><p>' . __('Agreement terminated successfully.', 'housing-rent-mgmt') . '</p></div>';
}

// Get all agreements with details
$agreements = $wpdb->get_results("
    SELECT ra.*, 
           p.property_name, p.address_line1, p.city,
           CONCAT(t.first_name, ' ', t.last_name) as tenant_name,
           CONCAT(o.first_name, ' ', o.last_name) as owner_name
    FROM $agreements_table ra
    JOIN " . HRM_Functions::get_table_name('properties') . " p ON ra.property_id = p.id
    JOIN " . HRM_Functions::get_table_name('tenants') . " t ON ra.tenant_id = t.id
    JOIN " . HRM_Functions::get_table_name('property_owners') . " o ON ra.owner_id = o.id
    ORDER BY ra.created_at DESC
");
?>

<div class="wrap">
    <h1><?php _e('Rent Agreements', 'housing-rent-mgmt'); ?>
        <button class="hrm-button hrm-open-modal" data-modal="hrm-add-agreement-modal" style="float: right;"><?php _e('New Agreement', 'housing-rent-mgmt'); ?></button>
    </h1>
    
    <div class="hrm-notices"></div>
    
    <!-- Search Box -->
    <div class="hrm-search-box">
        <input type="text" class="hrm-search-input" data-table="agreements" placeholder="<?php _e('Search agreements...', 'housing-rent-mgmt'); ?>">
        <select id="hrm-filter-status">
            <option value=""><?php _e('All Status', 'housing-rent-mgmt'); ?></option>
            <option value="active"><?php _e('Active', 'housing-rent-mgmt'); ?></option>
            <option value="expired"><?php _e('Expired', 'housing-rent-mgmt'); ?></option>
            <option value="terminated"><?php _e('Terminated', 'housing-rent-mgmt'); ?></option>
        </select>
    </div>
    
    <!-- Agreements Table -->
    <table class="hrm-table">
        <thead>
            <tr>
                <th><?php _e('Agreement #', 'housing-rent-mgmt'); ?></th>
                <th><?php _e('Property', 'housing-rent-mgmt'); ?></th>
                <th><?php _e('Tenant', 'housing-rent-mgmt'); ?></th>
                <th><?php _e('Owner', 'housing-rent-mgmt'); ?></th>
                <th><?php _e('Period', 'housing-rent-mgmt'); ?></th>
                <th><?php _e('Monthly Rent', 'housing-rent-mgmt'); ?></th>
                <th><?php _e('Deposit', 'housing-rent-mgmt'); ?></th>
                <th><?php _e('Status', 'housing-rent-mgmt'); ?></th>
                <th><?php _e('Actions', 'housing-rent-mgmt'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($agreements)): ?>
                <tr>
                    <td colspan="9" style="text-align: center;"><?php _e('No agreements found.', 'housing-rent-mgmt'); ?></td>
                </tr>
            <?php else: ?>
                <?php foreach ($agreements as $agreement): ?>
                    <tr>
                        <td><strong><?php echo esc_html($agreement->agreement_number); ?></strong></td>
                        <td>
                            <?php echo esc_html($agreement->property_name); ?><br>
                            <small><?php echo esc_html($agreement->address_line1 . ', ' . $agreement->city); ?></small>
                        </td>
                        <td><?php echo esc_html($agreement->tenant_name); ?></td>
                        <td><?php echo esc_html($agreement->owner_name); ?></td>
                        <td>
                            <?php echo date_i18n(get_option('date_format'), strtotime($agreement->start_date)); ?><br>
                            <small><?php _e('to', 'housing-rent-mgmt'); ?> <?php echo date_i18n(get_option('date_format'), strtotime($agreement->end_date)); ?></small>
                        </td>
                        <td><?php echo HRM_Functions::format_currency($agreement->monthly_rent); ?></td>
                        <td><?php echo HRM_Functions::format_currency($agreement->security_deposit); ?></td>
                        <td><span class="hrm-status hrm-status-<?php echo $agreement->status; ?>"><?php echo ucfirst($agreement->status); ?></span></td>
                        <td>
                            <button class="hrm-button hrm-button-secondary hrm-view-agreement" data-id="<?php echo $agreement->id; ?>"><?php _e('View', 'housing-rent-mgmt'); ?></button>
                            <?php if ($agreement->status === 'active'): ?>
                                <button class="hrm-button hrm-button-secondary hrm-terminate-agreement" data-id="<?php echo $agreement->id; ?>"><?php _e('Terminate', 'housing-rent-mgmt'); ?></button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Add Agreement Modal -->
<div id="hrm-add-agreement-modal" class="hrm-modal">
    <div class="hrm-modal-content" style="max-width: 800px;">
        <span class="hrm-modal-close">&times;</span>
        <h2><?php _e('Create New Rent Agreement', 'housing-rent-mgmt'); ?></h2>
        
        <form id="hrm-add-agreement-form" class="hrm-form">
            <div class="hrm-form-row">
                <label for="property_id"><?php _e('Property *', 'housing-rent-mgmt'); ?></label>
                <select name="property_id" id="property_id" required>
                    <option value=""><?php _e('Select Property', 'housing-rent-mgmt'); ?></option>
                    <?php
                    $properties = HRM_Functions::get_properties(['status' => 'available']);
                    foreach ($properties as $property) {
                        echo '<option value="' . $property->id . '">' . esc_html($property->property_name . ' - ' . $property->address_line1 . ', ' . $property->city) . '</option>';
                    }
                    ?>
                </select>
            </div>
            
            <div class="hrm-form-row">
                <label for="tenant_id"><?php _e('Tenant *', 'housing-rent-mgmt'); ?></label>
                <select name="tenant_id" id="tenant_id" required>
                    <option value=""><?php _e('Select Tenant', 'housing-rent-mgmt'); ?></option>
                    <?php
                    $tenants = $wpdb->get_results("SELECT id, first_name, last_name, email FROM " . HRM_Functions::get_table_name('tenants') . " WHERE status = 'active'");
                    foreach ($tenants as $tenant) {
                        echo '<option value="' . $tenant->id . '">' . esc_html($tenant->first_name . ' ' . $tenant->last_name . ' - ' . $tenant->email) . '</option>';
                    }
                    ?>
                </select>
            </div>
            
            <div class="hrm-form-row">
                <label for="owner_id"><?php _e('Owner *', 'housing-rent-mgmt'); ?></label>
                <select name="owner_id" id="owner_id" required>
                    <option value=""><?php _e('Select Owner', 'housing-rent-mgmt'); ?></option>
                    <?php
                    $owners = $wpdb->get_results("SELECT id, first_name, last_name FROM " . HRM_Functions::get_table_name('property_owners'));
                    foreach ($owners as $owner) {
                        echo '<option value="' . $owner->id . '">' . esc_html($owner->first_name . ' ' . $owner->last_name) . '</option>';
                    }
                    ?>
                </select>
            </div>
            
            <div class="hrm-form-row">
                <label for="agreement_type"><?php _e('Agreement Type', 'housing-rent-mgmt'); ?></label>
                <select name="agreement_type" id="agreement_type">
                    <option value="residential"><?php _e('Residential', 'housing-rent-mgmt'); ?></option>
                    <option value="commercial"><?php _e('Commercial', 'housing-rent-mgmt'); ?></option>
                    <option value="short_term"><?php _e('Short Term', 'housing-rent-mgmt'); ?></option>
                </select>
            </div>
            
            <div class="hrm-form-row">
                <label for="start_date"><?php _e('Start Date *', 'housing-rent-mgmt'); ?></label>
                <input type="date" name="start_date" id="start_date" class="hrm-datepicker" required>
            </div>
            
            <div class="hrm-form-row">
                <label for="end_date"><?php _e('End Date *', 'housing-rent-mgmt'); ?></label>
                <input type="date" name="end_date" id="end_date" class="hrm-datepicker" required>
            </div>
            
            <div class="hrm-form-row">
                <label for="monthly_rent"><?php _e('Monthly Rent *', 'housing-rent-mgmt'); ?></label>
                <input type="number" name="monthly_rent" id="monthly_rent" step="0.01" min="0" required>
            </div>
            
            <div class="hrm-form-row">
                <label for="security_deposit"><?php _e('Security Deposit', 'housing-rent-mgmt'); ?></label>
                <input type="number" name="security_deposit" id="security_deposit" step="0.01" min="0">
            </div>
            
            <div class="hrm-form-row">
                <label for="rent_due_day"><?php _e('Rent Due Day', 'housing-rent-mgmt'); ?></label>
                <input type="number" name="rent_due_day" id="rent_due_day" min="1" max="31" value="1">
            </div>
            
            <div class="hrm-form-row">
                <label for="late_fee"><?php _e('Late Fee', 'housing-rent-mgmt'); ?></label>
                <input type="number" name="late_fee" id="late_fee" step="0.01" min="0" value="0">
            </div>
            
            <div class="hrm-form-row">
                <label for="late_fee_after_days"><?php _e('Late Fee After (days)', 'housing-rent-mgmt'); ?></label>
                <input type="number" name="late_fee_after_days" id="late_fee_after_days" min="0" value="5">
            </div>
            
            <div class="hrm-form-row">
                <label for="notice_period_days"><?php _e('Notice Period (days)', 'housing-rent-mgmt'); ?></label>
                <input type="number" name="notice_period_days" id="notice_period_days" min="0" value="30">
            </div>
            
            <div class="hrm-form-row">
                <label for="maintenance_responsibility"><?php _e('Maintenance Responsibility', 'housing-rent-mgmt'); ?></label>
                <textarea name="maintenance_responsibility" id="maintenance_responsibility" rows="3"></textarea>
            </div>
            
            <div class="hrm-form-row">
                <label for="utilities_included"><?php _e('Utilities Included', 'housing-rent-mgmt'); ?></label>
                <textarea name="utilities_included" id="utilities_included" rows="2" placeholder="<?php _e('e.g., Water, Electricity, Gas', 'housing-rent-mgmt'); ?>"></textarea>
            </div>
            
            <div class="hrm-form-row">
                <label for="special_clauses"><?php _e('Special Clauses', 'housing-rent-mgmt'); ?></label>
                <textarea name="special_clauses" id="special_clauses" rows="3"></textarea>
            </div>
            
            <div class="hrm-form-row">
                <label for="agreement_document"><?php _e('Agreement Document', 'housing-rent-mgmt'); ?></label>
                <input type="file" name="agreement_document" id="agreement_document" accept=".pdf,.doc,.docx">
            </div>
            
            <div class="hrm-form-row">
                <button type="submit" class="hrm-button"><?php _e('Create Agreement', 'housing-rent-mgmt'); ?></button>
            </div>
        </form>
    </div>
</div>

<!-- Termination Modal -->
<div id="hrm-terminate-agreement-modal" class="hrm-modal">
    <div class="hrm-modal-content">
        <span class="hrm-modal-close">&times;</span>
        <h2><?php _e('Terminate Agreement', 'housing-rent-mgmt'); ?></h2>
        
        <form method="post">
            <?php wp_nonce_field('terminate_agreement', '_wpnonce'); ?>
            <input type="hidden" name="action" value="terminate_agreement">
            <input type="hidden" name="agreement_id" id="terminate_agreement_id" value="">
            
            <div class="hrm-form-row">
                <label for="termination_reason"><?php _e('Termination Reason *', 'housing-rent-mgmt'); ?></label>
                <textarea name="termination_reason" id="termination_reason" rows="4" required></textarea>
            </div>
            
            <div class="hrm-form-row">
                <button type="submit" class="hrm-button" style="background: #dc3232;"><?php _e('Terminate Agreement', 'housing-rent-mgmt'); ?></button>
                <button type="button" class="hrm-button hrm-button-secondary hrm-modal-close"><?php _e('Cancel', 'housing-rent-mgmt'); ?></button>
            </div>
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('.hrm-terminate-agreement').on('click', function() {
        var agreementId = $(this).data('id');
        $('#terminate_agreement_id').val(agreementId);
        $('#hrm-terminate-agreement-modal').show();
    });
});
</script>