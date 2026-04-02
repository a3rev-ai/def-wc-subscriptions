<?php
/**
 * WooCommerce stubs for refund test.
 *
 * Simulates: 1 subscription with parent (completed) + 1 renewal (refunded).
 * Expected total_spent: 29.99 + 29.99 = 59.98 (refunded counts as paid).
 */

class Stub_WC_Date_R {
    private string $d;
    public function __construct( string $d ) { $this->d = $d; }
    public function date( string $f ): string { return $this->d; }
}

class Stub_WC_Item_R {
    public function get_name(): string { return 'Plan'; }
}

class Stub_WC_Order_R {
    private int $id;
    private string $status;
    private float $total;

    public function __construct( int $id, string $status, float $total ) {
        $this->id     = $id;
        $this->status = $status;
        $this->total  = $total;
    }

    public function get_status(): string { return $this->status; }
    public function get_total(): float { return $this->total; }
    public function get_date_created(): Stub_WC_Date_R {
        return new Stub_WC_Date_R( '2026-01-01T00:00:00+00:00' );
    }
}

class Stub_WC_Sub_R {
    public function get_id(): int { return 100; }
    public function get_status(): string { return 'active'; }
    public function get_time( string $t ): int { return 0; }
    public function get_date( string $t ): ?string {
        return 'date_created' === $t ? '2026-01-01' : null;
    }
    public function get_total(): float { return 29.99; }
    public function get_currency(): string { return 'USD'; }
    public function get_items(): array { return array( new Stub_WC_Item_R() ); }
    public function get_parent_id(): int { return 200; }
    public function get_related_orders( string $type, string $rel ): array { return array( 301 ); }
}

function wcs_get_users_subscriptions( int $uid ): array {
    return array( new Stub_WC_Sub_R() );
}

function wc_get_order( int $id ) {
    return match( $id ) {
        200     => new Stub_WC_Order_R( 200, 'completed', 29.99 ),
        301     => new Stub_WC_Order_R( 301, 'refunded', 29.99 ),
        default => null,
    };
}
