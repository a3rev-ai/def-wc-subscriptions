<?php
/**
 * PHPUnit bootstrap for def-wc-subscriptions unit tests.
 *
 * Defines WordPress and def-core stubs so tests run without
 * a full WordPress environment.
 *
 * @package def-wc-subscriptions/tests
 */

declare(strict_types=1);

// Composer autoload.
require_once dirname( __DIR__, 2 ) . '/vendor/autoload.php';

// ── WordPress constants ─────────────────────────────────────────────────
if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );
}
if ( ! defined( 'HOUR_IN_SECONDS' ) ) {
    define( 'HOUR_IN_SECONDS', 3600 );
}

// ── WordPress function stubs ────────────────────────────────────────────

// Track add_action calls for verification.
global $_test_actions;
$_test_actions = array();

if ( ! function_exists( 'add_action' ) ) {
    function add_action( string $hook, $callback, int $priority = 10, int $accepted_args = 1 ): void {
        global $_test_actions;
        $_test_actions[] = array(
            'hook'     => $hook,
            'callback' => $callback,
            'priority' => $priority,
        );
    }
}

if ( ! function_exists( 'add_filter' ) ) {
    function add_filter( string $hook, $callback, int $priority = 10, int $accepted_args = 1 ): void {
        // No-op stub.
    }
}

if ( ! function_exists( '__' ) ) {
    function __( string $text, string $domain = 'default' ): string {
        return $text;
    }
}

if ( ! function_exists( 'esc_html_e' ) ) {
    function esc_html_e( string $text, string $domain = 'default' ): void {
        echo htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
    }
}

if ( ! function_exists( 'esc_html__' ) ) {
    function esc_html__( string $text, string $domain = 'default' ): string {
        return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
    }
}

// ── WP_REST_Response stub ───────────────────────────────────────────────
if ( ! class_exists( 'WP_REST_Response' ) ) {
    class WP_REST_Response {
        public $data;
        public $status;
        public function __construct( $data = null, int $status = 200 ) {
            $this->data   = $data;
            $this->status = $status;
        }
        public function get_data() {
            return $this->data;
        }
        public function get_status(): int {
            return $this->status;
        }
    }
}

// ── WP_REST_Request stub ────────────────────────────────────────────────
if ( ! class_exists( 'WP_REST_Request' ) ) {
    class WP_REST_Request {
        private array $params = array();
        public function get_param( string $key ) {
            return $this->params[ $key ] ?? null;
        }
        public function set_param( string $key, $value ): void {
            $this->params[ $key ] = $value;
        }
    }
}

// ── DEF_Core_Cache stub (trackable) ─────────────────────────────────────
if ( ! class_exists( 'DEF_Core_Cache' ) ) {
    class DEF_Core_Cache {
        /** @var array Track calls to get_or_set */
        public static array $get_or_set_calls = array();
        /** @var array Track calls to invalidate_user */
        public static array $invalidate_calls = array();

        public static function reset(): void {
            self::$get_or_set_calls = array();
            self::$invalidate_calls = array();
        }

        public static function get_or_set( string $key, int $user_id, int $expiration, callable $callback ) {
            self::$get_or_set_calls[] = array(
                'key'        => $key,
                'user_id'    => $user_id,
                'expiration' => $expiration,
            );
            return call_user_func( $callback );
        }

        public static function invalidate_user( int $user_id, string $prefix = '' ): void {
            self::$invalidate_calls[] = array(
                'user_id' => $user_id,
                'prefix'  => $prefix,
            );
        }
    }
}

// ── DEF_Core_Tool_Base stub ─────────────────────────────────────────────
if ( ! class_exists( 'DEF_Core_Tool_Base' ) ) {
    abstract class DEF_Core_Tool_Base {
        protected string $name    = '';
        protected string $route   = '';
        protected array  $methods = array();
        protected string $module  = '';

        /** @var object|null Simulated current user */
        public static $test_current_user = null;

        public function __construct() {
            $this->init();
            // In real plugin, auto-registers if should_register() returns true.
            // In tests, we just instantiate — no registration side effects.
        }

        abstract protected function init(): void;
        abstract public function handle_request( \WP_REST_Request $request ): \WP_REST_Response;

        protected function should_register(): bool {
            return true;
        }

        protected function get_current_user(): ?object {
            return static::$test_current_user;
        }

        protected function error_response( string $message, int $status = 400 ): \WP_REST_Response {
            return new \WP_REST_Response( array( 'error' => true, 'message' => $message ), $status );
        }

        protected function success_response( $data, int $status = 200 ): \WP_REST_Response {
            return new \WP_REST_Response( array( 'success' => true, 'data' => $data ), $status );
        }

        // Expose protected properties for testing.
        public function get_name(): string { return $this->name; }
        public function get_route(): string { return $this->route; }
        public function get_methods(): array { return $this->methods; }
        public function get_module(): string { return $this->module; }
    }
}

// ── Load source files ───────────────────────────────────────────────────
// Note: class-def-wc-subscriptions-cache.php calls DEF_WC_Subscriptions_Cache::init()
// at file load time, which calls add_action() — our stub captures those.
require_once dirname( __DIR__, 2 ) . '/includes/class-def-wc-subscriptions-cache.php';

// class-def-wc-subscriptions-tool.php has an add_action('plugins_loaded', ...) at
// the bottom that instantiates the tool. We load the file which defines the class
// and registers that hook (captured by our stub).
require_once dirname( __DIR__, 2 ) . '/includes/class-def-wc-subscriptions-tool.php';
