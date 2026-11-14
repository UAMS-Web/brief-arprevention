<?php

declare(strict_types=1);
/**
 * Brief child theme.
 *
 * Theme supports.
 *
 * @author  Minimal Genesis
 * @license GPL-2.0-or-later
 *
 * @link    https://my.studiopress.com/themes/brief/
 */

return [
    'genesis-custom-logo' => [
        'height' => 120,
        'width' => 700,
        'flex-height' => true,
        'flex-width' => true,
    ],
    'html5' => [
        'caption',
        'comment-form',
        'comment-list',
        'gallery',
        'search-form',
    ],
    'genesis-accessibility' => [
        'drop-down-menu',
        'headings',
        'search-form',
        'skip-links',
    ],
    'genesis-after-entry-widget-area' => '',
    'genesis-footer-widgets' => 1,
    'genesis-menus' => [
        'primary' => __('Header Menu', 'brief'),
        'secondary' => __('Footer Menu', 'brief'),
    ],
];
