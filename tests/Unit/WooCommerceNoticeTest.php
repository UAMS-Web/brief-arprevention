<?php

/**
 * The Genesis Connect for WooCommerce notice in lib/woocommerce/woocommerce-notice.php.
 *
 * @see lib/woocommerce/woocommerce-notice.php
 */

declare(strict_types=1);

use Brain\Monkey\Functions;

use function Brain\Monkey\Functions\when;

/**
 * Runs the notice callback and returns what it printed.
 */
function brief_notice_output(): string
{
    ob_start();
    brief_woocommerce_theme_notice();

    return (string) ob_get_clean();
}

/**
 * The state in which the notice shows: WooCommerce active, Genesis Connect not,
 * a user who may manage WooCommerce and has not dismissed it.
 */
function brief_notice_conditions(bool $can_install = false): void
{
    brief_woocommerce_active(true);
    brief_function_exists(['gencwooc_setup' => false]);
    when('current_user_can')->alias(fn (string $capability): bool => match ($capability) {
        'manage_woocommerce' => true,
        'install_plugins' => $can_install,
        default => false,
    });
    when('get_current_user_id')->justReturn(7);
    when('get_user_option')->justReturn(false);
    when('wp_get_theme')->justReturn(new class
    {
        public function get(string $key): string
        {
            return "<{$key}>";
        }
    });
    when('wp_kses_post')->returnArg();
}

it('registers its hooks', function (string $type, string $hook, string $callback, int $priority): void {
    expect(brief_registered_hooks($type, $hook, $callback))->toBe($priority);
})->with([
    ['actions', 'admin_print_styles', 'brief_remove_woocommerce_notice', 10],
    ['actions', 'admin_notices', 'brief_woocommerce_theme_notice', 10],
    ['actions', 'wp_ajax_brief_dismiss_woocommerce_notice', 'brief_dismiss_woocommerce_notice', 10],
    ['actions', 'admin_enqueue_scripts', 'brief_notice_script', 10],
    ['actions', 'switch_theme', 'brief_reset_woocommerce_notice', 10],
    ['actions', 'deactivated_plugin', 'brief_reset_woocommerce_notice_on_deactivation', 10],
]);

describe('removing the WooCommerce theme support notice', function (): void {
    beforeEach(function (): void {
        require_once __DIR__.'/../Stubs/WC_Admin_Notices.php';

        WC_Admin_Notices::$removed = [];
    });

    it('removes it when WooCommerce has the notices class', function (): void {
        brief_class_exists(['WC_Admin_Notices' => true]);

        brief_remove_woocommerce_notice();

        expect(WC_Admin_Notices::$removed)->toBe(['theme_support']);
    });

    it('does nothing for a WooCommerce too old to have it', function (): void {
        brief_class_exists(['WC_Admin_Notices' => false]);

        brief_remove_woocommerce_notice();

        expect(WC_Admin_Notices::$removed)->toBe([]);
    });
});

describe('the install prompt', function (): void {
    it('stays hidden unless WooCommerce is active and Genesis Connect is not', function (bool $woocommerce, bool $connect): void {
        brief_notice_conditions();
        brief_woocommerce_active($woocommerce);
        brief_function_exists(['gencwooc_setup' => $connect]);

        expect(brief_notice_output())->toBe('');
    })->with([
        'WooCommerce inactive' => [false, false],
        'Genesis Connect active' => [true, true],
        'neither' => [false, true],
    ]);

    it('stays hidden from a user who cannot manage WooCommerce', function (): void {
        brief_notice_conditions();
        when('current_user_can')->justReturn(false);

        expect(brief_notice_output())->toBe('');
    });

    it('stays hidden once dismissed', function (): void {
        brief_notice_conditions();
        when('get_user_option')->alias(fn (string $option, int $user): string|false => $option === 'brief_woocommerce_message_dismissed' && $user === 7 ? '1' : false);

        expect(brief_notice_output())->toBe('');
    });

    it('asks a user who cannot install plugins to install Genesis Connect', function (): void {
        brief_notice_conditions();

        expect(brief_notice_output())->toBe('<div class="notice notice-info is-dismissible brief-woocommerce-notice"><p>'
            .'Please install and activate <a href="https://wordpress.org/plugins/genesis-connect-woocommerce/" target="_blank">Genesis Connect for WooCommerce</a> to <strong>enable WooCommerce support for <Name></strong>.'
            .'</p></div>');
    });

    it('links the install to a user who can install plugins', function (): void {
        brief_notice_conditions(can_install: true);
        Functions\expect('network_admin_url')->once()->with('update.php')->andReturn('https://example.test/wp-admin/network/update.php');
        Functions\expect('add_query_arg')->once()->with(['action' => 'install-plugin', 'plugin' => 'genesis-connect-woocommerce'], 'https://example.test/wp-admin/network/update.php')->andReturn('https://example.test/install');
        Functions\expect('wp_nonce_url')->once()->with('https://example.test/install', 'install-plugin_genesis-connect-woocommerce')->andReturn('https://example.test/install?nonce=1');

        expect(brief_notice_output())->toBe('<div class="notice notice-info is-dismissible brief-woocommerce-notice"><p>'
            .'Please <a href="https://example.test/install?nonce=1">install and activate Genesis Connect for WooCommerce</a> to <strong>enable WooCommerce support for <Name></strong>.'
            .'</p></div>');
    });
});

it('remembers the dismissal for the current user', function (): void {
    when('get_current_user_id')->justReturn(7);
    Functions\expect('update_user_option')->once()->with(7, 'brief_woocommerce_message_dismissed', 1);

    brief_dismiss_woocommerce_notice();
});

it('enqueues the dismissal script in the footer', function (): void {
    when('get_stylesheet_directory_uri')->justReturn('https://example.test/theme');
    Functions\expect('wp_enqueue_script')->once()->with('brief_notice_script', 'https://example.test/theme/lib/woocommerce/js/notice-update.js', ['jquery'], '1.0', true);

    brief_notice_script();
});

it('forgets the dismissal of every user who had dismissed it', function (): void {
    $GLOBALS['wpdb'] = (object) ['prefix' => 'wp_'];
    Functions\expect('get_users')->once()->with(['meta_key' => 'wp_brief_woocommerce_message_dismissed', 'meta_value' => 1])->andReturn([(object) ['ID' => 3], (object) ['ID' => 9]]);
    $deleted = [];
    when('delete_user_option')->alias(function (int $id, string $option) use (&$deleted): void {
        $deleted[] = [$id, $option];
    });

    brief_reset_woocommerce_notice();

    expect($deleted)->toBe([[3, 'brief_woocommerce_message_dismissed'], [9, 'brief_woocommerce_message_dismissed']]);
});

it('forgets the dismissal when WooCommerce or Genesis Connect is deactivated', function (string $plugin, bool $reset): void {
    $GLOBALS['wpdb'] = (object) ['prefix' => 'wp_'];
    Functions\expect('get_users')->times($reset ? 1 : 0)->andReturn([]);

    brief_reset_woocommerce_notice_on_deactivation($plugin);
})->with([
    'WooCommerce' => ['woocommerce/woocommerce.php', true],
    'Genesis Connect' => ['genesis-connect-woocommerce/genesis-connect-woocommerce.php', true],
    'another plugin' => ['akismet/akismet.php', false],
]);
