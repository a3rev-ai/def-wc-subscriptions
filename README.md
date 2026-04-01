# Digital Employees – WooCommerce Subscriptions

A module plugin for [Digital Employees](https://github.com/a3rev-ai/def-core) that adds WooCommerce Subscriptions integration. Provides subscription API tools for Digital Employee AI assistants.

[![Download Plugin](https://img.shields.io/badge/Download_Plugin-v1.2.0-blue?style=for-the-badge&logo=wordpress)](https://github.com/a3rev-ai/def-wc-subscriptions/releases/download/v1.2.0/def-wc-subscriptions.zip) [![License: GPL v2+](https://img.shields.io/badge/License-GPL_v2+-green?style=for-the-badge)](https://www.gnu.org/licenses/gpl-2.0.html) [![WordPress 6.2+](https://img.shields.io/badge/WordPress-6.2+-21759b?style=for-the-badge&logo=wordpress)](https://wordpress.org/)

## Requirements

- [Digital Employee Framework - Core](https://github.com/a3rev-ai/def-core) (active)
- [WooCommerce](https://woocommerce.com/) (active)
- [WooCommerce Subscriptions](https://woocommerce.com/products/woocommerce-subscriptions/) (active)
- WordPress 6.2+ / PHP 8.0+

## Installation

1. Download the latest release zip from the button above
2. In WordPress admin: **Plugins → Add New → Upload Plugin** → select the zip
3. Activate the plugin
4. The `/tools/wc/subscriptions` endpoint registers automatically when all dependencies are active

## What It Does

When a customer interacts with a Digital Employee (via Customer Chat, Staff AI, or any channel), the AI can retrieve their WooCommerce subscription data:

- Active subscriptions with status, dates, and pricing
- Parent order and full renewal history
- Total spend across all subscription orders
- Product names and currency

Results are cached for 7 days with automatic invalidation on subscription status changes, date updates, renewals, and payment events.

## API Endpoint

### `GET /wp-json/a3-ai/v1/tools/wc/subscriptions`

Returns subscriptions for the authenticated user (JWT required).

```json
{
  "success": true,
  "total_subscriptions": 1,
  "subscriptions": [
    {
      "id": 123,
      "status": "active",
      "start_date": "2026-01-01 12:00:00",
      "next_payment": "2026-02-01T12:00:00+00:00",
      "total": "29.99",
      "currency": "USD",
      "products": ["Premium Plan"],
      "parent_order": { "id": 456, "status": "completed", "total": "29.99" },
      "renewal_orders": [{ "id": 789, "status": "completed", "total": "29.99" }],
      "renewal_count": 1,
      "total_spent": "59.98"
    }
  ]
}
```

## Why a Separate Plugin?

WooCommerce Subscriptions is a commercial plugin that not all def-core users will have. Per the [Module Development Guide](https://github.com/a3rev-ai/def-core/blob/main/MODULE_DEVELOPMENT.md), integrations for commercial plugins are built as separate modules rather than bundled into def-core.

This plugin follows the same `DEF_Core_Tool_Base` pattern as def-core's built-in tools — it just ships independently.

## Development

See the [Module Development Guide](https://github.com/a3rev-ai/def-core/blob/main/MODULE_DEVELOPMENT.md) for the full module architecture, base class API, and conventions.

## License

GPLv2 or later. See [LICENSE](LICENSE).
