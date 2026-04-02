<?php
/**
 * Plugin Name: Digital Employees – WooCommerce Subscriptions
 * Description: WooCommerce Subscriptions module for Digital Employee Framework - Core. Provides subscription API tools for Digital Employees.
 * Version: 1.2.1
 * Author: a3rev
 * Author URI: https://a3rev.com/
 * Text Domain: def-wc-subscriptions
 * Domain Path: /languages
 * Requires at least: 6.2
 * Requires PHP: 8.0
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package def-wc-subscriptions
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'DEF_MODULE_WC_SUBSCRIPTIONS_VERSION' ) ) {
	define( 'DEF_MODULE_WC_SUBSCRIPTIONS_VERSION', '1.2.1' );
}

define( 'DEF_MODULE_WC_SUBSCRIPTIONS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'DEF_MODULE_WC_SUBSCRIPTIONS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Check if main plugin is active.
add_action(
	'admin_notices',
	function () {
		if ( class_exists( 'DEF_Core' ) ) {
			return;
		}

		?>
		<div class="notice notice-error">
			<p><?php esc_html_e( 'Digital Employees – WooCommerce Subscriptions requires Digital Employee Framework - Core to be installed and activated.', 'def-wc-subscriptions' ); ?></p>
		</div>
		<?php
	}
);

add_action( 'def_core_inited', 'def_module_wc_subscriptions_load', 10, 0 );

/**
 * Load module files.
 */
function def_module_wc_subscriptions_load(): void {
	require_once __DIR__ . '/includes/class-def-wc-subscriptions-tool.php';
	require_once __DIR__ . '/includes/class-def-wc-subscriptions-cache.php';

	// GitHub auto-updater (uses DEF_Core_GitHub_Updater from def-core).
	if ( class_exists( 'DEF_Core_GitHub_Updater' ) ) {
		new DEF_Core_GitHub_Updater( array(
			'file'    => __FILE__,
			'repo'    => 'a3rev-ai/def-wc-subscriptions',
			'slug'    => 'def-wc-subscriptions',
			'asset'   => 'def-wc-subscriptions.zip',
			'version' => DEF_MODULE_WC_SUBSCRIPTIONS_VERSION,
		) );
	}
}
