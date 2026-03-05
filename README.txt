=== Housing Rent Management ===
Contributors: Prashant J.
Tags: rent, housing, property management, tenants, maintenance
Requires at least: 5.0
Tested up to: 6.0
Stable tag: 1.0.0
Requires PHP: 7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Complete housing rent management system with agreements, owner details, monthly rent tracking, and maintenance.

== Description ==

Housing Rent Management is a comprehensive WordPress plugin for managing rental properties. It provides everything you need to manage properties, tenants, rent agreements, payments, and maintenance requests.

= Features =

* **Property Management**: Add and manage rental properties with detailed information
* **Owner Management**: Keep track of property owners and their details
* **Tenant Management**: Manage tenant information and rental history
* **Rent Agreements**: Create and manage rental agreements with automatic agreement number generation
* **Rent Payments**: Track monthly rent payments, generate receipts
* **Maintenance Requests**: Handle maintenance requests from tenants
* **Monthly Maintenance**: Track monthly maintenance expenses
* **Dashboard**: Overview of all rental activities
* **Reports**: Generate reports on rent collection and expenses
* **Email Notifications**: Automatic notifications for payments and maintenance
* **Shortcodes**: Display property listings and tenant dashboards on frontend
* **Multi-currency Support**: Support for multiple currencies
* **Responsive Design**: Works on all devices

= Shortcodes =

* `[hrm_tenant_dashboard]` - Display tenant dashboard
* `[hrm_pay_rent]` - Display rent payment form
* `[hrm_maintenance_request]` - Display maintenance request form
* `[hrm_properties_list]` - Display list of properties
* `[hrm_rental_history]` - Display tenant rental history

== Installation ==

1. **Database Setup**:
   - Before activating the plugin, you need to create the database tables
   - Navigate to the plugin's `/sql` folder
   - Import `install.sql` into your WordPress database using phpMyAdmin or WP CLI

2. **Plugin Installation**:
   - Upload the `housing-rent-management` folder to the `/wp-content/plugins/` directory
   - Activate the plugin through the 'Plugins' menu in WordPress

3. **Configuration**:
   - Go to Housing Rent → Settings to configure plugin options
   - Set currency, date format, and notification preferences
   - Add your first property, owner, and tenant

== Frequently Asked Questions ==

= Do I need to create database tables manually? =

Yes, the plugin requires manual database table creation. Import the SQL file from the `/sql` folder before activating the plugin.

= Can tenants pay rent online? =

Yes, tenants can pay rent online using the `[hrm_pay_rent]` shortcode. The plugin supports multiple payment methods.

= Can tenants submit maintenance requests? =

Yes, tenants can submit maintenance requests using the `[hrm_maintenance_request]` shortcode.

= Does the plugin send email notifications? =

Yes, the plugin sends email notifications for payment receipts and new maintenance requests.

= Is the plugin multilingual? =

Yes, the plugin is translation-ready and comes with a .pot file for easy translation.

== Changelog ==

= 1.0.0 =
* Initial release
* Property management
* Owner management
* Tenant management
* Rent agreements
* Rent payments
* Maintenance requests
* Dashboard and reports
* Shortcodes for frontend
* Email notifications

== Upgrade Notice ==

= 1.0.0 =
Initial release.