<div id="hrm-add-payment-modal" class="hrm-modal">
    <div class="hrm-modal-content">
        <span class="hrm-modal-close">&times;</span>
        <h2><?php _e('Record Rent Payment', 'housing-rent-mgmt'); ?></h2>
        
        <form id="hrm-add-payment-form" class="hrm-form">
            <?php wp_nonce_field('hrm_add_payment', 'payment_nonce'); ?>
            
            <div class="hrm-form-row">
                <label for="agreement_id"><?php _e('Agreement *', 'housing-rent-mgmt'); ?></label>
                <select name="agreement_id" id="agreement_id" required>
                    <option value=""><?php _e('Select Agreement', 'housing-rent-mgmt'); ?></option>
                    <?php
                    global $wpdb;
                    $agreements = $wpdb->get_results("
                        SELECT ra.*, 
                               CONCAT(t.first_name, ' ', t.last_name) as tenant_name,
                               p.property_name 
                        FROM " . HRM_Functions::get_table_name('rent_agreements') . " ra
                        JOIN " . HRM_Functions::get_table_name('tenants') . " t ON ra.tenant_id = t.id
                        JOIN " . HRM_Functions::get_table_name('properties') . " p ON ra.property_id = p.id
                        WHERE ra.status = 'active'
                    ");
                    
                    foreach ($agreements as $agreement) {
                        echo '<option value="' . $agreement->id . '" data-rent="' . $agreement->monthly_rent . '">';
                        echo $agreement->agreement_number . ' - ' . $agreement->tenant_name . ' - ' . $agreement->property_name;
                        echo ' (' . HRM_Functions::format_currency($agreement->monthly_rent) . '/month)';
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
                <p class="description"><?php _e('Select the month this payment is for', 'housing-rent-mgmt'); ?></p>
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
                <p class="description"><?php _e('Check number, transaction ID, or reference', 'housing-rent-mgmt'); ?></p>
            </div>
            
            <div class="hrm-form-row">
                <label for="notes"><?php _e('Notes', 'housing-rent-mgmt'); ?></label>
                <textarea name="notes" id="notes" rows="3" placeholder="<?php _e('Any additional notes about this payment', 'housing-rent-mgmt'); ?>"></textarea>
            </div>
            
            <div class="hrm-form-row">
                <label for="send_receipt"><?php _e('Send Receipt', 'housing-rent-mgmt'); ?></label>
                <input type="checkbox" name="send_receipt" id="send_receipt" value="1" checked>
                <span><?php _e('Send payment receipt to tenant via email', 'housing-rent-mgmt'); ?></span>
            </div>
            
            <div class="hrm-form-row">
                <button type="submit" class="hrm-button"><?php _e('Record Payment', 'housing-rent-mgmt'); ?></button>
                <button type="button" class="hrm-button hrm-button-secondary hrm-modal-close"><?php _e('Cancel', 'housing-rent-mgmt'); ?></button>
            </div>
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Auto-fill amount when agreement is selected
    $('#agreement_id').on('change', function() {
        var selected = $(this).find(':selected');
        var rent = selected.data('rent');
        if (rent) {
            $('#amount').val(rent);
        }
    });
});
</script>