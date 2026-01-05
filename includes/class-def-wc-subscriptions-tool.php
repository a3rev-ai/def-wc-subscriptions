<?php
/**
 * Class DEF_WC_Subscriptions_Tool
 *
 * The WooCommerce Subscriptions module tools for Digital Employee Framework - Core.
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
 * Class DEF_WC_Subscriptions_Tool
 *
 * Extends the base tool class to provide WooCommerce Subscriptions functionality.
 *
 * @package def-wc-subscriptions
 * @since 0.1.0
 * @version 0.1.0
 */
class DEF_WC_Subscriptions_Tool extends DEF_Core_Tool_Base {

	/**
	 * Initialize the tool.
	 *
	 * @since 0.1.0
	 * @version 0.1.0
	 */
	protected function init(): void {
		$this->name    = __( 'WooCommerce Subscriptions', 'def-wc-subscriptions' );
		$this->route   = '/tools/wc/subscriptions';
		$this->methods = array( 'GET' );
		$this->module  = 'woocommerce-subscriptions';
	}

	/**
	 * Check if the tool should be registered.
	 * Only register if WooCommerce and WooCommerce Subscriptions are active.
	 *
	 * @return bool True if WooCommerce and WooCommerce Subscriptions are active, false otherwise.
	 * @since 0.2.0
	 * @version 0.2.0
	 */
	protected function should_register(): bool {
		// Check if WooCommerce is installed and active.
		if ( ! class_exists( 'WooCommerce' ) && ! function_exists( 'WC' ) ) {
			return false;
		}

		// Check if WooCommerce Subscriptions is installed and active.
		if ( ! class_exists( 'WC_Subscriptions' ) ) {
			return false;
		}

		// Check if WooCommerce Subscriptions functions are available.
		if ( ! function_exists( 'wcs_get_users_subscriptions' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Handle the request.
	 *
	 * @param \WP_REST_Request $request The request object.
	 * @return \WP_REST_Response The response object.
	 * @since 0.1.0
	 * @version 0.1.0
	 */
	public function handle_request( \WP_REST_Request $request ): \WP_REST_Response {
		$user = $this->get_current_user();
		if ( ! $user ) {
			return $this->error_response( 'Unauthorized', 401 );
		}

		if ( ! function_exists( 'wcs_get_users_subscriptions' ) ) {
			return $this->error_response( 'WooCommerce Subscriptions not active', 400 );
		}

		return DEF_WC_Subscriptions_Cache::get_or_set(
			'subscriptions',
			$user->ID,
			604800, // 7 days - subscriptions change less frequently (should be cached for a week).
			function () use ( $user ) {
				$subs = wcs_get_users_subscriptions( (int) $user->ID );
				$out  = array();
				// Statuses that count as "paid".
				$paid_statuses = array( 'completed', 'processing', 'refunded' );

				foreach ( $subs as $sub ) {
					/**
					 * Subscription object.
					 *
					 * @var WC_Subscription $sub
					 */
					$next        = $sub->get_time( 'next_payment' );
					$total_spent = 0.0;

					// Get product names from subscription items.
					$product_names = array();
					foreach ( $sub->get_items() as $item ) {
						$product_names[] = $item->get_name();
					}

					// Get parent order info.
					$parent_order_data = null;
					$parent_order_id   = $sub->get_parent_id();
					if ( $parent_order_id ) {
						$parent_order = wc_get_order( $parent_order_id );
						if ( $parent_order ) {
							$parent_status     = $parent_order->get_status();
							$parent_total      = (float) $parent_order->get_total();
							$parent_order_data = array(
								'id'     => (int) $parent_order_id,
								'status' => $parent_status,
								'date'   => $parent_order->get_date_created() ? $parent_order->get_date_created()->date( 'c' ) : null,
								'total'  => (string) $parent_total,
							);
							// Add to total spent if paid.
							if ( in_array( $parent_status, $paid_statuses, true ) ) {
								$total_spent += $parent_total;
							}
						}
					}

					// Get all renewal orders.
					$renewal_orders_data = array();
					$renewal_order_ids   = $sub->get_related_orders( 'ids', 'renewal' );
					if ( ! empty( $renewal_order_ids ) ) {
						foreach ( $renewal_order_ids as $renewal_id ) {
							$renewal_order = wc_get_order( $renewal_id );
							if ( $renewal_order ) {
								$renewal_status        = $renewal_order->get_status();
								$renewal_total         = (float) $renewal_order->get_total();
								$renewal_orders_data[] = array(
									'id'     => (int) $renewal_id,
									'status' => $renewal_status,
									'date'   => $renewal_order->get_date_created() ? $renewal_order->get_date_created()->date( 'c' ) : null,
									'total'  => (string) $renewal_total,
								);
								// Add to total spent if paid..
								if ( in_array( $renewal_status, $paid_statuses, true ) ) {
									$total_spent += $renewal_total;
								}
							}
						}
					}

					$out[] = array(
						'id'             => (int) $sub->get_id(),
						'status'         => $sub->get_status(),
						'start_date'     => $sub->get_date( 'date_created' ),
						'next_payment'   => $next ? gmdate( 'c', $next ) : null,
						'end_date'       => $sub->get_date( 'end' ) ? $sub->get_date( 'end' ) : null,
						'total'          => (string) $sub->get_total(),
						'currency'       => $sub->get_currency(),
						'products'       => $product_names,
						'parent_order'   => $parent_order_data,
						'renewal_orders' => $renewal_orders_data,
						'renewal_count'  => count( $renewal_orders_data ),
						'total_spent'    => number_format( $total_spent, 2, '.', '' ),
					);
				}
				return new \WP_REST_Response(
					array(
						'success'             => true,
						'total_subscriptions' => count( $out ),
						'subscriptions'       => $out,
					),
					200
				);
			}
		);
	}
}

/**
 * Initialize WooCommerce Subscriptions tools.
 *
 * Tools will automatically register themselves when instantiated.
 * The should_register() method handles conditional registration.
 *
 * @since 0.2.0
 * @version 0.2.0
 */
add_action(
	'plugins_loaded',
	function () {
		// Instantiate the tool - it will auto-register via base class if WooCommerce and WooCommerce Subscriptions are active.
		new DEF_WC_Subscriptions_Tool();
	},
	20 // Priority 20 to ensure main plugin is loaded first.
);
