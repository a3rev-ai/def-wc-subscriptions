=== Digital Employees - WooCommerce Subscriptions ===
Contributors: a3rev
Tags: subscriptions, woocommerce, api, digital employee, ai, rest api
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 8.0
Stable tag: 1.0.0
License: Commercial
License URI: https://a3rev.com/

Extends Digital Employee Framework with WooCommerce Subscriptions integration for subscription management via API.

== Description ==

Digital Employees - WooCommerce Subscriptions extends the Digital Employee Framework - Core plugin with comprehensive subscription management capabilities for WooCommerce Subscriptions.

This module enables Digital Employee AI applications to search subscriptions, retrieve subscription details, order history, and manage subscription-related operations through secure REST API endpoints.

= Features =

* **Subscription Search** - Search subscriptions by keyword (email, subscription ID, order ID)
* **Subscription Details** - Get comprehensive subscription information
* **Order History** - Access related orders and renewal history
* **Product Information** - Get subscription product details
* **Status Management** - View subscription status and dates
* **Intelligent Caching** - Built-in caching for improved performance
* **Security** - JWT authentication required for all endpoints

= API Endpoints =

All endpoints require JWT authentication and are available under `/wp-json/a3-ai/v1/`:

* `GET /wc-subscriptions/search` - Search subscriptions by keyword (email, ID, order)
* `GET /wc-subscriptions/subscriptions/{id}` - Get specific subscription details with orders

= Requirements =

* Digital Employee Framework - Core (main plugin)
* WooCommerce plugin installed and activated
* WooCommerce Subscriptions plugin installed and activated
* PHP 8.0 or higher
* WordPress 6.0 or higher

= Use Cases =

* Allow Digital Employee to search customer subscriptions
* Enable AI-powered subscription support and troubleshooting
* Integrate subscription management with external applications
* Provide subscription information to Digital Employee
* Automate subscription support inquiries
* Access renewal history and payment information

= Developer Features =

* Clean, modern PHP 8.0+ code
* Strict typing throughout
* PSR-4 compatible structure
* Comprehensive caching system
* WordPress coding standards compliant

== Installation ==

= Requirements Check =

Before installing, ensure you have:
1. Digital Employee Framework - Core plugin installed and activated
2. WooCommerce plugin installed and activated
3. WooCommerce Subscriptions plugin installed and activated

= Automatic Installation =

1. Log in to your WordPress admin panel
2. Navigate to Plugins > Add New
3. Upload the module zip file
4. Click "Install Now"
5. Activate the plugin

= Manual Installation =

1. Ensure required plugins are installed and activated
2. Upload `def-wc-subscriptions` folder to `/wp-content/plugins/`
3. Activate through the 'Plugins' menu in WordPress
4. API tools will automatically register with the bridge plugin

= Configuration =

No additional configuration required. The module automatically registers its API endpoints with the main bridge plugin.

To enable/disable specific endpoints:
1. Navigate to **Settings > Digital Employees**
2. Scroll to **API Tools** section
3. Toggle individual subscription endpoints on/off

== Frequently Asked Questions ==

= Do I need the main bridge plugin? =

Yes. This module requires Digital Employee Framework - Core to function.

= What version of WooCommerce Subscriptions is supported? =

This module supports WooCommerce Subscriptions 2.0 and higher.

= Are the API endpoints secure? =

Yes. All endpoints require JWT authentication from the main bridge plugin.

= Does this module affect store performance? =

No. The module includes intelligent caching to minimize performance impact.

= Can I search subscriptions by customer email? =

Yes. The search endpoint supports searching by email, subscription ID, and order ID.

= Can Digital Employee modify subscriptions? =

Currently, the module provides read-only access to subscription information. Modification capabilities are planned for future releases.

= Does this respect WooCommerce permissions? =

Yes. The module respects WordPress and WooCommerce user capabilities and permissions.

= Can I access renewal history? =

Yes. The subscription details endpoint includes related orders and renewal history.

== API Documentation ==

= Search Subscriptions =

**Endpoint**: `GET /wp-json/a3-ai/v1/wc-subscriptions/search`

**Parameters**:
- `keyword` (required) - Search term (email, subscription ID, or order ID)

**Response**: Array of subscriptions with:
- Subscription ID, status, type
- Customer information (name, email)
- Billing details
- Start, trial, next payment, and end dates
- Total amount and currency
- Product names
- Order IDs
- Created and modified timestamps

= Get Subscription Details =

**Endpoint**: `GET /wp-json/a3-ai/v1/wc-subscriptions/subscriptions/{id}`

**Parameters**:
- `id` (required) - Subscription ID

**Response**: Complete subscription details with:
- All subscription information
- Array of related orders with:
  - Order ID, status, total
  - Order date
  - Payment method
  - Order type (parent, renewal, switch, etc.)
- Product details
- Customer data
- Billing and shipping information
- Payment and renewal dates

== Changelog ==

= 1.0.0 - 2026-01-02 =
* Initial release
* Subscription search endpoint with keyword filtering
* Subscription details endpoint with order history
* Customer and billing information
* Related orders and renewals
* Intelligent caching system
* Full JWT authentication integration

== Upgrade Notice ==

= 1.0.0 =
Initial release of Digital Employees - WooCommerce Subscriptions.

== Additional Info ==

**Support**: For support inquiries, please visit [a3rev.com](https://a3rev.com/)

**Main Plugin**: Requires Digital Employee Framework - Core

**Required Plugins**: WooCommerce, WooCommerce Subscriptions

**Documentation**: API documentation available in plugin settings

== Privacy Policy ==

This module does not collect any additional personal data beyond what WooCommerce and WooCommerce Subscriptions already collect. All API responses respect WooCommerce permissions and privacy settings.

== License ==

This software is under commercial license and copyright to A3 Revolution Software Development team.
