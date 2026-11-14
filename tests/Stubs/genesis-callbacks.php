<?php

/**
 * Declarations of the Genesis callbacks the theme names as strings, for PHPStan only.
 *
 * Genesis is the parent theme and is not part of this repository, so PHPStan cannot see these
 * functions and reports each string handed to `add_action()` or `add_filter()` as not callable.
 * `phpstan.neon.dist` lists this file under `scanFiles`: it is read for symbols and never loaded
 * by a test or by WordPress. Each takes no arguments, which is all the callable type asks for.
 */

declare(strict_types=1);

function genesis_do_nav(): void {}

function genesis_do_subnav(): void {}

function genesis_post_info(): void {}

function genesis_post_meta(): void {}

function genesis_entry_header_markup_open(): void {}

function genesis_entry_header_markup_close(): void {}

function genesis_do_post_title(): void {}

function genesis_do_breadcrumbs(): void {}

function genesis_skip_links(): void {}

function genesis_header_markup_open(): void {}

function genesis_do_header(): void {}

function genesis_header_markup_close(): void {}

function genesis_footer_markup_open(): void {}

function genesis_do_footer(): void {}

function genesis_footer_markup_close(): void {}

function __genesis_return_full_width_content(): string
{
    return 'full-width-content';
}

/**
 * The theme handle, for PHPStan only.
 */
function genesis_get_theme_handle(): string
{
    return '';
}

/**
 * The theme version, for PHPStan only.
 */
function genesis_get_theme_version(): string
{
    return '';
}

/**
 * The config file Genesis loads from `config/`, typed by name, for PHPStan only.
 *
 * Genesis returns `mixed`. The theme's own files under `config/` return arrays, and
 * `appearance` has a fixed shape every caller reads, so it is spelled out here.
 *
 * @param  string  $name  The config file name, without `.php`.
 * @return ($name is 'appearance' ? array{
 *     'fonts-url': string,
 *     'content-width': int,
 *     'text-color': string,
 *     'link-color': string,
 *     'button-color': string,
 *     'default-colors': array{text: string, link: string},
 *     'editor-color-palette': list<array{name: string, slug: string, color: string}>,
 *     'editor-font-sizes': list<array{name: string, size: float|int, slug: string}>
 * } : ($name is 'post-type-supports' ? array<string, array<string>|string> : array<string, mixed>))
 */
function genesis_get_config(string $name): array
{
    return [];
}
