<?php
/**
 * WooCommerce stubs for null/missing-order defensive branches.
 *
 * Simulates:
 * - Parent order with null get_date_created()
 * - Renewal 301 returns null from wc_get_order() (missing/deleted order)
 * - Renewal 302 is a normal completed order
 *
 * Expected: parent_order.date = null, only 1 renewal in output, total_spent = 29.99 + 15.00 = 44.99
 */

class Stub_WC_Date_N {
    private string $d;
    public function __construct( string $d ) { $this->d = $d; }
    public function date( string $f ): string { return $this->d; }
}

class Stub_WC_Item_N {
    public function get_name(): string { return 'Plan'; }
}

class Stub_WC_Order_N {
    private int $id;
    private string $status;
    private float $total;
    private bool $has_date;

    public function __construct( int $id, string $status, float $total, bool $has_date = true ) {
        $this->id       = $id;
        $this->status   = $status;
        $this->total    = $total;
        $this->has_date = $has_date;
    }

    public function get_status(): string { return $this->status; }
    public function get_total(): float { return $this->total; }
    public function get_date_created() {
        // Return null to exercise the null-date branch.
        return $this->has_date ? new Stub_WC_Date_N( '2026-01-01T00:00:00+00:00' ) : null;
    }
}

class Stub_WC_Sub_N {
    public function get_id(): int { return 100; }
    public function get_status(): string { return 'active'; }
    public function get_time( string $t ): int { return 0; }
    public function get_date( string $t ): ?string {
        return 'date_created' === $t ? '2026-01-01' : null;
    }
    public function get_total(): float { return 29.99; }
    public function get_currency(): string { return 'USD'; }
    public function get_items(): array { return array( new Stub_WC_Item_N() ); }
    public function get_parent_id(): int { return 200; }
    public function get_related_orders( string $type, string $rel ): array { return array( 301, 302 ); }
}

function wcs_get_users_subscriptions( int $uid ): array {
    return array( new Stub_WC_Sub_N() );
}

function wc_get_order( int $id ) {
    return match( $id ) {
        200     => new Stub_WC_Order_N( 200, 'completed', 29.99, false ), // null date
        301     => null, // missing/deleted order
        302     => new Stub_WC_Order_N( 302, 'completed', 15.00, true ),
        default => null,
    };
}
