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

// Get tenant's properties (through active agreements)
$properties = $wpdb->get_results($wpdb->prepare("
    SELECT p.* 
    FROM " . HRM_Functions::get_table_name('properties') . " p
    JOIN " . HRM_Functions::get_table_name('rent_agreements') . " ra ON p.id = ra.property_id
    WHERE ra.tenant_id = %d AND ra.status = 'active'
", $tenant->id));
?>

<div class="hrm-container">
    <div class="hrm-maintenance-form">
        <h2><?php _e('Submit Maintenance Request', 'housing-rent-mgmt'); ?></h2>
        
        <div class="hrm-alert-container"></div>
        
        <form id="hrm-maintenance-form" enctype="multipart/form-data">
            <div class="hrm-form-group">
                <label for="property_id"><?php _e('Select Property *', 'housing-rent-mgmt'); ?></label>
                <select name="property_id" id="property_id" required>
                    <option value=""><?php _e('Choose a property', 'housing-rent-mgmt'); ?></option>
                    <?php foreach ($properties as $property): ?>
                        <option value="<?php echo $property->id; ?>">
                            <?php echo esc_html($property->property_name . ' - ' . $property->address_line1); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="hrm-form-group">
                <label><?php _e('Issue Type *', 'housing-rent-mgmt'); ?></label>
                <div class="hrm-issue-types">
                    <div class="hrm-issue-type" data-type="plumbing">
                        <span class="dashicons dashicons-admin-plugins"></span>
                        <span><?php _e('Plumbing', 'housing-rent-mgmt'); ?></span>
                    </div>
                    <div class="hrm-issue-type" data-type="electrical">
                        <span class="dashicons dashicons-lightbulb"></span>
                        <span><?php _e('Electrical', 'housing-rent-mgmt'); ?></span>
                    </div>
                    <div class="hrm-issue-type" data-type="hvac">
                        <span class="dashicons dashicons-thermometer"></span>
                        <span><?php _e('HVAC', 'housing-rent-mgmt'); ?></span>
                    </div>
                    <div class="hrm-issue-type" data-type="appliance">
                        <span class="dashicons dashicons-admin-generic"></span>
                        <span><?php _e('Appliance', 'housing-rent-mgmt'); ?></span>
                    </div>
                    <div class="hrm-issue-type" data-type="structural">
                        <span class="dashicons dashicons-building"></span>
                        <span><?php _e('Structural', 'housing-rent-mgmt'); ?></span>
                    </div>
                    <div class="hrm-issue-type" data-type="pest">
                        <span class="dashicons dashicons-bug"></span>
                        <span><?php _e('Pest Control', 'housing-rent-mgmt'); ?></span>
                    </div>
                    <div class="hrm-issue-type" data-type="security">
                        <span class="dashicons dashicons-lock"></span>
                        <span><?php _e('Security', 'housing-rent-mgmt'); ?></span>
                    </div>
                    <div class="hrm-issue-type" data-type="other">
                        <span class="dashicons dashicons-admin-tools"></span>
                        <span><?php _e('Other', 'housing-rent-mgmt'); ?></span>
                    </div>
                </div>
                <input type="hidden" name="issue_type" id="issue_type" required>
            </div>
            
            <div class="hrm-form-group">
                <label for="priority"><?php _e('Priority *', 'housing-rent-mgmt'); ?></label>
                <select name="priority" id="priority" required>
                    <option value="low"><?php _e('Low - Can wait', 'housing-rent-mgmt'); ?></option>
                    <option value="medium" selected><?php _e('Medium - Needs attention soon', 'housing-rent-mgmt'); ?></option>
                    <option value="high"><?php _e('High - Urgent', 'housing-rent-mgmt'); ?></option>
                    <option value="emergency"><?php _e('Emergency - Immediate action required', 'housing-rent-mgmt'); ?></option>
                </select>
            </div>
            
            <div class="hrm-form-group">
                <label for="description"><?php _e('Description *', 'housing-rent-mgmt'); ?></label>
                <textarea name="description" id="description" rows="5" required 
                    placeholder="<?php _e('Please describe the issue in detail...', 'housing-rent-mgmt'); ?>"></textarea>
            </div>
            
            <div class="hrm-form-group">
                <label for="attachment"><?php _e('Attach Photos (Optional)', 'housing-rent-mgmt'); ?></label>
                <input type="file" name="attachment" id="attachment" accept="image/*">
                <p class="description"><?php _e('Max file size: 5MB. Supported formats: JPG, PNG', 'housing-rent-mgmt'); ?></p>
            </div>
            
            <div class="hrm-form-group">
                <label for="preferred_time"><?php _e('Preferred Access Time (Optional)', 'housing-rent-mgmt'); ?></label>
                <input type="text" name="preferred_time" id="preferred_time" 
                    placeholder="<?php _e('e.g., Weekdays after 5 PM', 'housing-rent-mgmt'); ?>">
            </div>
            
            <button type="submit" class="hrm-submit-btn"><?php _e('Submit Request', 'housing-rent-mgmt'); ?></button>
        </form>
        
        <div class="hrm-guidelines" style="margin-top: 30px; padding: 20px; background: #f5f5f5; border-radius: 5px;">
            <h3><?php _e('Maintenance Guidelines', 'housing-rent-mgmt'); ?></h3>
            <ul style="margin-left: 20px;">
                <li><?php _e('Emergency issues (gas leak, flooding, electrical hazard) will be prioritized immediately', 'housing-rent-mgmt'); ?></li>
                <li><?php _e('Please provide as much detail as possible about the issue', 'housing-rent-mgmt'); ?></li>
                <li><?php _e('Photos help us understand the issue better', 'housing-rent-mgmt'); ?></li>
                <li><?php _e('You will receive updates via email about your request status', 'housing-rent-mgmt'); ?></li>
                <li><?php _e('For emergency issues, also call our emergency line: (555) 123-4567', 'housing-rent-mgmt'); ?></li>
            </ul>
        </div>
        
        <p style="text-align: center; margin-top: 20px;">
            <a href="[hrm_tenant_dashboard]"><?php _e('← Back to Dashboard', 'housing-rent-mgmt'); ?></a>
        </p>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Issue type selection
    $('.hrm-issue-type').on('click', function() {
        $('.hrm-issue-type').removeClass('selected');
        $(this).addClass('selected');
        $('#issue_type').val($(this).data('type'));
    });
    
    // Form submission
    $('#hrm-maintenance-form').on('submit', function(e) {
        e.preventDefault();
        
        if (!$('#issue_type').val()) {
            showAlert('error', '<?php _e('Please select an issue type.', 'housing-rent-mgmt'); ?>');
            return;
        }
        
        var form = $(this);
        var formData = new FormData(form[0]);
        formData.append('action', 'hrm_submit_maintenance_request');
        formData.append('nonce', hrm_public.nonce);
        
        $.ajax({
            url: hrm_public.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                form.find('button[type="submit"]').prop('disabled', true).html('<span class="hrm-spinner"></span> <?php _e('Submitting...', 'housing-rent-mgmt'); ?>');
            },
            success: function(response) {
                if (response.success) {
                    showAlert('success', '<?php _e('Request submitted successfully!', 'housing-rent-mgmt'); ?>');
                    form[0].reset();
                    $('.hrm-issue-type').removeClass('selected');
                    
                    // Redirect to dashboard after 2 seconds
                    setTimeout(function() {
                        window.location.href = '[hrm_tenant_dashboard]';
                    }, 2000);
                } else {
                    showAlert('error', response.data);
                    form.find('button[type="submit"]').prop('disabled', false).text('<?php _e('Submit Request', 'housing-rent-mgmt'); ?>');
                }
            },
            error: function() {
                showAlert('error', '<?php _e('Request failed. Please try again.', 'housing-rent-mgmt'); ?>');
                form.find('button[type="submit"]').prop('disabled', false).text('<?php _e('Submit Request', 'housing-rent-mgmt'); ?>');
            }
        });
    });
    
    function showAlert(type, message) {
        var alertClass = type === 'success' ? 'hrm-alert-success' : 'hrm-alert-error';
        var alert = $('<div class="hrm-alert ' + alertClass + '">' + message + '</div>');
        
        $('.hrm-alert-container').html(alert);
        
        setTimeout(function() {
            alert.fadeOut();
        }, 5000);
    }
});
</script>