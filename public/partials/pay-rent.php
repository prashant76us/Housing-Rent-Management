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
    "SELECT ra.*, p.property_name 
     FROM $agreement_table ra
     JOIN " . HRM_Functions::get_table_name('properties') . " p ON ra.property_id = p.id
     WHERE ra.tenant_id = %d AND ra.status = 'active'",
    $tenant->id
));

if (!$agreement) {
    echo '<div class="hrm-alert hrm-alert-error">' . __('No active rental agreement found.', 'housing-rent-mgmt') . '</div>';
    return;
}

// Check if already paid for current month
$current_month = date('Y-m-01');
$existing_payment = $wpdb->get_row($wpdb->prepare(
    "SELECT * FROM " . HRM_Functions::get_table_name('rent_payments') . " 
     WHERE tenant_id = %d AND for_month = %s",
    $tenant->id,
    $current_month
));

if ($existing_payment && $existing_payment->status === 'paid') {
    echo '<div class="hrm-alert hrm-alert-success">' . __('You have already paid rent for this month.', 'housing-rent-mgmt') . '</div>';
    return;
}

// Calculate late fee if applicable
$due_date = date('Y-m-d', strtotime($agreement->start_date . ' + ' . ($agreement->rent_due_day - 1) . ' days'));
$late_fee = 0;
$days_late = 0;

if (strtotime(current_time('Y-m-d')) > strtotime($due_date)) {
    $days_late = floor((strtotime(current_time('Y-m-d')) - strtotime($due_date)) / (60 * 60 * 24));
    if ($days_late > $agreement->late_fee_after_days) {
        $late_fee = $agreement->late_fee;
    }
}

$total_amount = $agreement->monthly_rent + $late_fee;
?>

