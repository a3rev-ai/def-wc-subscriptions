<?php
/**
 * Unit tests for DEF_WC_Subscriptions_Tool.
 *
 * @package def-wc-subscriptions/tests
 */

declare(strict_types=1);

use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Test subclass that exposes protected methods for testing.
 */
class Testable_WC_Subscriptions_Tool extends DEF_WC_Subscriptions_Tool {
    public function test_should_register(): bool {
        return $this->should_register();
    }
}

class ToolTest extends TestCase {

    private Testable_WC_Subscriptions_Tool $tool;

    protected function set_up(): void {
        parent::set_up();
        DEF_Core_Tool_Base::$test_current_user = null;
        DEF_Core_Cache::reset();
        $this->tool = new Testable_WC_Subscriptions_Tool();
    }

    // ── init() tests ────────────────────────────────────────────────

    public function test_init_sets_correct_route(): void {
        $this->assertSame( '/tools/wc/subscriptions', $this->tool->get_route() );
    }

    public function test_init_sets_correct_name(): void {
        $this->assertSame( 'WooCommerce Subscriptions', $this->tool->get_name() );
    }

    public function test_init_sets_correct_module(): void {
        $this->assertSame( 'woocommerce-subscriptions', $this->tool->get_module() );
    }

    public function test_init_sets_get_method(): void {
        $this->assertSame( array( 'GET' ), $this->tool->get_methods() );
    }

    // ── should_register() tests ─────────────────────────────────────

