<?php

declare(strict_types=1);
/**
 * Brief.
 *
 * This file adds the Genesis Connect for WooCommerce notice to the Brief Theme.
 *
 * @author  Minimal Genesis
 * @license GPL-2.0-or-later
 *
 * @link    https://minimalgenesis.com/
 */
add_action('admin_print_styles', 'brief_remove_woocommerce_notice');
/**
 * Removes the default WooCommerce Notice.
 *
 * @since 2.3.0
 */
function brief_remove_woocommerce_notice(): void
{

    // If below version WooCommerce 2.3.0, exit early.
    if (! class_exists('WC_Admin_Notices')) {
        return;
    }

    WC_Admin_Notices::remove_notice('theme_support');

}

add_action('admin_notices', 'brief_woocommerce_theme_notice');
/**
 * Adds a prompt to activate Genesis Connect for WooCommerce
 * if WooCommerce is active but Genesis Connect is not.
 *
 * @since 2.3.0
 */
function brief_woocommerce_theme_notice(): void
{

    // If WooCommerce isn't installed or Genesis Connect is installed, exit early.
    if (! class_exists('WooCommerce') || function_exists('gencwooc_setup')) {
        return;
    }

    // If user doesn't have access, exit early.
    if (! current_user_can('manage_woocommerce')) {
        return;
    }

    // If message dismissed, exit early.
    if (get_user_option('brief_woocommerce_message_dismissed', get_current_user_id())) {
        return;
    }

    /* translators: %s: child theme name */
    $notice_html = sprintf(__('Please install and activate <a href="https://wordpress.org/plugins/genesis-connect-woocommerce/" target="_blank">Genesis Connect for WooCommerce</a> to <strong>enable WooCommerce support for %s</strong>.', 'brief'), wp_get_theme()->get('Name'));

    if (current_user_can('install_plugins')) {
        $plugin_slug = 'genesis-connect-woocommerce';
        $admin_url = network_admin_url('update.php');
        $install_link = sprintf(
            '<a href="%s">%s</a>',
            wp_nonce_url(
                add_query_arg(
                    [
                        'action' => 'install-plugin',
                        'plugin' => $plugin_slug,
                    ],
                    $admin_url
                ),
                'install-plugin_'.$plugin_slug
            ),
            __('install and activate Genesis Connect for WooCommerce', 'brief')
        );

        /* translators: 1: plugin install prompt presented as link, 2: child theme name */
        $notice_html = sprintf(__('Please %1$s to <strong>enable WooCommerce support for %2$s</strong>.', 'brief'), $install_link, wp_get_theme()->get('Name'));
    }

    echo '<div class="notice notice-info is-dismissible brief-woocommerce-notice"><p>'.wp_kses_post($notice_html).'</p></div>';

}

add_action('wp_ajax_brief_dismiss_woocommerce_notice', 'brief_dismiss_woocommerce_notice');
/**
 * Adds option to dismiss Genesis Connect for Woocommerce plugin install prompt.
 *
 * @since 2.3.0
 */
function brief_dismiss_woocommerce_notice(): void
{

    update_user_option(get_current_user_id(), 'brief_woocommerce_message_dismissed', 1);

}

add_action('admin_enqueue_scripts', 'brief_notice_script');
/**
 * Enqueues script to clear the Genesis Connect for WooCommerce plugin install prompt on dismissal.
 *
 * @since 2.3.0
 */
function brief_notice_script(): void
{

    wp_enqueue_script('brief_notice_script', get_stylesheet_directory_uri().'/lib/woocommerce/js/notice-update.js', ['jquery'], '1.0', true);

}

add_action('switch_theme', 'brief_reset_woocommerce_notice', 10, 2);
/**
 * Clears the Genesis Connect for WooCommerce plugin install prompt on theme change.
 *
 * @since 2.3.0
 */
function brief_reset_woocommerce_notice(): void
{

    global $wpdb;

    $args = [
        'meta_key' => $wpdb->prefix.'brief_woocommerce_message_dismissed',
        'meta_value' => 1,
    ];
    $users = get_users($args);

    foreach ($users as $user) {
        delete_user_option($user->ID, 'brief_woocommerce_message_dismissed');
    }

}

add_action('deactivated_plugin', 'brief_reset_woocommerce_notice_on_deactivation', 10, 2);
/**
 * Clears the Genesis Connect for WooCommerce plugin prompt on deactivation.
 *
 * @since 2.3.0
 *
 * @param  string  $plugin  The plugin slug.
 * @param  bool  $network_deactivating  Whether the plugin is deactivated for all sites in the network. or just the current site.
 */
function brief_reset_woocommerce_notice_on_deactivation($plugin, $network_deactivating): void
{

    // Conditional check to see if we're deactivating WooCommerce or Genesis Connect for WooCommerce.
    if ($plugin !== 'woocommerce/woocommerce.php' && $plugin !== 'genesis-connect-woocommerce/genesis-connect-woocommerce.php') {
        return;
    }

    brief_reset_woocommerce_notice();

}
