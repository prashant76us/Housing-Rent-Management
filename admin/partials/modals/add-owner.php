<div id="hrm-add-owner-modal" class="hrm-modal">
    <div class="hrm-modal-content">
        <span class="hrm-modal-close">&times;</span>
        <h2><?php _e('Add New Owner', 'housing-rent-mgmt'); ?></h2>
        
        <form id="hrm-add-owner-form" class="hrm-form">
            <div class="hrm-form-row">
                <label for="first_name"><?php _e('First Name *', 'housing-rent-mgmt'); ?></label>
                <input type="text" name="first_name" id="first_name" required>
            </div>
            
            <div class="hrm-form-row">
                <label for="last_name"><?php _e('Last Name *', 'housing-rent-mgmt'); ?></label>
                <input type="text" name="last_name" id="last_name" required>
            </div>
            
            <div class="hrm-form-row">
                <label for="email"><?php _e('Email *', 'housing-rent-mgmt'); ?></label>
                <input type="email" name="email" id="email" required>
            </div>
            
            <div class="hrm-form-row">
                <label for="phone"><?php _e('Phone *', 'housing-rent-mgmt'); ?></label>
                <input type="text" name="phone" id="phone" required>
            </div>
            
            <div class="hrm-form-row">
                <label for="alternate_phone"><?php _e('Alternate Phone', 'housing-rent-mgmt'); ?></label>
                <input type="text" name="alternate_phone" id="alternate_phone">
            </div>
            
            <div class="hrm-form-row">
                <label for="address"><?php _e('Address', 'housing-rent-mgmt'); ?></label>
                <textarea name="address" id="address" rows="3"></textarea>
            </div>
            
            <div class="hrm-form-row">
                <label for="city"><?php _e('City', 'housing-rent-mgmt'); ?></label>
                <input type="text" name="city" id="city">
            </div>
            
            <div class="hrm-form-row">
                <label for="state"><?php _e('State', 'housing-rent-mgmt'); ?></label>
                <input type="text" name="state" id="state">
            </div>
            
            <div class="hrm-form-row">
                <label for="zip_code"><?php _e('Zip Code', 'housing-rent-mgmt'); ?></label>
                <input type="text" name="zip_code" id="zip_code">
            </div>
            
            <h3><?php _e('Identification', 'housing-rent-mgmt'); ?></h3>
            
            <div class="hrm-form-row">
                <label for="id_proof_type"><?php _e('ID Proof Type', 'housing-rent-mgmt'); ?></label>
                <select name="id_proof_type" id="id_proof_type">
                    <option value=""><?php _e('Select Type', 'housing-rent-mgmt'); ?></option>
                    <option value="passport"><?php _e('Passport', 'housing-rent-mgmt'); ?></option>
                    <option value="driving_license"><?php _e('Driving License', 'housing-rent-mgmt'); ?></option>
                    <option value="national_id"><?php _e('National ID', 'housing-rent-mgmt'); ?></option>
                </select>
            </div>
            
            <div class="hrm-form-row">
                <label for="id_proof_number"><?php _e('ID Proof Number', 'housing-rent-mgmt'); ?></label>
                <input type="text" name="id_proof_number" id="id_proof_number">
            </div>
            
            <h3><?php _e('Bank Details', 'housing-rent-mgmt'); ?></h3>
            
            <div class="hrm-form-row">
                <label for="bank_name"><?php _e('Bank Name', 'housing-rent-mgmt'); ?></label>
                <input type="text" name="bank_name" id="bank_name">
            </div>
            
            <div class="hrm-form-row">
                <label for="bank_account_number"><?php _e('Account Number', 'housing-rent-mgmt'); ?></label>
                <input type="text" name="bank_account_number" id="bank_account_number">
            </div>
            
            <div class="hrm-form-row">
                <label for="bank_routing_number"><?php _e('Routing Number', 'housing-rent-mgmt'); ?></label>
                <input type="text" name="bank_routing_number" id="bank_routing_number">
            </div>
            
            <div class="hrm-form-row">
                <label for="tax_id"><?php _e('Tax ID / SSN', 'housing-rent-mgmt'); ?></label>
                <input type="text" name="tax_id" id="tax_id">
            </div>
            
            <div class="hrm-form-row">
                <label for="notes"><?php _e('Notes', 'housing-rent-mgmt'); ?></label>
                <textarea name="notes" id="notes" rows="3"></textarea>
            </div>
            
            <div class="hrm-form-row">
                <button type="submit" class="hrm-button"><?php _e('Add Owner', 'housing-rent-mgmt'); ?></button>
                <button type="button" class="hrm-button hrm-button-secondary hrm-modal-close"><?php _e('Cancel', 'housing-rent-mgmt'); ?></button>
            </div>
        </form>
    </div>
</div>