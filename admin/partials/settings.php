<?php
// Save settings
if (isset($_POST['hrm_save_settings']) && wp_verify_nonce($_POST['_wpnonce'], 'hrm_save_settings')) {
    update_option('hrm_currency', sanitize_text_field($_POST['hrm_currency']));
    update_option('hrm_date_format', sanitize_text_field($_POST['hrm_date_format']));
    update_option('hrm_notify_admin', sanitize_text_field($_POST['hrm_notify_admin']));
    update_option('hrm_notify_tenant', sanitize_text_field($_POST['hrm_notify_tenant']));
    update_option('hrm_admin_email', sanitize_email($_POST['hrm_admin_email']));
    update_option('hrm_company_name', sanitize_text_field($_POST['hrm_company_name']));
    update_option('hrm_company_address', sanitize_textarea_field($_POST['hrm_company_address']));
    update_option('hrm_company_phone', sanitize_text_field($_POST['hrm_company_phone']));
    update_option('hrm_company_email', sanitize_email($_POST['hrm_company_email']));
    update_option('hrm_company_tax_id', sanitize_text_field($_POST['hrm_company_tax_id']));
    update_option('hrm_auto_reminders', isset($_POST['hrm_auto_reminders']) ? 'yes' : 'no');
    update_option('hrm_reminder_days', intval($_POST['hrm_reminder_days']));
    update_option('hrm_late_fee_enabled', isset($_POST['hrm_late_fee_enabled']) ? 'yes' : 'no');
    update_option('hrm_default_late_fee', floatval($_POST['hrm_default_late_fee']));
    update_option('hrm_late_fee_grace_period', intval($_POST['hrm_late_fee_grace_period']));
    update_option('hrm_keep_data_on_uninstall', isset($_POST['hrm_keep_data_on_uninstall']) ? 'yes' : 'no');
    
    echo '<div class="notice notice-success"><p>' . __('Settings saved successfully.', 'housing-rent-mgmt') . '</p></div>';
}

// Get current settings
$currency = get_option('hrm_currency', 'USD');
$date_format = get_option('hrm_date_format', 'F j, Y');
$notify_admin = get_option('hrm_notify_admin', 'yes');
$notify_tenant = get_option('hrm_notify_tenant', 'yes');
$admin_email = get_option('hrm_admin_email', get_option('admin_email'));
$company_name = get_option('hrm_company_name', get_bloginfo('name'));
$company_address = get_option('hrm_company_address', '');
$company_phone = get_option('hrm_company_phone', '');
$company_email = get_option('hrm_company_email', get_option('admin_email'));
$company_tax_id = get_option('hrm_company_tax_id', '');
$auto_reminders = get_option('hrm_auto_reminders', 'yes');
$reminder_days = get_option('hrm_reminder_days', 5);
$late_fee_enabled = get_option('hrm_late_fee_enabled', 'no');
$default_late_fee = get_option('hrm_default_late_fee', 50);
$late_fee_grace_period = get_option('hrm_late_fee_grace_period', 5);
$keep_data = get_option('hrm_keep_data_on_uninstall', 'no');
?>