    public function test_should_register_false_without_dependencies(): void {
        $this->assertFalse( $this->tool->test_should_register() );
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_should_register_true_when_all_present(): void {
        if ( ! class_exists( 'WooCommerce' ) ) {
            eval( 'class WooCommerce {}' );
        }
        if ( ! class_exists( 'WC_Subscriptions' ) ) {
            eval( 'class WC_Subscriptions {}' );
        }
        if ( ! function_exists( 'wcs_get_users_subscriptions' ) ) {
            eval( 'function wcs_get_users_subscriptions( $user_id ) { return array(); }' );
        }
        $tool = new Testable_WC_Subscriptions_Tool();
        $this->assertTrue( $tool->test_should_register() );
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_should_register_false_with_woocommerce_but_no_subscriptions(): void {
        if ( ! class_exists( 'WooCommerce' ) ) {
            eval( 'class WooCommerce {}' );
        }
        $tool = new Testable_WC_Subscriptions_Tool();
        $this->assertFalse( $tool->test_should_register() );
    }

    /**
     * Test the WC() function fallback path — WooCommerce class absent but WC() function exists.
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_should_register_true_via_wc_function_fallback(): void {
        // No WooCommerce class — but WC() function exists (alternative bootstrap).
        if ( ! function_exists( 'WC' ) ) {
            eval( 'function WC() { return new stdClass(); }' );
        }
        if ( ! class_exists( 'WC_Subscriptions' ) ) {
            eval( 'class WC_Subscriptions {}' );
        }
        if ( ! function_exists( 'wcs_get_users_subscriptions' ) ) {
            eval( 'function wcs_get_users_subscriptions( $user_id ) { return array(); }' );
        }
        $tool = new Testable_WC_Subscriptions_Tool();
        $this->assertTrue( $tool->test_should_register() );
    }

    // ── handle_request() — auth and error paths ─────────────────────

    public function test_handle_request_returns_401_without_user(): void {
        DEF_Core_Tool_Base::$test_current_user = null;
        $response = $this->tool->handle_request( new WP_REST_Request() );
        $this->assertSame( 401, $response->get_status() );
        $data = $response->get_data();
        $this->assertTrue( $data['error'] );
        $this->assertSame( 'Unauthorized', $data['message'] );
    }

    public function test_handle_request_returns_400_without_wcs_function(): void {
        DEF_Core_Tool_Base::$test_current_user = (object) array( 'ID' => 1 );
        $response = $this->tool->handle_request( new WP_REST_Request() );
        $this->assertSame( 400, $response->get_status() );
        $data = $response->get_data();
        $this->assertTrue( $data['error'] );
        $this->assertSame( 'WooCommerce Subscriptions not active', $data['message'] );
    }

    // ── handle_request() — happy path with data shaping ─────────────

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_handle_request_success_with_subscriptions(): void {
        require_once __DIR__ . '/stubs/wc-stubs-happy.php';

        DEF_Core_Tool_Base::$test_current_user = (object) array( 'ID' => 42 );
        $tool = new Testable_WC_Subscriptions_Tool();
        $response = $tool->handle_request( new WP_REST_Request() );

        $this->assertSame( 200, $response->get_status() );
        $data = $response->get_data();
        $this->assertTrue( $data['success'] );
        $this->assertSame( 1, $data['total_subscriptions'] );

        $sub = $data['subscriptions'][0];
        $this->assertSame( 100, $sub['id'] );
        $this->assertSame( 'active', $sub['status'] );
        $this->assertSame( '29.99', $sub['total'] );
        $this->assertSame( 'USD', $sub['currency'] );
        $this->assertSame( array( 'Premium Plan' ), $sub['products'] );

        // Parent order.
        $this->assertNotNull( $sub['parent_order'] );
        $this->assertSame( 200, $sub['parent_order']['id'] );
        $this->assertSame( 'completed', $sub['parent_order']['status'] );
        $this->assertSame( '29.99', $sub['parent_order']['total'] );

        // Renewal orders.
        $this->assertCount( 2, $sub['renewal_orders'] );
        $this->assertSame( 301, $sub['renewal_orders'][0]['id'] );
        $this->assertSame( 'completed', $sub['renewal_orders'][0]['status'] );
        $this->assertSame( 302, $sub['renewal_orders'][1]['id'] );
        $this->assertSame( 'processing', $sub['renewal_orders'][1]['status'] );
        $this->assertSame( 2, $sub['renewal_count'] );

        // Total spent: parent 29.99 + renewal completed 29.99 + renewal processing 29.99 = 89.97.
        $this->assertSame( '89.97', $sub['total_spent'] );
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_handle_request_empty_subscriptions(): void {
        if ( ! function_exists( 'wcs_get_users_subscriptions' ) ) {
            eval( 'function wcs_get_users_subscriptions( $uid ) { return array(); }' );
        }

        DEF_Core_Tool_Base::$test_current_user = (object) array( 'ID' => 1 );
        $tool = new Testable_WC_Subscriptions_Tool();
        $response = $tool->handle_request( new WP_REST_Request() );

        $this->assertSame( 200, $response->get_status() );
        $data = $response->get_data();
        $this->assertTrue( $data['success'] );
        $this->assertSame( 0, $data['total_subscriptions'] );
        $this->assertSame( array(), $data['subscriptions'] );
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_handle_request_refunded_order_counts_in_total_spent(): void {
        require_once __DIR__ . '/stubs/wc-stubs-refund.php';

        DEF_Core_Tool_Base::$test_current_user = (object) array( 'ID' => 42 );
        $tool = new Testable_WC_Subscriptions_Tool();
        $response = $tool->handle_request( new WP_REST_Request() );

        $sub = $response->get_data()['subscriptions'][0];
        // Parent completed 29.99 + renewal refunded 29.99 = 59.98.
        $this->assertSame( '59.98', $sub['total_spent'] );
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_handle_request_failed_order_excluded_from_total_spent(): void {
        require_once __DIR__ . '/stubs/wc-stubs-failed.php';

        DEF_Core_Tool_Base::$test_current_user = (object) array( 'ID' => 42 );
        $tool = new Testable_WC_Subscriptions_Tool();
        $response = $tool->handle_request( new WP_REST_Request() );

        $sub = $response->get_data()['subscriptions'][0];
        // Parent completed 29.99 only — failed renewal excluded.
        $this->assertSame( '29.99', $sub['total_spent'] );
    }

    /**
     * Test defensive branches: wc_get_order() returns null for a renewal,
     * and get_date_created() returns null on the parent order.
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_handle_request_missing_order_and_null_date(): void {
        require_once __DIR__ . '/stubs/wc-stubs-nulls.php';

        DEF_Core_Tool_Base::$test_current_user = (object) array( 'ID' => 42 );
        $tool = new Testable_WC_Subscriptions_Tool();
        $response = $tool->handle_request( new WP_REST_Request() );

        $this->assertSame( 200, $response->get_status() );
        $data = $response->get_data();
        $sub  = $data['subscriptions'][0];

        // Parent order has null date.
        $this->assertNotNull( $sub['parent_order'] );
        $this->assertNull( $sub['parent_order']['date'] );
        $this->assertSame( '29.99', $sub['parent_order']['total'] );

        // Renewal 301 returned null from wc_get_order — should be skipped entirely.
        // Only renewal 302 (completed, 15.00) should appear.
        $this->assertCount( 1, $sub['renewal_orders'] );
        $this->assertSame( 302, $sub['renewal_orders'][0]['id'] );

        // Total spent: parent completed 29.99 + renewal 302 completed 15.00 = 44.99.
        $this->assertSame( '44.99', $sub['total_spent'] );
    }

    // ── Cache delegation ────────────────────────────────────────────

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_handle_request_delegates_to_cache(): void {
        if ( ! function_exists( 'wcs_get_users_subscriptions' ) ) {
            eval( 'function wcs_get_users_subscriptions( $uid ) { return array(); }' );
        }

        DEF_Core_Tool_Base::$test_current_user = (object) array( 'ID' => 7 );
        $tool = new Testable_WC_Subscriptions_Tool();
        $tool->handle_request( new WP_REST_Request() );

        $this->assertCount( 1, DEF_Core_Cache::$get_or_set_calls );
        $call = DEF_Core_Cache::$get_or_set_calls[0];
        $this->assertSame( 'subscriptions', $call['key'] );
        $this->assertSame( 7, $call['user_id'] );
        $this->assertSame( 604800, $call['expiration'] );
    }

    // ── Hook registration (strict callback wiring) ──────────────────

    public function test_init_hook_wired_correctly(): void {
        global $_test_actions;
        $found = false;
        foreach ( $_test_actions as $action ) {
            if ( 'init' === $action['hook']
                && 20 === $action['priority']
                && is_callable( $action['callback'] )
                && 1 === $action['accepted_args']
            ) {
                $found = true;
                break;
            }
        }
        $this->assertTrue( $found, 'Tool should register a callable on init at priority 20 with accepted_args 1' );
    }
}
