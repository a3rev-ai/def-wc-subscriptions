# Digital Employees - WooCommerce Subscriptions

## Description

This is a module plugin for **Digital Employee Framework - Core** that provides WooCommerce Subscriptions integration. It adds API tools for retrieving user subscriptions from WooCommerce Subscriptions.

## Requirements

- WordPress 6.0 or higher
- PHP 8.0 or higher
- **Digital Employee Framework - Core** plugin (must be installed and activated)
- WooCommerce plugin (must be installed and activated)
- WooCommerce Subscriptions plugin (optional - tools only register if WooCommerce Subscriptions is active)

## Installation

1. Upload the `def-wc-subscriptions` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Ensure **Digital Employee Framework - Core** is installed and activated
4. Ensure WooCommerce is installed and activated
5. Ensure WooCommerce Subscriptions is installed and activated (if you want to use subscription functionality)

## Features

### API Tools

- **WooCommerce Subscriptions** (`/tools/wc/subscriptions`)
  - Retrieves user's active subscriptions from WooCommerce Subscriptions
  - Includes subscription details, product names, payment dates, renewal orders, and total spent
  - Supports all subscription statuses
  - Includes parent order and renewal order information
  - Results are cached for 7 days

### Cache Management

- Automatically invalidates cache when:
  - Subscription status is updated
  - Subscription dates are updated

## API Endpoint

### GET `/tools/wc/subscriptions`

Retrieves subscriptions for the authenticated user.

**Parameters:**
- None

**Response:**
```json
{
  "success": true,
  "total_subscriptions": 1,
  "subscriptions": [
    {
      "id": 123,
      "status": "active",
      "start_date": "2024-01-01 12:00:00",
      "next_payment": "2024-02-01T12:00:00+00:00",
      "end_date": null,
      "total": "29.99",
      "currency": "USD",
      "products": ["Premium Plan"],
      "parent_order": {
        "id": 456,
        "status": "completed",
        "date": "2024-01-01T12:00:00+00:00",
        "total": "29.99"
      },
      "renewal_orders": [
        {
          "id": 789,
          "status": "completed",
          "date": "2024-02-01T12:00:00+00:00",
          "total": "29.99"
        }
      ],
      "renewal_count": 1,
      "total_spent": "59.98"
    }
  ]
}
```

## Integration with Main Plugin

This module registers its tools via the `def_core_register_tools` action hook, which is called by the main plugin during initialization. The tools will automatically appear in the main plugin's admin settings page where they can be enabled/disabled.

## Development

### File Structure

```
def-wc-subscriptions/
├── def-wc-subscriptions.php  # Main plugin file
├── README.md                                      # This file
└── includes/
    ├── class-def-wc-subscriptions-tool.php   # Tools implementation
    └── class-def-wc-subscriptions-cache.php  # Cache handling
```

### Extending

To add more subscription-related tools, extend the `DEF_Module_WC_Subscriptions_Tools` class and register additional tools.

## Changelog

### 0.1.0
- Initial release
- WooCommerce Subscriptions API tool
- Cache management for subscription data

## License

Same as Digital Employee Framework - Core

