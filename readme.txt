=== Digital Employees – WooCommerce Subscriptions ===
Contributors: a3rev
Tags: subscriptions, woocommerce, api, digital employee, ai
Requires at least: 6.2
Tested up to: 6.9
Requires PHP: 8.0
Stable tag: 1.2.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

WooCommerce Subscriptions module for Digital Employees. Provides subscription API tools for AI assistants.

== Description ==

This module plugin extends [Digital Employee Framework - Core](https://github.com/a3rev-ai/def-core) with WooCommerce Subscriptions integration.

When active, it registers a `/tools/wc/subscriptions` REST endpoint that returns the authenticated user's subscription data — including status, renewal history, parent orders, and total spend.

Digital Employee AI assistants use this endpoint to answer subscription-related questions across all channels (Customer Chat, Staff AI, Setup Assistant).

= Features =

* Subscription details — status, dates, pricing, products
* Parent order and full renewal history
* Total spend calculation across all orders
* 7-day intelligent cache with automatic invalidation
* JWT authentication (via def-core)

= Requirements =

* Digital Employee Framework - Core plugin
* WooCommerce plugin
* WooCommerce Subscriptions plugin

== Installation ==

1. Download the latest release from [GitHub Releases](https://github.com/a3rev-ai/def-wc-subscriptions/releases)
2. Upload via **Plugins → Add New → Upload Plugin**
3. Activate — the endpoint registers automatically when all dependencies are active

== Changelog ==

= 1.1.0 - 2026/04/02 =
* Improvement: Open-source release — removed commercial update infrastructure
* Improvement: GitHub release workflow for downloadable zip
* Fix: License updated to GPLv2 or later
* Fix: Removed a3rev Dashboard requirement

= 1.0.0 - 2026/01/02 =
* Initial release

== License ==

GPLv2 or later. See https://www.gnu.org/licenses/gpl-2.0.html
