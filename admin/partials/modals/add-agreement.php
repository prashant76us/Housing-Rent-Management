<div id="hrm-add-agreement-modal" class="hrm-modal">
    <div class="hrm-modal-content" style="max-width: 800px;">
        <span class="hrm-modal-close">&times;</span>
        <h2><?php _e('Create New Rent Agreement', 'housing-rent-mgmt'); ?></h2>
        
        <form id="hrm-add-agreement-form" class="hrm-form">
            <?php wp_nonce_field('hrm_add_agreement', 'agreement_nonce'); ?>
            
            <div class="hrm-form-row">
                <label for="property_id"><?php _e('Property *', 'housing-rent-mgmt'); ?></label>
                <select name="property_id" id="property_id" required>
                    <option value=""><?php _e('Select Property', 'housing-rent-mgmt'); ?></option>
                    <?php
                    global $wpdb;
                    $properties = $wpdb->get_results("SELECT id, property_name, address_line1, city FROM " . HRM_Functions::get_table_name('properties') . " WHERE status = 'available'");
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
                <textarea name="maintenance_responsibility" id="maintenance_responsibility" rows="3" placeholder="<?php _e('Who is responsible for what maintenance?', 'housing-rent-mgmt'); ?>"></textarea>
            </div>
            
            <div class="hrm-form-row">
                <label for="utilities_included"><?php _e('Utilities Included', 'housing-rent-mgmt'); ?></label>
                <textarea name="utilities_included" id="utilities_included" rows="2" placeholder="<?php _e('e.g., Water, Electricity, Gas', 'housing-rent-mgmt'); ?>"></textarea>
            </div>
            
            <div class="hrm-form-row">
                <label for="special_clauses"><?php _e('Special Clauses', 'housing-rent-mgmt'); ?></label>
                <textarea name="special_clauses" id="special_clauses" rows="3" placeholder="<?php _e('Any special terms or conditions', 'housing-rent-mgmt'); ?>"></textarea>
            </div>
            
            <div class="hrm-form-row">
                <label for="agreement_document"><?php _e('Agreement Document', 'housing-rent-mgmt'); ?></label>
                <input type="file" name="agreement_document" id="agreement_document" accept=".pdf,.doc,.docx">
                <p class="description"><?php _e('Upload the signed agreement document (PDF, DOC, DOCX)', 'housing-rent-mgmt'); ?></p>
            </div>
            
            <div class="hrm-form-row">
                <button type="submit" class="hrm-button"><?php _e('Create Agreement', 'housing-rent-mgmt'); ?></button>
                <button type="button" class="hrm-button hrm-button-secondary hrm-modal-close"><?php _e('Cancel', 'housing-rent-mgmt'); ?></button>
            </div>
        </form>
    </div>
</div>