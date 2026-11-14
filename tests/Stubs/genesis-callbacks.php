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
