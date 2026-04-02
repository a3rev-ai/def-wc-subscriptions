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
    // Note: We defined WooCommerce, WC_Subscriptions, and wcs_get_users_subscriptions
    // are NOT defined in bootstrap, so should_register returns false.
    // We test the "all present" case in a separate process.

    public function test_should_register_false_without_dependencies(): void {
        // No WooCommerce, WC_Subscriptions, or wcs_get_users_subscriptions defined.
        $this->assertFalse( $this->tool->test_should_register() );
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_should_register_true_when_all_present(): void {
        // Define all required stubs.
        // phpcs:disable
        if ( ! class_exists( 'WooCommerce' ) ) {
            eval( 'class WooCommerce {}' );
        }
        if ( ! class_exists( 'WC_Subscriptions' ) ) {
            eval( 'class WC_Subscriptions {}' );
        }
        if ( ! function_exists( 'wcs_get_users_subscriptions' ) ) {
            eval( 'function wcs_get_users_subscriptions( $user_id ) { return array(); }' );
        }
        // phpcs:enable

        $tool = new Testable_WC_Subscriptions_Tool();
        $this->assertTrue( $tool->test_should_register() );
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_should_register_false_with_woocommerce_but_no_subscriptions(): void {
        if ( ! class_exists( 'WooCommerce' ) ) {
            eval( 'class WooCommerce {}' ); // phpcs:ignore
        }
        // WC_Subscriptions NOT defined.

        $tool = new Testable_WC_Subscriptions_Tool();
        $this->assertFalse( $tool->test_should_register() );
    }

    // ── handle_request() tests ──────────────────────────────────────

    public function test_handle_request_returns_401_without_user(): void {
        DEF_Core_Tool_Base::$test_current_user = null;
        $request  = new WP_REST_Request();
        $response = $this->tool->handle_request( $request );
        $this->assertSame( 401, $response->get_status() );
        $data = $response->get_data();
        $this->assertTrue( $data['error'] );
        $this->assertSame( 'Unauthorized', $data['message'] );
    }

    // ── Hook registration tests ─────────────────────────────────────

    public function test_plugins_loaded_hook_registered(): void {
        global $_test_actions;
        $found = false;
        foreach ( $_test_actions as $action ) {
            if ( 'plugins_loaded' === $action['hook'] && 20 === $action['priority'] ) {
                $found = true;
                break;
            }
        }
        $this->assertTrue( $found, 'Tool should register on plugins_loaded at priority 20' );
    }
}
