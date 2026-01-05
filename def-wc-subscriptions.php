<?php
/**
 * Plugin Name: Digital Employees – WooCommerce Subscriptions
 * Description: WooCommerce Subscriptions module for Digital Employee Framework - Core. Provides WooCommerce Subscriptions API tools.
 * Version: 1.0.0
 * Author: a3rev
 * Author URI: https://a3rev.com/
 * Text Domain: def-wc-subscriptions
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 8.0
 * Requires Plugins: def-core, woocommerce-subscriptions, woocommerce
 * Update URI: def-wc-subscriptions
 * License: This software is under commercial license and copyright to A3 Revolution Software Development team
 *
 * @package def-wc-subscriptions
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants for upgrade and metadata.
if ( ! defined( 'DEF_MODULE_WC_SUBSCRIPTIONS_PLUGIN_NAME' ) ) {
	define( 'DEF_MODULE_WC_SUBSCRIPTIONS_PLUGIN_NAME', plugin_basename( __FILE__ ) );
}
if ( ! defined( 'DEF_MODULE_WC_SUBSCRIPTIONS_KEY' ) ) {
	define( 'DEF_MODULE_WC_SUBSCRIPTIONS_KEY', 'def-wc-subscriptions' );
}
if ( ! defined( 'DEF_MODULE_WC_SUBSCRIPTIONS_VERSION' ) ) {
	define( 'DEF_MODULE_WC_SUBSCRIPTIONS_VERSION', '1.0.0' );
}

define( 'DEF_MODULE_WC_SUBSCRIPTIONS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'DEF_MODULE_WC_SUBSCRIPTIONS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Include upgrade handler (CloudFront-based auto-update like other premium plugins).
if ( file_exists( __DIR__ . '/upgrade/class-def-wc-subscriptions-upgrade.php' ) ) {
	require_once __DIR__ . '/upgrade/class-def-wc-subscriptions-upgrade.php';
}

// Require a3rev Dashboard requirement (for auto-updates & support).
if ( ! class_exists( 'a3rev_Dashboard_Plugin_Requirement' ) ) {
	require_once __DIR__ . '/a3rev-dashboard-requirement.php';
}

// Check if main plugin is active.
add_action(
	'admin_notices',
	function () {
		if ( class_exists( 'DEF_Core' ) ) {
			return;
		}

		?>
		<div class="notice notice-error">
			<p><?php esc_html_e( 'Digital Employees - WooCommerce Subscriptions requires Digital Employee Framework - Core to be installed and activated.', 'def-wc-subscriptions' ); ?></p>
		</div>
		<?php
	}
);

add_action( 'def_core_inited', 'def_module_wc_subscriptions_load', 10, 0 );

/**
 * Load modules.
 */
function def_module_wc_subscriptions_load(): void {
	// Load includes.
	require_once __DIR__ . '/includes/class-def-wc-subscriptions-tool.php';
	require_once __DIR__ . '/includes/class-def-wc-subscriptions-cache.php';
}
