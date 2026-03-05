/**
 * Housing Rent Management Public Scripts
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        initPaymentForm();
        initMaintenanceForm();
        initPropertySearch();
        initDashboardTabs();
    });
    
    // Initialize payment form
    function initPaymentForm() {
        $('#hrm-payment-form').on('submit', function(e) {
            e.preventDefault();
            
            var form = $(this);
            var formData = new FormData(form[0]);
            formData.append('action', 'hrm_process_rent_payment');
            formData.append('nonce', hrm_public.nonce);
            
            $.ajax({
                url: hrm_public.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    form.find('button[type="submit"]').prop('disabled', true).html('<span class="hrm-spinner"></span> Processing...');
                },
                success: function(response) {
                    if (response.success) {
                        showAlert('success', 'Payment successful! Receipt: ' + response.data.receipt_number);
                        form[0].reset();
                        
                        // Redirect to receipt page
                        if (response.data.receipt_url) {
                            window.location.href = response.data.receipt_url;
                        }
                    } else {
                        showAlert('error', response.data);
                    }
                },
                error: function() {
                    showAlert('error', 'Payment failed. Please try again.');
                },
                complete: function() {
                    form.find('button[type="submit"]').prop('disabled', false).text('Pay Now');
                }
            });
        });
        
        // Payment method selection
        $('.hrm-payment-method input[type="radio"]').on('change', function() {
            $('.hrm-payment-method').removeClass('selected');
            $(this).closest('.hrm-payment-method').addClass('selected');
        });
    }
    
    // Initialize maintenance form
    function initMaintenanceForm() {
        $('#hrm-maintenance-form').on('submit', function(e) {
            e.preventDefault();
            
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
                    form.find('button[type="submit"]').prop('disabled', true).html('<span class="hrm-spinner"></span> Submitting...');
                },
                success: function(response) {
                    if (response.success) {
                        showAlert('success', 'Maintenance request submitted successfully!');
                        form[0].reset();
                    } else {
                        showAlert('error', response.data);
                    }
                },
                error: function() {
                    showAlert('error', 'Failed to submit request. Please try again.');
                },
                complete: function() {
                    form.find('button[type="submit"]').prop('disabled', false).text('Submit Request');
                }
            });
        });
        
        // Issue type selection
        $('.hrm-issue-type').on('click', function() {
            $('.hrm-issue-type').removeClass('selected');
            $(this).addClass('selected');
            $('#hrm-issue-type').val($(this).data('type'));
        });
    }
    
    // Initialize property search
    function initPropertySearch() {
        var searchTimer;
        
        $('#hrm-property-search').on('keyup', function() {
            clearTimeout(searchTimer);
            var term = $(this).val();
            
            searchTimer = setTimeout(function() {
                searchProperties(term);
            }, 500);
        });
        
        $('#hrm-property-filter').on('change', function() {
            filterProperties();
        });
    }
    
    // Search properties
    function searchProperties(term) {
        $.ajax({
            url: hrm_public.ajax_url,
            type: 'POST',
            data: {
                action: 'hrm_search_properties',
                term: term,
                nonce: hrm_public.nonce
            },
            beforeSend: function() {
                $('.hrm-properties-list').html('<div class="hrm-spinner"></div>');
            },
            success: function(response) {
                if (response.success) {
                    updatePropertiesList(response.data);
                }
            }
        });
    }
    
    // Filter properties
    function filterProperties() {
        var filters = {
            type: $('#hrm-filter-type').val(),
            city: $('#hrm-filter-city').val(),
            min_price: $('#hrm-filter-min-price').val(),
            max_price: $('#hrm-filter-max-price').val()
        };
        
        $.ajax({
            url: hrm_public.ajax_url,
            type: 'POST',
            data: {
                action: 'hrm_filter_properties',
                filters: filters,
                nonce: hrm_public.nonce
            },
            success: function(response) {
                if (response.success) {
                    updatePropertiesList(response.data);
                }
            }
        });
    }
    
    // Update properties list
    function updatePropertiesList(properties) {
        var html = '';
        
        properties.forEach(function(property) {
            html += '<div class="hrm-property-card">';
            html += '<div class="hrm-property-details">';
            html += '<h3>' + property.property_name + '</h3>';
            html += '<p>' + property.address_line1 + ', ' + property.city + '</p>';
            html += '<p>Bedrooms: ' + property.bedrooms + ' | Bathrooms: ' + property.bathrooms + '</p>';
            html += '<div class="hrm-property-price">' + formatCurrency(property.monthly_rent) + '/month</div>';
            html += '<span class="hrm-property-status ' + property.status + '">' + property.status + '</span>';
            html += '</div>';
            html += '</div>';
        });
        
        $('.hrm-properties-list').html(html);
    }
    
    // Initialize dashboard tabs
    function initDashboardTabs() {
        $('.hrm-dashboard-tab').on('click', function(e) {
            e.preventDefault();
            var tabId = $(this).data('tab');
            
            $('.hrm-dashboard-tab').removeClass('active');
            $(this).addClass('active');
            
            $('.hrm-dashboard-pane').removeClass('active');
            $('#' + tabId).addClass('active');
        });
    }
    
    // Load tenant details
    function loadTenantDetails() {
        $.ajax({
            url: hrm_public.ajax_url,
            type: 'POST',
            data: {
                action: 'hrm_get_tenant_details',
                nonce: hrm_public.nonce
            },
            success: function(response) {
                if (response.success) {
                    updateTenantDashboard(response.data);
                }
            }
        });
    }
    
    // Update tenant dashboard
    function updateTenantDashboard(data) {
        if (data.agreement) {
            $('#hrm-current-rent').text(formatCurrency(data.agreement.monthly_rent));
            $('#hrm-lease-end').text(data.agreement.end_date);
        }
        
        if (data.recent_payments) {
            updatePaymentsTable(data.recent_payments);
        }
        
        if (data.maintenance_requests) {
            updateMaintenanceTable(data.maintenance_requests);
        }
    }
    
    // Update payments table
    function updatePaymentsTable(payments) {
        var html = '';
        
        payments.forEach(function(payment) {
            html += '<tr>';
            html += '<td>' + payment.payment_date + '</td>';
            html += '<td>' + formatCurrency(payment.amount) + '</td>';
            html += '<td><span class="hrm-status hrm-status-' + payment.status + '">' + payment.status + '</span></td>';
            html += '<td>' + payment.payment_method + '</td>';
            html += '</tr>';
        });
        
        $('#hrm-payments-table tbody').html(html);
    }
    
    // Update maintenance table
    function updateMaintenanceTable(requests) {
        var html = '';
        
        requests.forEach(function(request) {
            html += '<tr>';
            html += '<td>' + request.request_date + '</td>';
            html += '<td>' + request.issue_type + '</td>';
            html += '<td><span class="hrm-status hrm-status-' + request.priority + '">' + request.priority + '</span></td>';
            html += '<td><span class="hrm-status hrm-status-' + request.status + '">' + request.status + '</span></td>';
            html += '</tr>';
        });
        
        $('#hrm-maintenance-table tbody').html(html);
    }
    
    // Show alert message
    function showAlert(type, message) {
        var alertClass = type === 'success' ? 'hrm-alert-success' : 'hrm-alert-error';
        var alert = $('<div class="hrm-alert ' + alertClass + '">' + message + '</div>');
        
        $('.hrm-alert-container').html(alert);
        
        setTimeout(function() {
            alert.fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
    }
    
    // Format currency
    function formatCurrency(amount) {
        return '$' + parseFloat(amount).toFixed(2);
    }
    
    // Validate email
    function validateEmail(email) {
        var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
    
    // Validate phone
    function validatePhone(phone) {
        var re = /^[\d\s\-\(\)]+$/;
        return re.test(phone);
    }
    
})(jQuery);