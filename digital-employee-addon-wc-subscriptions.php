<?php
/**
 * Plugin Name: Digital Employee Add-on: WooCommerce Subscriptions
 * Description: WooCommerce Subscriptions addon for Digital Employee Framework WordPress Bridge. Provides WooCommerce Subscriptions API tools.
 * Version: 1.0.0
 * Author: a3rev
 * Author URI: https://a3rev.com/
 * Text Domain: digital-employee-addon-wc-subscriptions
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 8.0
 * Requires Plugins: digital-employee-wp-bridge, woocommerce-subscriptions, woocommerce
 * Update URI: digital-employee-addon-wc-subscriptions
 * License: This software is under commercial license and copyright to A3 Revolution Software Development team
 *
 * @package digital-employee-addon-wc-subscriptions
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants for upgrade and metadata.
if ( ! defined( 'DE_ADDON_WC_SUBSCRIPTIONS_PLUGIN_NAME' ) ) {
	define( 'DE_ADDON_WC_SUBSCRIPTIONS_PLUGIN_NAME', plugin_basename( __FILE__ ) );
}
if ( ! defined( 'DE_ADDON_WC_SUBSCRIPTIONS_KEY' ) ) {
	define( 'DE_ADDON_WC_SUBSCRIPTIONS_KEY', 'digital-employee-addon-wc-subscriptions' );
}
if ( ! defined( 'DE_ADDON_WC_SUBSCRIPTIONS_VERSION' ) ) {
	define( 'DE_ADDON_WC_SUBSCRIPTIONS_VERSION', '1.0.0' );
}

define( 'DE_ADDON_WC_SUBSCRIPTIONS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'DE_ADDON_WC_SUBSCRIPTIONS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Include upgrade handler (CloudFront-based auto-update like other premium plugins).
if ( file_exists( __DIR__ . '/upgrade/class-digital-employee-addon-wc-subscriptions-upgrade.php' ) ) {
	require_once __DIR__ . '/upgrade/class-digital-employee-addon-wc-subscriptions-upgrade.php';
}

// Require a3rev Dashboard requirement (for auto-updates & support).
if ( ! class_exists( 'a3rev_Dashboard_Plugin_Requirement' ) ) {
	require_once __DIR__ . '/a3rev-dashboard-requirement.php';
}

// Check if main plugin is active.
add_action(
	'admin_notices',
	function () {
		if ( class_exists( 'Digital_Employee_WP_Bridge' ) ) {
			return;
		}

		?>
		<div class="notice notice-error">
			<p><?php esc_html_e( 'Digital Employee Add-on: WooCommerce Subscriptions requires Digital Employee Framework - WordPress Bridge to be installed and activated.', 'digital-employee-addon-wc-subscriptions' ); ?></p>
		</div>
		<?php
	}
);

add_action( 'digital_employee_wp_bridge_inited', 'digital_employee_addon_wc_subscriptions_load_addons', 10, 0 );

/**
 * Load addons.
 */
function digital_employee_addon_wc_subscriptions_load_addons(): void {
	// Load includes.
	require_once __DIR__ . '/includes/class-digital-employee-addon-wc-subscriptions-tools.php';
	require_once __DIR__ . '/includes/class-digital-employee-addon-wc-subscriptions-cache.php';

	// Initialize cache handler.
	Digital_Employee_Addon_WC_Subscriptions_Cache::init();
}