<div class="wrap hrm-wrap">
    <h1><?php _e('Settings', 'housing-rent-mgmt'); ?></h1>
    
    <div class="hrm-notices"></div>
    
    <form method="post" action="" class="hrm-form">
        <?php wp_nonce_field('hrm_save_settings'); ?>
        
        <div class="hrm-tabs">
            <div class="hrm-tab-buttons">
                <button type="button" class="hrm-tab-button active" data-tab="general"><?php _e('General', 'housing-rent-mgmt'); ?></button>
                <button type="button" class="hrm-tab-button" data-tab="company"><?php _e('Company Info', 'housing-rent-mgmt'); ?></button>
                <button type="button" class="hrm-tab-button" data-tab="payments"><?php _e('Payments', 'housing-rent-mgmt'); ?></button>
                <button type="button" class="hrm-tab-button" data-tab="notifications"><?php _e('Notifications', 'housing-rent-mgmt'); ?></button>
                <button type="button" class="hrm-tab-button" data-tab="advanced"><?php _e('Advanced', 'housing-rent-mgmt'); ?></button>
            </div>
            
            <!-- General Settings -->
            <div id="general" class="hrm-tab-pane active">
                <h3><?php _e('General Settings', 'housing-rent-mgmt'); ?></h3>
                
                <div class="hrm-form-row">
                    <label for="hrm_currency"><?php _e('Currency', 'housing-rent-mgmt'); ?></label>
                    <select name="hrm_currency" id="hrm_currency">
                        <option value="USD" <?php selected($currency, 'USD'); ?>>USD ($)</option>
                        <option value="EUR" <?php selected($currency, 'EUR'); ?>>EUR (€)</option>
                        <option value="GBP" <?php selected($currency, 'GBP'); ?>>GBP (£)</option>
                        <option value="INR" <?php selected($currency, 'INR'); ?>>INR (₹)</option>
                        <option value="CAD" <?php selected($currency, 'CAD'); ?>>CAD ($)</option>
                        <option value="AUD" <?php selected($currency, 'AUD'); ?>>AUD ($)</option>
                    </select>
                    <p class="description"><?php _e('Select the currency for all financial transactions.', 'housing-rent-mgmt'); ?></p>
                </div>
                
                <div class="hrm-form-row">
                    <label for="hrm_date_format"><?php _e('Date Format', 'housing-rent-mgmt'); ?></label>
                    <select name="hrm_date_format" id="hrm_date_format">
                        <option value="F j, Y" <?php selected($date_format, 'F j, Y'); ?>>January 1, 2024</option>
                        <option value="Y-m-d" <?php selected($date_format, 'Y-m-d'); ?>>2024-01-01</option>
                        <option value="m/d/Y" <?php selected($date_format, 'm/d/Y'); ?>>01/01/2024</option>
                        <option value="d/m/Y" <?php selected($date_format, 'd/m/Y'); ?>>01/01/2024</option>
                        <option value="d.m.Y" <?php selected($date_format, 'd.m.Y'); ?>>01.01.2024</option>
                    </select>
                    <p class="description"><?php _e('Select the date format for displaying dates.', 'housing-rent-mgmt'); ?></p>
                </div>
            </div>
            
            <!-- Company Information -->
            <div id="company" class="hrm-tab-pane">
                <h3><?php _e('Company Information', 'housing-rent-mgmt'); ?></h3>
                <p><?php _e('This information will appear on receipts and documents.', 'housing-rent-mgmt'); ?></p>
                
                <div class="hrm-form-row">
                    <label for="hrm_company_name"><?php _e('Company Name', 'housing-rent-mgmt'); ?></label>
                    <input type="text" name="hrm_company_name" id="hrm_company_name" value="<?php echo esc_attr($company_name); ?>">
                </div>
                
                <div class="hrm-form-row">
                    <label for="hrm_company_address"><?php _e('Company Address', 'housing-rent-mgmt'); ?></label>
                    <textarea name="hrm_company_address" id="hrm_company_address" rows="4"><?php echo esc_textarea($company_address); ?></textarea>
                </div>
                
                <div class="hrm-form-row">
                    <label for="hrm_company_phone"><?php _e('Company Phone', 'housing-rent-mgmt'); ?></label>
                    <input type="text" name="hrm_company_phone" id="hrm_company_phone" value="<?php echo esc_attr($company_phone); ?>">
                </div>
                
                <div class="hrm-form-row">
                    <label for="hrm_company_email"><?php _e('Company Email', 'housing-rent-mgmt'); ?></label>
                    <input type="email" name="hrm_company_email" id="hrm_company_email" value="<?php echo esc_attr($company_email); ?>">
                </div>
                
                <div class="hrm-form-row">
                    <label for="hrm_company_tax_id"><?php _e('Tax ID / EIN', 'housing-rent-mgmt'); ?></label>
                    <input type="text" name="hrm_company_tax_id" id="hrm_company_tax_id" value="<?php echo esc_attr($company_tax_id); ?>">
                    <p class="description"><?php _e('Your company tax identification number.', 'housing-rent-mgmt'); ?></p>
                </div>
            </div>
            
            <!-- Payment Settings -->
            <div id="payments" class="hrm-tab-pane">
                <h3><?php _e('Payment Settings', 'housing-rent-mgmt'); ?></h3>
                
                <div class="hrm-form-row">
                    <label for="hrm_late_fee_enabled">
                        <input type="checkbox" name="hrm_late_fee_enabled" id="hrm_late_fee_enabled" value="1" <?php checked($late_fee_enabled, 'yes'); ?>>
                        <?php _e('Enable Late Fees', 'housing-rent-mgmt'); ?>
                    </label>
                    <p class="description"><?php _e('Automatically apply late fees to overdue payments.', 'housing-rent-mgmt'); ?></p>
                </div>
                
                <div class="hrm-form-row">
                    <label for="hrm_default_late_fee"><?php _e('Default Late Fee Amount', 'housing-rent-mgmt'); ?></label>
                    <input type="number" name="hrm_default_late_fee" id="hrm_default_late_fee" value="<?php echo esc_attr($default_late_fee); ?>" step="0.01" min="0">
                </div>
                
                <div class="hrm-form-row">
                    <label for="hrm_late_fee_grace_period"><?php _e('Grace Period (days)', 'housing-rent-mgmt'); ?></label>
                    <input type="number" name="hrm_late_fee_grace_period" id="hrm_late_fee_grace_period" value="<?php echo esc_attr($late_fee_grace_period); ?>" min="0" max="30">
                    <p class="description"><?php _e('Number of days after due date before late fee applies.', 'housing-rent-mgmt'); ?></p>
                </div>
                
                <h3><?php _e('Payment Gateways', 'housing-rent-mgmt'); ?></h3>
                <p><?php _e('Configure payment gateways for online rent collection.', 'housing-rent-mgmt'); ?></p>
                
                <div class="hrm-form-row">
                    <label>
                        <input type="checkbox" name="hrm_paypal_enabled" id="hrm_paypal_enabled" value="1" <?php checked(get_option('hrm_paypal_enabled'), 'yes'); ?>>
                        <?php _e('Enable PayPal', 'housing-rent-mgmt'); ?>
                    </label>
                </div>
                
                <div class="hrm-form-row">
                    <label for="hrm_paypal_email"><?php _e('PayPal Email', 'housing-rent-mgmt'); ?></label>
                    <input type="email" name="hrm_paypal_email" id="hrm_paypal_email" value="<?php echo esc_attr(get_option('hrm_paypal_email')); ?>">
                </div>
                
                <div class="hrm-form-row">
                    <label>
                        <input type="checkbox" name="hrm_stripe_enabled" id="hrm_stripe_enabled" value="1" <?php checked(get_option('hrm_stripe_enabled'), 'yes'); ?>>
                        <?php _e('Enable Stripe', 'housing-rent-mgmt'); ?>
                    </label>
                </div>
                
                <div class="hrm-form-row">
                    <label for="hrm_stripe_publishable_key"><?php _e('Stripe Publishable Key', 'housing-rent-mgmt'); ?></label>
                    <input type="text" name="hrm_stripe_publishable_key" id="hrm_stripe_publishable_key" value="<?php echo esc_attr(get_option('hrm_stripe_publishable_key')); ?>">
                </div>
                
                <div class="hrm-form-row">
                    <label for="hrm_stripe_secret_key"><?php _e('Stripe Secret Key', 'housing-rent-mgmt'); ?></label>
                    <input type="password" name="hrm_stripe_secret_key" id="hrm_stripe_secret_key" value="<?php echo esc_attr(get_option('hrm_stripe_secret_key')); ?>">
                </div>
            </div>
            
            <!-- Notification Settings -->
            <div id="notifications" class="hrm-tab-pane">
                <h3><?php _e('Email Notifications', 'housing-rent-mgmt'); ?></h3>
                
                <div class="hrm-form-row">
                    <label for="hrm_admin_email"><?php _e('Admin Email', 'housing-rent-mgmt'); ?></label>
                    <input type="email" name="hrm_admin_email" id="hrm_admin_email" value="<?php echo esc_attr($admin_email); ?>">
                    <p class="description"><?php _e('Email address for admin notifications.', 'housing-rent-mgmt'); ?></p>
                </div>
                
                <div class="hrm-form-row">
                    <label>
                        <input type="checkbox" name="hrm_notify_admin" id="hrm_notify_admin" value="yes" <?php checked($notify_admin, 'yes'); ?>>
                        <?php _e('Notify admin on new payments', 'housing-rent-mgmt'); ?>
                    </label>
                </div>
                
                <div class="hrm-form-row">
                    <label>
                        <input type="checkbox" name="hrm_notify_admin_maintenance" id="hrm_notify_admin_maintenance" value="yes" <?php checked(get_option('hrm_notify_admin_maintenance'), 'yes'); ?>>
                        <?php _e('Notify admin on new maintenance requests', 'housing-rent-mgmt'); ?>
                    </label>
                </div>
                
                <div class="hrm-form-row">
                    <label>
                        <input type="checkbox" name="hrm_notify_tenant" id="hrm_notify_tenant" value="yes" <?php checked($notify_tenant, 'yes'); ?>>
                        <?php _e('Send payment receipts to tenants', 'housing-rent-mgmt'); ?>
                    </label>
                </div>
                
                <div class="hrm-form-row">
                    <label>
                        <input type="checkbox" name="hrm_notify_tenant_maintenance" id="hrm_notify_tenant_maintenance" value="yes" <?php checked(get_option('hrm_notify_tenant_maintenance'), 'yes'); ?>>
                        <?php _e('Notify tenants on maintenance updates', 'housing-rent-mgmt'); ?>
                    </label>
                </div>
                
                <h3><?php _e('Payment Reminders', 'housing-rent-mgmt'); ?></h3>
                
                <div class="hrm-form-row">
                    <label>
                        <input type="checkbox" name="hrm_auto_reminders" id="hrm_auto_reminders" value="yes" <?php checked($auto_reminders, 'yes'); ?>>
                        <?php _e('Send automatic payment reminders', 'housing-rent-mgmt'); ?>
                    </label>
                </div>
                
                <div class="hrm-form-row">
                    <label for="hrm_reminder_days"><?php _e('Reminder Days Before Due', 'housing-rent-mgmt'); ?></label>
                    <input type="number" name="hrm_reminder_days" id="hrm_reminder_days" value="<?php echo esc_attr($reminder_days); ?>" min="1" max="30">
                </div>
            </div>
            
            <!-- Advanced Settings -->
            <div id="advanced" class="hrm-tab-pane">
                <h3><?php _e('Advanced Settings', 'housing-rent-mgmt'); ?></h3>
                
                <div class="hrm-form-row">
                    <label>
                        <input type="checkbox" name="hrm_keep_data_on_uninstall" id="hrm_keep_data_on_uninstall" value="yes" <?php checked($keep_data, 'yes'); ?>>
                        <?php _e('Keep data on uninstall', 'housing-rent-mgmt'); ?>
                    </label>
                    <p class="description"><?php _e('If checked, plugin data will remain in the database when the plugin is uninstalled.', 'housing-rent-mgmt'); ?></p>
                </div>
                
                <div class="hrm-form-row">
                    <label for="hrm_debug_mode">
                        <input type="checkbox" name="hrm_debug_mode" id="hrm_debug_mode" value="yes" <?php checked(get_option('hrm_debug_mode'), 'yes'); ?>>
                        <?php _e('Enable Debug Mode', 'housing-rent-mgmt'); ?>
                    </label>
                    <p class="description"><?php _e('Log plugin activities for debugging purposes.', 'housing-rent-mgmt'); ?></p>
                </div>
                
                <hr>
                
                <h3><?php _e('Database Tools', 'housing-rent-mgmt'); ?></h3>
                
                <div class="hrm-form-row">
                    <button type="button" class="hrm-button hrm-button-secondary" id="hrm-check-tables"><?php _e('Check Database Tables', 'housing-rent-mgmt'); ?></button>
                    <button type="button" class="hrm-button hrm-button-secondary" id="hrm-optimize-tables"><?php _e('Optimize Tables', 'housing-rent-mgmt'); ?></button>
                    <p class="description"><?php _e('Check and optimize database tables.', 'housing-rent-mgmt'); ?></p>
                </div>
                
                <div class="hrm-form-row">
                    <button type="button" class="hrm-button hrm-button-danger" id="hrm-clear-logs" onclick="return confirm('<?php _e('Are you sure? This will delete all activity logs.', 'housing-rent-mgmt'); ?>')"><?php _e('Clear Activity Logs', 'housing-rent-mgmt'); ?></button>
                </div>
            </div>
        </div>
        
        <div class="hrm-form-row" style="margin-top: 30px;">
            <input type="hidden" name="hrm_save_settings" value="1">
            <button type="submit" class="hrm-button"><?php _e('Save Settings', 'housing-rent-mgmt'); ?></button>
            <button type="reset" class="hrm-button hrm-button-secondary"><?php _e('Reset', 'housing-rent-mgmt'); ?></button>
        </div>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    // Tab switching
    $('.hrm-tab-button').on('click', function() {
        var tabId = $(this).data('tab');
        
        $('.hrm-tab-button').removeClass('active');
        $(this).addClass('active');
        
        $('.hrm-tab-pane').removeClass('active');
        $('#' + tabId).addClass('active');
    });
    
    // Check database tables
    $('#hrm-check-tables').on('click', function() {
        $.ajax({
            url: hrm_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'hrm_check_tables',
                nonce: hrm_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    alert('All tables are working properly.');
                } else {
                    alert('Some tables need attention: ' + response.data);
                }
            }
        });
    });
    
    // Optimize tables
    $('#hrm-optimize-tables').on('click', function() {
        $.ajax({
            url: hrm_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'hrm_optimize_tables',
                nonce: hrm_ajax.nonce
            },
            beforeSend: function() {
                $(this).prop('disabled', true).text('Optimizing...');
            },
            success: function(response) {
                if (response.success) {
                    alert('Tables optimized successfully.');
                }
            },
            complete: function() {
                $('#hrm-optimize-tables').prop('disabled', false).text('Optimize Tables');
            }
        });
    });
    
    // Clear logs
    $('#hrm-clear-logs').on('click', function() {
        if (confirm('<?php _e('Are you sure? This will delete all activity logs.', 'housing-rent-mgmt'); ?>')) {
            $.ajax({
                url: hrm_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'hrm_clear_logs',
                    nonce: hrm_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        alert('Logs cleared successfully.');
                    }
                }
            });
        }
    });
});
</script>