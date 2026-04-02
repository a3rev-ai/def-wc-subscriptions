<?php
/**
 * Unit tests for DEF_WC_Subscriptions_Cache.
 *
 * @package def-wc-subscriptions/tests
 */

declare(strict_types=1);

use Yoast\PHPUnitPolyfills\TestCases\TestCase;

class CacheTest extends TestCase {

    protected function set_up(): void {
        parent::set_up();
        DEF_Core_Cache::reset();
    }

    // ── get_or_set() tests ──────────────────────────────────────────

    public function test_get_or_set_delegates_to_core_cache(): void {
        $called = false;
        $callback = function () use ( &$called ) {
            $called = true;
            return new WP_REST_Response( array( 'success' => true ), 200 );
        };

        DEF_WC_Subscriptions_Cache::get_or_set( 'subscriptions', 42, 604800, $callback );

        // Verify DEF_Core_Cache::get_or_set was called with correct args.
        $this->assertCount( 1, DEF_Core_Cache::$get_or_set_calls );
        $call = DEF_Core_Cache::$get_or_set_calls[0];
        $this->assertSame( 'subscriptions', $call['key'] );
        $this->assertSame( 42, $call['user_id'] );
        $this->assertSame( 604800, $call['expiration'] );
        // The stub executes the callback.
        $this->assertTrue( $called );
    }

    public function test_get_or_set_returns_response(): void {
        $response = DEF_WC_Subscriptions_Cache::get_or_set(
            'subscriptions',
            1,
            3600,
            function () {
                return new WP_REST_Response( array( 'success' => true, 'count' => 5 ), 200 );
            }
        );

        $this->assertInstanceOf( WP_REST_Response::class, $response );
        $this->assertSame( 200, $response->get_status() );
        $data = $response->get_data();
        $this->assertTrue( $data['success'] );
        $this->assertSame( 5, $data['count'] );
    }

    // ── on_subscription_changed() tests ─────────────────────────────

    public function test_on_subscription_changed_invalidates_user_cache(): void {
        // Create a mock subscription with get_user_id().
        $subscription = new class {
            public function get_user_id(): int {
                return 99;
            }
        };

        DEF_WC_Subscriptions_Cache::on_subscription_changed( $subscription );

        $this->assertCount( 1, DEF_Core_Cache::$invalidate_calls );
        $call = DEF_Core_Cache::$invalidate_calls[0];
        $this->assertSame( 99, $call['user_id'] );
        $this->assertSame( 'subscriptions', $call['prefix'] );
    }

    public function test_on_subscription_changed_ignores_non_object(): void {
        DEF_WC_Subscriptions_Cache::on_subscription_changed( 'not an object' );
        $this->assertCount( 0, DEF_Core_Cache::$invalidate_calls );
    }

    public function test_on_subscription_changed_ignores_object_without_method(): void {
        $obj = new \stdClass();
        DEF_WC_Subscriptions_Cache::on_subscription_changed( $obj );
        $this->assertCount( 0, DEF_Core_Cache::$invalidate_calls );
    }

    public function test_on_subscription_changed_ignores_zero_user_id(): void {
        $subscription = new class {
            public function get_user_id(): int {
                return 0;
            }
        };

        DEF_WC_Subscriptions_Cache::on_subscription_changed( $subscription );
        $this->assertCount( 0, DEF_Core_Cache::$invalidate_calls );
    }

    // ── Hook registration tests ─────────────────────────────────────

    public function test_cache_hooks_registered(): void {
        global $_test_actions;
        $hooks = array_column( $_test_actions, 'hook' );
        $this->assertContains( 'woocommerce_subscription_status_updated', $hooks );
        $this->assertContains( 'woocommerce_subscription_date_updated', $hooks );
    }
}