<div class="hrm-container">
    <div class="hrm-form">
        <h2><?php _e('Pay Rent', 'housing-rent-mgmt'); ?></h2>
        
        <div class="hrm-alert-container"></div>
        
        <div class="hrm-payment-details">
            <h3><?php _e('Payment Details', 'housing-rent-mgmt'); ?></h3>
            <p><strong><?php _e('Property:', 'housing-rent-mgmt'); ?></strong> <?php echo esc_html($agreement->property_name); ?></p>
            <p><strong><?php _e('Monthly Rent:', 'housing-rent-mgmt'); ?></strong> <?php echo HRM_Functions::format_currency($agreement->monthly_rent); ?></p>
            <p><strong><?php _e('Due Date:', 'housing-rent-mgmt'); ?></strong> <?php echo date_i18n(get_option('date_format'), strtotime($due_date)); ?></p>
            
            <?php if ($late_fee > 0): ?>
                <p><strong style="color: #dc3232;"><?php _e('Late Fee:', 'housing-rent-mgmt'); ?></strong> <?php echo HRM_Functions::format_currency($late_fee); ?></p>
                <p><small><?php printf(__('Payment is %d days late', 'housing-rent-mgmt'), $days_late); ?></small></p>
            <?php endif; ?>
            
            <p class="total"><strong><?php _e('Total Amount Due:', 'housing-rent-mgmt'); ?></strong> <?php echo HRM_Functions::format_currency($total_amount); ?></p>
        </div>
        
        <form id="hrm-payment-form" class="hrm-payment-form">
            <input type="hidden" name="agreement_id" value="<?php echo $agreement->id; ?>">
            <input type="hidden" name="amount" value="<?php echo $total_amount; ?>">
            
            <div class="hrm-form-group">
                <label><?php _e('Select Payment Method', 'housing-rent-mgmt'); ?></label>
                <div class="hrm-payment-methods">
                    <div class="hrm-payment-method">
                        <input type="radio" name="payment_method" id="method_card" value="credit_card" required>
                        <label for="method_card"><?php _e('Credit Card', 'housing-rent-mgmt'); ?></label>
                    </div>
                    
                    <div class="hrm-payment-method">
                        <input type="radio" name="payment_method" id="method_bank" value="bank_transfer">
                        <label for="method_bank"><?php _e('Bank Transfer', 'housing-rent-mgmt'); ?></label>
                    </div>
                    
                    <div class="hrm-payment-method">
                        <input type="radio" name="payment_method" id="method_paypal" value="paypal">
                        <label for="method_paypal"><?php _e('PayPal', 'housing-rent-mgmt'); ?></label>
                    </div>
                </div>
            </div>
            
            <!-- Credit Card Fields -->
            <div id="credit_card_fields" class="payment-fields" style="display: none;">
                <div class="hrm-form-group">
                    <label for="card_number"><?php _e('Card Number', 'housing-rent-mgmt'); ?></label>
                    <input type="text" name="card_number" id="card_number" placeholder="**** **** **** ****">
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <div class="hrm-form-group">
                        <label for="card_expiry"><?php _e('Expiry (MM/YY)', 'housing-rent-mgmt'); ?></label>
                        <input type="text" name="card_expiry" id="card_expiry" placeholder="MM/YY">
                    </div>
                    
                    <div class="hrm-form-group">
                        <label for="card_cvv"><?php _e('CVV', 'housing-rent-mgmt'); ?></label>
                        <input type="text" name="card_cvv" id="card_cvv" placeholder="***">
                    </div>
                </div>
            </div>
            
            <!-- Bank Transfer Fields -->
            <div id="bank_transfer_fields" class="payment-fields" style="display: none;">
                <div class="hrm-alert hrm-alert-info">
                    <p><?php _e('Please transfer the amount to the following bank account:', 'housing-rent-mgmt'); ?></p>
                    <p><strong><?php _e('Bank:', 'housing-rent-mgmt'); ?></strong> Example Bank<br>
                    <strong><?php _e('Account Name:', 'housing-rent-mgmt'); ?></strong> Housing Rent Management<br>
                    <strong><?php _e('Account Number:', 'housing-rent-mgmt'); ?></strong> 1234567890<br>
                    <strong><?php _e('Routing Number:', 'housing-rent-mgmt'); ?></strong> 123456789</p>
                    <p><small><?php _e('After transfer, enter the transaction ID below.', 'housing-rent-mgmt'); ?></small></p>
                </div>
                
                <div class="hrm-form-group">
                    <label for="transaction_id"><?php _e('Transaction ID / Reference', 'housing-rent-mgmt'); ?></label>
                    <input type="text" name="transaction_id" id="transaction_id">
                </div>
            </div>
            
            <!-- PayPal Fields -->
            <div id="paypal_fields" class="payment-fields" style="display: none;">
                <div class="hrm-alert hrm-alert-info">
                    <p><?php _e('You will be redirected to PayPal to complete your payment.', 'housing-rent-mgmt'); ?></p>
                </div>
            </div>
            
            <div class="hrm-form-group">
                <label for="notes"><?php _e('Additional Notes (Optional)', 'housing-rent-mgmt'); ?></label>
                <textarea name="notes" id="notes" rows="3" placeholder="<?php _e('Any additional information...', 'housing-rent-mgmt'); ?>"></textarea>
            </div>
            
            <button type="submit" class="hrm-submit-btn"><?php printf(__('Pay %s Now', 'housing-rent-mgmt'), HRM_Functions::format_currency($total_amount)); ?></button>
        </form>
        
        <p style="text-align: center; margin-top: 20px;">
            <a href="[hrm_tenant_dashboard]"><?php _e('← Back to Dashboard', 'housing-rent-mgmt'); ?></a>
        </p>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Show/hide payment fields based on selected method
    $('input[name="payment_method"]').on('change', function() {
        $('.payment-fields').hide();
        var method = $(this).val();
        $('#' + method + '_fields').show();
    });
    
    // Format card number
    $('#card_number').on('input', function() {
        var value = $(this).val().replace(/\D/g, '');
        var formatted = value.match(/.{1,4}/g);
        if (formatted) {
            $(this).val(formatted.join(' '));
        }
    });
    
    // Format expiry
    $('#card_expiry').on('input', function() {
        var value = $(this).val().replace(/\D/g, '');
        if (value.length >= 2) {
            $(this).val(value.substr(0, 2) + '/' + value.substr(2, 2));
        }
    });
    
    // Form submission
    $('#hrm-payment-form').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var formData = new FormData(form[0]);
        formData.append('action', 'hrm_process_rent_payment');
        formData.append('nonce', hrm_public.nonce);
        
        // Validate based on payment method
        var method = $('input[name="payment_method"]:checked').val();
        
        if (method === 'credit_card') {
            if (!$('#card_number').val() || !$('#card_expiry').val() || !$('#card_cvv').val()) {
                showAlert('error', '<?php _e('Please fill in all card details.', 'housing-rent-mgmt'); ?>');
                return;
            }
        } else if (method === 'bank_transfer' && !$('#transaction_id').val()) {
            showAlert('error', '<?php _e('Please enter the transaction ID.', 'housing-rent-mgmt'); ?>');
            return;
        }
        
        $.ajax({
            url: hrm_public.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                form.find('button[type="submit"]').prop('disabled', true).html('<span class="hrm-spinner"></span> <?php _e('Processing...', 'housing-rent-mgmt'); ?>');
            },
            success: function(response) {
                if (response.success) {
                    showAlert('success', '<?php _e('Payment successful! Redirecting...', 'housing-rent-mgmt'); ?>');
                    
                    // Redirect to dashboard after 2 seconds
                    setTimeout(function() {
                        window.location.href = '[hrm_tenant_dashboard]';
                    }, 2000);
                } else {
                    showAlert('error', response.data);
                    form.find('button[type="submit"]').prop('disabled', false).text('<?php printf(__('Pay %s Now', 'housing-rent-mgmt'), HRM_Functions::format_currency($total_amount)); ?>');
                }
            },
            error: function() {
                showAlert('error', '<?php _e('Payment failed. Please try again.', 'housing-rent-mgmt'); ?>');
                form.find('button[type="submit"]').prop('disabled', false).text('<?php printf(__('Pay %s Now', 'housing-rent-mgmt'), HRM_Functions::format_currency($total_amount)); ?>');
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