<?php
/**
 * WooCommerce stubs for happy-path handle_request() test.
 *
 * Simulates: 1 active subscription with parent order + 2 renewal orders (completed + processing).
 * Expected total_spent: 29.99 + 29.99 + 29.99 = 89.97
 */

class Stub_WC_Date {
    private string $d;
    public function __construct( string $d ) { $this->d = $d; }
    public function date( string $f ): string { return $this->d; }
}

class Stub_WC_Item {
    private string $name;
    public function __construct( string $n ) { $this->name = $n; }
    public function get_name(): string { return $this->name; }
}

class Stub_WC_Order {
    private int $id;
    private string $status;
    private float $total;
    private ?string $date;

    public function __construct( int $id, string $status, float $total, ?string $date = '2026-01-01T00:00:00+00:00' ) {
        $this->id     = $id;
        $this->status = $status;
        $this->total  = $total;
        $this->date   = $date;
    }

    public function get_status(): string { return $this->status; }
    public function get_total(): float { return $this->total; }
    public function get_date_created(): ?Stub_WC_Date {
        return $this->date ? new Stub_WC_Date( $this->date ) : null;
    }
}

class Stub_WC_Subscription {
    public function get_id(): int { return 100; }
    public function get_status(): string { return 'active'; }
    public function get_time( string $t ): int { return 1735689600; }
    public function get_date( string $t ): ?string {
        return match( $t ) {
            'date_created' => '2026-01-01 00:00:00',
            'end'          => null,
            default        => null,
        };
    }
    public function get_total(): float { return 29.99; }
    public function get_currency(): string { return 'USD'; }
    public function get_items(): array { return array( new Stub_WC_Item( 'Premium Plan' ) ); }
    public function get_parent_id(): int { return 200; }
    public function get_related_orders( string $type, string $rel ): array { return array( 301, 302 ); }
}

function wcs_get_users_subscriptions( int $uid ): array {
    return array( new Stub_WC_Subscription() );
}

function wc_get_order( int $id ) {
    return match( $id ) {
        200     => new Stub_WC_Order( 200, 'completed', 29.99 ),
        301     => new Stub_WC_Order( 301, 'completed', 29.99 ),
        302     => new Stub_WC_Order( 302, 'processing', 29.99 ),
        default => null,
    };
}
