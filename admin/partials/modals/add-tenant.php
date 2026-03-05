<div id="hrm-add-tenant-modal" class="hrm-modal">
    <div class="hrm-modal-content" style="max-width: 800px;">
        <span class="hrm-modal-close">&times;</span>
        <h2><?php _e('Add New Tenant', 'housing-rent-mgmt'); ?></h2>
        
        <form id="hrm-add-tenant-form" class="hrm-form">
            <div class="hrm-tabs">
                <div class="hrm-tab-buttons">
                    <button type="button" class="hrm-tab-button active" data-tab="personal-info"><?php _e('Personal Info', 'housing-rent-mgmt'); ?></button>
                    <button type="button" class="hrm-tab-button" data-tab="employment"><?php _e('Employment', 'housing-rent-mgmt'); ?></button>
                    <button type="button" class="hrm-tab-button" data-tab="emergency"><?php _e('Emergency Contact', 'housing-rent-mgmt'); ?></button>
                    <button type="button" class="hrm-tab-button" data-tab="documents"><?php _e('Documents', 'housing-rent-mgmt'); ?></button>
                </div>
                
                <div id="personal-info" class="hrm-tab-pane active">
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
                        <label for="date_of_birth"><?php _e('Date of Birth', 'housing-rent-mgmt'); ?></label>
                        <input type="date" name="date_of_birth" id="date_of_birth" class="hrm-datepicker">
                    </div>
                    
                    <div class="hrm-form-row">
                        <label for="current_address"><?php _e('Current Address', 'housing-rent-mgmt'); ?></label>
                        <textarea name="current_address" id="current_address" rows="3"></textarea>
                    </div>
                </div>
                
                <div id="employment" class="hrm-tab-pane">
                    <div class="hrm-form-row">
                        <label for="employment_status"><?php _e('Employment Status', 'housing-rent-mgmt'); ?></label>
                        <select name="employment_status" id="employment_status">
                            <option value=""><?php _e('Select Status', 'housing-rent-mgmt'); ?></option>
                            <option value="employed"><?php _e('Employed', 'housing-rent-mgmt'); ?></option>
                            <option value="self_employed"><?php _e('Self Employed', 'housing-rent-mgmt'); ?></option>
                            <option value="unemployed"><?php _e('Unemployed', 'housing-rent-mgmt'); ?></option>
                            <option value="retired"><?php _e('Retired', 'housing-rent-mgmt'); ?></option>
                            <option value="student"><?php _e('Student', 'housing-rent-mgmt'); ?></option>
                        </select>
                    </div>
                    
                    <div class="hrm-form-row">
                        <label for="employer_name"><?php _e('Employer Name', 'housing-rent-mgmt'); ?></label>
                        <input type="text" name="employer_name" id="employer_name">
                    </div>
                    
                    <div class="hrm-form-row">
                        <label for="annual_income"><?php _e('Annual Income', 'housing-rent-mgmt'); ?></label>
                        <input type="number" name="annual_income" id="annual_income" step="0.01" min="0">
                    </div>
                </div>
                
                <div id="emergency" class="hrm-tab-pane">
                    <div class="hrm-form-row">
                        <label for="emergency_contact_name"><?php _e('Contact Name', 'housing-rent-mgmt'); ?></label>
                        <input type="text" name="emergency_contact_name" id="emergency_contact_name">
                    </div>
                    
                    <div class="hrm-form-row">
                        <label for="emergency_contact_phone"><?php _e('Contact Phone', 'housing-rent-mgmt'); ?></label>
                        <input type="text" name="emergency_contact_phone" id="emergency_contact_phone">
                    </div>
                </div>
                
                <div id="documents" class="hrm-tab-pane">
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
                    
                    <div class="hrm-form-row">
                        <label for="previous_landlord_name"><?php _e('Previous Landlord Name', 'housing-rent-mgmt'); ?></label>
                        <input type="text" name="previous_landlord_name" id="previous_landlord_name">
                    </div>
                    
                    <div class="hrm-form-row">
                        <label for="previous_landlord_phone"><?php _e('Previous Landlord Phone', 'housing-rent-mgmt'); ?></label>
                        <input type="text" name="previous_landlord_phone" id="previous_landlord_phone">
                    </div>
                </div>
            </div>
            
            <div class="hrm-form-row">
                <label for="notes"><?php _e('Notes', 'housing-rent-mgmt'); ?></label>
                <textarea name="notes" id="notes" rows="3"></textarea>
            </div>
            
            <div class="hrm-form-row">
                <label for="status"><?php _e('Status', 'housing-rent-mgmt'); ?></label>
                <select name="status" id="status">
                    <option value="active"><?php _e('Active', 'housing-rent-mgmt'); ?></option>
                    <option value="inactive"><?php _e('Inactive', 'housing-rent-mgmt'); ?></option>
                </select>
            </div>
            
            <div class="hrm-form-row">
                <button type="submit" class="hrm-button"><?php _e('Add Tenant', 'housing-rent-mgmt'); ?></button>
                <button type="button" class="hrm-button hrm-button-secondary hrm-modal-close"><?php _e('Cancel', 'housing-rent-mgmt'); ?></button>
            </div>
        </form>
    </div>
</div>