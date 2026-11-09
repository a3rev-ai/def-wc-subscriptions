=== Widrow AI – WooCommerce Subscriptions ===
Contributors: a3rev
Tags: subscriptions, woocommerce, api, digital employee, ai
Requires at least: 6.2
Tested up to: 7.0
Requires PHP: 8.0
WC tested up to: 10.6
Stable tag: 1.3.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

WooCommerce Subscriptions module for Widrow AI. Provides subscription API tools for AI assistants.

== Description ==

This module plugin extends [Widrow AI](https://github.com/a3rev-ai/widrow) with WooCommerce Subscriptions integration.

When active, it registers a `/tools/wc/subscriptions` REST endpoint that returns the authenticated user's subscription data — including status, renewal history, parent orders, and total spend.

Digital Employee AI assistants use this endpoint to answer subscription-related questions across all channels (Customer Chat, Staff AI, Setup Assistant).

= Features =

* Subscription details — status, dates, pricing, products
* Parent order and full renewal history
* Total spend calculation across all orders
* 7-day intelligent cache with automatic invalidation
* JWT authentication (via Widrow AI)

= Requirements =

* Widrow AI plugin, 10.0.0 or later
* WooCommerce plugin
* WooCommerce Subscriptions plugin

== Installation ==

1. Download the latest release from [GitHub Releases](https://github.com/a3rev-ai/def-wc-subscriptions/releases)
2. Upload via **Plugins → Add New → Upload Plugin**
3. Activate — the endpoint registers automatically when all dependencies are active

== Changelog ==

= 1.3.0 - 2026/11/09 =
* This release has compatibility with Widrow AI 10.0.0, the renamed plugin. Install Widrow AI 10.0.0 first, then update this plugin.
* Tweak - Follows the Widrow AI plugin's code rename (10.0.0): new hook and class names, requires Widrow AI. No change to what the tool does.
* Tweak - The module is now named Widrow AI – WooCommerce Subscriptions, following the plugin it extends.

= 1.2.3 - 2026/04/15 =
* This maintenance release has compatibility with WordPress 7.0, WooCommerce 10.6
* Tweak - Tested for compatibility with WordPress 7.0
* Tweak - Tested for compatibility with WooCommerce 10.6
* Tweak - Improved plugin initialization sequence for better reliability

= 1.2.2 - 2026/04/02 =
* Fix: Updated Tested up to 6.9.4 — WordPress update page now shows "100% compatible" instead of "Unknown"

= 1.1.0 - 2026/04/02 =
* Improvement: Open-source release — removed commercial update infrastructure
* Improvement: GitHub release workflow for downloadable zip
* Fix: License updated to GPLv2 or later
* Fix: Removed a3rev Dashboard requirement

= 1.0.0 - 2026/01/02 =
* Initial release

== Upgrade Notice ==

= 1.3.0 =
This release requires Widrow AI 10.0.0, the renamed plugin. Install Widrow AI 10.0.0 first, then update this plugin.

= 1.2.3 =
This maintenance release has compatibility with WordPress 7.0, WooCommerce 10.6.

== License ==

GPLv2 or later. See https://www.gnu.org/licenses/gpl-2.0.html
