<?php
global $wpdb;
$properties_table = HRM_Functions::get_table_name('properties');

// Handle property deletion
if (isset($_POST['action']) && $_POST['action'] === 'delete_property' && isset($_POST['property_id'])) {
    if (!wp_verify_nonce($_POST['_wpnonce'], 'delete_property_' . $_POST['property_id'])) {
        wp_die('Security check failed');
    }
    
    $wpdb->delete($properties_table, ['id' => intval($_POST['property_id'])]);
    echo '<div class="notice notice-success"><p>' . __('Property deleted successfully.', 'housing-rent-mgmt') . '</p></div>';
}

// Get all properties with owner information
$properties = $wpdb->get_results("
    SELECT p.*, GROUP_CONCAT(CONCAT(o.first_name, ' ', o.last_name) SEPARATOR ', ') as owners
    FROM $properties_table p
    LEFT JOIN " . HRM_Functions::get_table_name('property_owner_relations') . " por ON p.id = por.property_id
    LEFT JOIN " . HRM_Functions::get_table_name('property_owners') . " o ON por.owner_id = o.id
    GROUP BY p.id
    ORDER BY p.created_at DESC
");
?>

<div class="wrap">
    <h1><?php _e('Properties', 'housing-rent-mgmt'); ?>
        <button class="hrm-button hrm-open-modal" data-modal="hrm-add-property-modal" style="float: right;"><?php _e('Add New Property', 'housing-rent-mgmt'); ?></button>
    </h1>
    
    <div class="hrm-notices"></div>
    
    <!-- Search Box -->
    <div class="hrm-search-box">
        <input type="text" class="hrm-search-input" data-table="properties" placeholder="<?php _e('Search properties...', 'housing-rent-mgmt'); ?>">
        <select id="hrm-filter-status">
            <option value=""><?php _e('All Status', 'housing-rent-mgmt'); ?></option>
            <option value="available"><?php _e('Available', 'housing-rent-mgmt'); ?></option>
            <option value="rented"><?php _e('Rented', 'housing-rent-mgmt'); ?></option>
            <option value="maintenance"><?php _e('Maintenance', 'housing-rent-mgmt'); ?></option>
        </select>
    </div>
    
    <!-- Properties Table -->
    <table class="hrm-table">
        <thead>
            <tr>
                <th><?php _e('ID', 'housing-rent-mgmt'); ?></th>
                <th><?php _e('Property Name', 'housing-rent-mgmt'); ?></th>
                <th><?php _e('Address', 'housing-rent-mgmt'); ?></th>
                <th><?php _e('Type', 'housing-rent-mgmt'); ?></th>
                <th><?php _e('Bed/Bath', 'housing-rent-mgmt'); ?></th>
                <th><?php _e('Owners', 'housing-rent-mgmt'); ?></th>
                <th><?php _e('Status', 'housing-rent-mgmt'); ?></th>
                <th><?php _e('Actions', 'housing-rent-mgmt'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($properties)): ?>
                <tr>
                    <td colspan="8" style="text-align: center;"><?php _e('No properties found.', 'housing-rent-mgmt'); ?></td>
                </tr>
            <?php else: ?>
                <?php foreach ($properties as $property): ?>
                    <tr>
                        <td>#<?php echo $property->id; ?></td>
                        <td><strong><?php echo esc_html($property->property_name); ?></strong></td>
                        <td>
                            <?php echo esc_html($property->address_line1); ?><br>
                            <small><?php echo esc_html($property->city . ', ' . $property->state . ' ' . $property->zip_code); ?></small>
                        </td>
                        <td><?php echo ucfirst($property->property_type); ?></td>
                        <td><?php echo $property->bedrooms; ?> / <?php echo $property->bathrooms; ?></td>
                        <td><?php echo $property->owners ?: '—'; ?></td>
                        <td><span class="hrm-status hrm-status-<?php echo $property->status; ?>"><?php echo ucfirst($property->status); ?></span></td>
                        <td>
                            <button class="hrm-button hrm-button-secondary hrm-edit-property" data-id="<?php echo $property->id; ?>"><?php _e('Edit', 'housing-rent-mgmt'); ?></button>
                            <form method="post" style="display: inline;" onsubmit="return confirm('<?php _e('Are you sure?', 'housing-rent-mgmt'); ?>');">
                                <?php wp_nonce_field('delete_property_' . $property->id); ?>
                                <input type="hidden" name="action" value="delete_property">
                                <input type="hidden" name="property_id" value="<?php echo $property->id; ?>">
                                <button type="submit" class="hrm-button hrm-button-secondary" style="background: #dc3232; color: white;"><?php _e('Delete', 'housing-rent-mgmt'); ?></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Add Property Modal -->
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
            </div>
        </form>
    </div>
</div>