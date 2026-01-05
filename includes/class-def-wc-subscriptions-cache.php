<?php
/**
 * Class DEF_WC_Subscriptions_Cache
 *
 * Cache handling for WooCommerce Subscriptions module.
 *
 * @package def-wc-subscriptions
 * @since 0.1.0
 * @version 0.1.0
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class DEF_WC_Subscriptions_Cache
 *
 * Cache handling for WooCommerce Subscriptions module.
 *
 * @package def-wc-subscriptions
 * @since 0.1.0
 * @version 0.1.0
 */
final class DEF_WC_Subscriptions_Cache {

	/**
	 * Initialize cache hooks.
	 *
	 * @since 0.1.0
	 * @version 0.1.0
	 */
	public static function init(): void {
		// Invalidate subscription cache when subscriptions change.
		add_action( 'woocommerce_subscription_status_updated', array( __CLASS__, 'on_subscription_changed' ), 10, 1 );
		add_action( 'woocommerce_subscription_date_updated', array( __CLASS__, 'on_subscription_changed' ), 10, 1 );
	}

	/**
	 * Get cached data or execute callback and cache the result.
	 *
	 * @param string   $cache_key Cache key prefix.
	 * @param int      $user_id User ID.
	 * @param int      $expiration Cache expiration in seconds.
	 * @param callable $callback Callback function to execute if cache miss.
	 * @return \WP_REST_Response The response object.
	 * @since 0.1.0
	 * @version 0.1.0
	 */
	public static function get_or_set( string $cache_key, int $user_id, int $expiration, callable $callback ): \WP_REST_Response {
		// Use the main plugin's cache class if available.
		if ( class_exists( 'DEF_Core_Cache' ) ) {
			return DEF_Core_Cache::get_or_set( $cache_key, $user_id, $expiration, $callback );
		}

		// Fallback: execute callback directly if main cache class not available.
		return call_user_func( $callback );
	}

	/**
	 * Handle subscription change events.
	 *
	 * @param mixed $subscription The subscription object.
	 * @since 0.1.0
	 * @version 0.1.0
	 */
	public static function on_subscription_changed( $subscription ): void {
		// Use the main plugin's cache class if available.
		if ( class_exists( 'DEF_Core_Cache' ) ) {
			if ( ! is_object( $subscription ) || ! method_exists( $subscription, 'get_user_id' ) ) {
				return;
			}
			$user_id = $subscription->get_user_id();
			if ( $user_id ) {
				DEF_Core_Cache::invalidate_user( $user_id, 'subscriptions' );
			}
		}
	}
}
