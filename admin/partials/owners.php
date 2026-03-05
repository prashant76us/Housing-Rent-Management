<?php
global $wpdb;
$owners_table = HRM_Functions::get_table_name('property_owners');

// Handle owner deletion
if (isset($_POST['action']) && $_POST['action'] === 'delete_owner' && isset($_POST['owner_id'])) {
    if (!wp_verify_nonce($_POST['_wpnonce'], 'delete_owner_' . $_POST['owner_id'])) {
        wp_die('Security check failed');
    }
    
    $wpdb->delete($owners_table, ['id' => intval($_POST['owner_id'])]);
    echo '<div class="notice notice-success"><p>' . __('Owner deleted successfully.', 'housing-rent-mgmt') . '</p></div>';
}

// Get all owners with property count
$owners = $wpdb->get_results("
    SELECT o.*, COUNT(por.property_id) as property_count
    FROM $owners_table o
    LEFT JOIN " . HRM_Functions::get_table_name('property_owner_relations') . " por ON o.id = por.owner_id
    GROUP BY o.id
    ORDER BY o.created_at DESC
");
?>

<div class="wrap">
    <h1><?php _e('Property Owners', 'housing-rent-mgmt'); ?>
        <button class="hrm-button hrm-open-modal" data-modal="hrm-add-owner-modal" style="float: right;"><?php _e('Add New Owner', 'housing-rent-mgmt'); ?></button>
    </h1>
    
    <div class="hrm-notices"></div>
    
    <!-- Search Box -->
    <div class="hrm-search-box">
        <input type="text" class="hrm-search-input" data-table="owners" placeholder="<?php _e('Search owners...', 'housing-rent-mgmt'); ?>">
    </div>
    
    <!-- Owners Table -->
    <table class="hrm-table">
        <thead>
            <tr>
                <th><?php _e('ID', 'housing-rent-mgmt'); ?></th>
                <th><?php _e('Name', 'housing-rent-mgmt'); ?></th>
                <th><?php _e('Email', 'housing-rent-mgmt'); ?></th>
                <th><?php _e('Phone', 'housing-rent-mgmt'); ?></th>
                <th><?php _e('Properties', 'housing-rent-mgmt'); ?></th>
                <th><?php _e('Bank Details', 'housing-rent-mgmt'); ?></th>
                <th><?php _e('Actions', 'housing-rent-mgmt'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($owners)): ?>
                <tr>
                    <td colspan="7" style="text-align: center;"><?php _e('No owners found.', 'housing-rent-mgmt'); ?></td>
                </tr>
            <?php else: ?>
                <?php foreach ($owners as $owner): ?>
                    <tr>
                        <td>#<?php echo $owner->id; ?></td>
                        <td><strong><?php echo esc_html($owner->first_name . ' ' . $owner->last_name); ?></strong></td>
                        <td><a href="mailto:<?php echo esc_attr($owner->email); ?>"><?php echo esc_html($owner->email); ?></a></td>
                        <td><?php echo esc_html($owner->phone); ?></td>
                        <td><?php echo $owner->property_count; ?></td>
                        <td>
                            <?php if ($owner->bank_name): ?>
                                <small>
                                    <?php echo esc_html($owner->bank_name); ?><br>
                                    <?php _e('Account:', 'housing-rent-mgmt'); ?> <?php echo substr($owner->bank_account_number, -4); ?>
                                </small>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="hrm-button hrm-button-secondary hrm-edit-owner" data-id="<?php echo $owner->id; ?>"><?php _e('Edit', 'housing-rent-mgmt'); ?></button>
                            <form method="post" style="display: inline;" onsubmit="return confirm('<?php _e('Are you sure?', 'housing-rent-mgmt'); ?>');">
                                <?php wp_nonce_field('delete_owner_' . $owner->id); ?>
                                <input type="hidden" name="action" value="delete_owner">
                                <input type="hidden" name="owner_id" value="<?php echo $owner->id; ?>">
                                <button type="submit" class="hrm-button hrm-button-secondary" style="background: #dc3232; color: white;"><?php _e('Delete', 'housing-rent-mgmt'); ?></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Add Owner Modal -->
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
            </div>
        </form>
    </div>
</div>