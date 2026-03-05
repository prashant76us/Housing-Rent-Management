/**
 * Housing Rent Management Admin Scripts
 */

(function($) {
    'use strict';
    
    // Initialize on document ready
    $(document).ready(function() {
        initDashboard();
        initForms();
        initModals();
        initTabs();
        initSearch();
        initDatepickers();
        loadDashboardStats();
    });
    
    // Initialize dashboard
    function initDashboard() {
        // Refresh stats every 30 seconds
        setInterval(loadDashboardStats, 30000);
    }
    
    // Initialize forms
    function initForms() {
        // Property form
        $('#hrm-add-property-form').on('submit', function(e) {
            e.preventDefault();
            submitForm($(this), 'hrm_add_property');
        });
        
        // Owner form
        $('#hrm-add-owner-form').on('submit', function(e) {
            e.preventDefault();
            submitForm($(this), 'hrm_add_owner');
        });
        
        // Tenant form
        $('#hrm-add-tenant-form').on('submit', function(e) {
            e.preventDefault();
            submitForm($(this), 'hrm_add_tenant');
        });
        
        // Agreement form
        $('#hrm-add-agreement-form').on('submit', function(e) {
            e.preventDefault();
            submitForm($(this), 'hrm_add_agreement');
        });
        
        // Payment form
        $('#hrm-add-payment-form').on('submit', function(e) {
            e.preventDefault();
            submitForm($(this), 'hrm_add_payment');
        });
    }
    
    // Submit form via AJAX
    function submitForm(form, action) {
        var formData = new FormData(form[0]);
        formData.append('action', action);
        formData.append('nonce', hrm_ajax.nonce);
        
        $.ajax({
            url: hrm_ajax.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                form.find('button[type="submit"]').prop('disabled', true).text('Saving...');
            },
            success: function(response) {
                if (response.success) {
                    showNotice('success', response.data.message);
                    form[0].reset();
                    
                    // Close modal if open
                    $('.hrm-modal').hide();
                    
                    // Refresh table if exists
                    if (typeof refreshTable !== 'undefined') {
                        refreshTable();
                    }
                    
                    // Reload stats
                    loadDashboardStats();
                } else {
                    showNotice('error', response.data);
                }
            },
            error: function() {
                showNotice('error', 'An error occurred. Please try again.');
            },
            complete: function() {
                form.find('button[type="submit"]').prop('disabled', false).text('Save');
            }
        });
    }
    
    // Initialize modals
    function initModals() {
        // Open modal
        $('.hrm-open-modal').on('click', function(e) {
            e.preventDefault();
            var modalId = $(this).data('modal');
            $('#' + modalId).show();
        });
        
        // Close modal
        $('.hrm-modal-close').on('click', function() {
            $(this).closest('.hrm-modal').hide();
        });
        
        // Close on outside click
        $(window).on('click', function(e) {
            if ($(e.target).hasClass('hrm-modal')) {
                $('.hrm-modal').hide();
            }
        });
    }
    
    // Initialize tabs
    function initTabs() {
        $('.hrm-tab-button').on('click', function() {
            var tabId = $(this).data('tab');
            
            $('.hrm-tab-button').removeClass('active');
            $(this).addClass('active');
            
            $('.hrm-tab-pane').removeClass('active');
            $('#' + tabId).addClass('active');
        });
    }
    
    // Initialize search
    function initSearch() {
        var searchTimer;
        
        $('.hrm-search-input').on('keyup', function() {
            clearTimeout(searchTimer);
            var input = $(this);
            var table = input.data('table');
            
            searchTimer = setTimeout(function() {
                performSearch(input.val(), table);
            }, 500);
        });
    }
    
    // Perform search
    function performSearch(term, table) {
        $.ajax({
            url: hrm_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'hrm_search',
                term: term,
                table: table,
                nonce: hrm_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    updateTable(response.data);
                }
            }
        });
    }
    
    // Initialize datepickers
    function initDatepickers() {
        $('.hrm-datepicker').datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true
        });
    }
    
    // Load dashboard statistics
    function loadDashboardStats() {
        $.ajax({
            url: hrm_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'hrm_get_stats',
                nonce: hrm_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    updateDashboardStats(response.data);
                }
            }
        });
    }
    
    // Update dashboard statistics
    function updateDashboardStats(stats) {
        $.each(stats, function(key, value) {
            var element = $('#hrm-stat-' + key);
            if (element.length) {
                if (key === 'monthly_rent_collected' && value) {
                    element.text(formatCurrency(value));
                } else {
                    element.text(value || '0');
                }
            }
        });
    }
    
    // Format currency
    function formatCurrency(amount) {
        return '$' + parseFloat(amount).toFixed(2);
    }
    
    // Show notice
    function showNotice(type, message) {
        var noticeClass = type === 'success' ? 'notice-success' : 'notice-error';
        var notice = $('<div class="notice ' + noticeClass + ' is-dismissible"><p>' + message + '</p></div>');
        
        $('.hrm-notices').append(notice);
        
        setTimeout(function() {
            notice.fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
    }
    
    // Update table with search results
    function updateTable(data) {
        // Implement table update logic based on your table structure
        console.log('Table update received:', data);
    }
    
    // Delete item with confirmation
    window.hrmDeleteItem = function(itemId, itemType) {
        if (confirm('Are you sure you want to delete this item?')) {
            $.ajax({
                url: hrm_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'hrm_delete_item',
                    item_id: itemId,
                    item_type: itemType,
                    nonce: hrm_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        showNotice('success', 'Item deleted successfully');
                        location.reload();
                    } else {
                        showNotice('error', response.data);
                    }
                }
            });
        }
    };
    
})(jQuery);