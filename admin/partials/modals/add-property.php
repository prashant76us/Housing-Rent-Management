<div id="hrm-add-property-modal" class="hrm-modal">
    <div class="hrm-modal-content">
        <span class="hrm-modal-close">&times;</span>
        <h2><?php _e('Add New Property', 'housing-rent-mgmt'); ?></h2>
        
        <form id="hrm-add-property-form" class="hrm-form" enctype="multipart/form-data">
            <div class="hrm-form-row">
                <label for="property_name"><?php _e('Property Name *', 'housing-rent-mgmt'); ?></label>
                <input type="text" name="property_name" id="property_name" required>
            </div>
            
            <div class="hrm-form-row">
                <label for="property_type"><?php _e('Property Type', 'housing-rent-mgmt'); ?></label>
                <select name="property_type" id="property_type">
                    <option value="apartment"><?php _e('Apartment', 'housing-rent-mgmt'); ?></option>
                    <option value="house"><?php _e('House', 'housing-rent-mgmt'); ?></option>
                    <option value="condo"><?php _e('Condo', 'housing-rent-mgmt'); ?></option>
                    <option value="townhouse"><?php _e('Townhouse', 'housing-rent-mgmt'); ?></option>
                    <option value="commercial"><?php _e('Commercial', 'housing-rent-mgmt'); ?></option>
                </select>
            </div>
            
            <div class="hrm-form-row">
                <label for="address_line1"><?php _e('Address Line 1 *', 'housing-rent-mgmt'); ?></label>
                <input type="text" name="address_line1" id="address_line1" required>
            </div>
            
            <div class="hrm-form-row">
                <label for="address_line2"><?php _e('Address Line 2', 'housing-rent-mgmt'); ?></label>
                <input type="text" name="address_line2" id="address_line2">
            </div>
            
            <div class="hrm-form-row">
                <label for="city"><?php _e('City *', 'housing-rent-mgmt'); ?></label>
                <input type="text" name="city" id="city" required>
            </div>
            
            <div class="hrm-form-row">
                <label for="state"><?php _e('State *', 'housing-rent-mgmt'); ?></label>
                <input type="text" name="state" id="state" required>
            </div>
            
            <div class="hrm-form-row">
                <label for="zip_code"><?php _e('Zip Code *', 'housing-rent-mgmt'); ?></label>
                <input type="text" name="zip_code" id="zip_code" required>
            </div>
            
            <div class="hrm-form-row">
                <label for="country"><?php _e('Country', 'housing-rent-mgmt'); ?></label>
                <input type="text" name="country" id="country" value="USA">
            </div>
            
            <div class="hrm-form-row">
                <label for="bedrooms"><?php _e('Bedrooms', 'housing-rent-mgmt'); ?></label>
                <input type="number" name="bedrooms" id="bedrooms" min="0">
            </div>
            
            <div class="hrm-form-row">
                <label for="bathrooms"><?php _e('Bathrooms', 'housing-rent-mgmt'); ?></label>
                <input type="number" name="bathrooms" id="bathrooms" min="0" step="0.5">
            </div>
            
            <div class="hrm-form-row">
                <label for="square_feet"><?php _e('Square Feet', 'housing-rent-mgmt'); ?></label>
                <input type="number" name="square_feet" id="square_feet" min="0">
            </div>
            
            <div class="hrm-form-row">
                <label for="furnished"><?php _e('Furnished', 'housing-rent-mgmt'); ?></label>
                <input type="checkbox" name="furnished" id="furnished" value="1">
            </div>
            
            <div class="hrm-form-row">
                <label for="parking_spaces"><?php _e('Parking Spaces', 'housing-rent-mgmt'); ?></label>
                <input type="number" name="parking_spaces" id="parking_spaces" min="0">
            </div>
            
            <div class="hrm-form-row">
                <label for="description"><?php _e('Description', 'housing-rent-mgmt'); ?></label>
                <textarea name="description" id="description" rows="4"></textarea>
            </div>
            
            <div class="hrm-form-row">
                <label for="amenities"><?php _e('Amenities', 'housing-rent-mgmt'); ?></label>
                <textarea name="amenities" id="amenities" rows="3" placeholder="<?php _e('Enter amenities separated by commas', 'housing-rent-mgmt'); ?>"></textarea>
            </div>
            
            <div class="hrm-form-row">
                <label for="status"><?php _e('Status', 'housing-rent-mgmt'); ?></label>
                <select name="status" id="status">
                    <option value="available"><?php _e('Available', 'housing-rent-mgmt'); ?></option>
                    <option value="rented"><?php _e('Rented', 'housing-rent-mgmt'); ?></option>
                    <option value="maintenance"><?php _e('Maintenance', 'housing-rent-mgmt'); ?></option>
                </select>
            </div>
            
            <div class="hrm-form-row">
                <label for="property_images"><?php _e('Property Images', 'housing-rent-mgmt'); ?></label>
                <input type="file" name="property_images[]" id="property_images" multiple accept="image/*">
            </div>
            
            <div class="hrm-form-row">
                <button type="submit" class="hrm-button"><?php _e('Add Property', 'housing-rent-mgmt'); ?></button>
                <button type="button" class="hrm-button hrm-button-secondary hrm-modal-close"><?php _e('Cancel', 'housing-rent-mgmt'); ?></button>
            </div>
        </form>
    </div>
</div>