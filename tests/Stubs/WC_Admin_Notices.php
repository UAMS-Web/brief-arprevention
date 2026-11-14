<?php

/**
 * A stand-in for WooCommerce's notices class: records what the theme removes.
 *
 * @see lib/woocommerce/woocommerce-notice.php
 */

declare(strict_types=1);

class WC_Admin_Notices
{
    /** @var list<string> */
    public static array $removed = [];

    public static function remove_notice(string $name): void
    {
        self::$removed[] = $name;
    }
}
