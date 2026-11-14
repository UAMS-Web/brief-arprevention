<?php

declare(strict_types=1);
/**
 * Brief theme settings.
 *
 * Genesis 2.9+ updates these settings when themes are activated.
 *
 * @author  Minimal Genesis
 * @license GPL-2.0-or-later
 *
 * @link    https://minimalgenesis.com/
 */

return [
    GENESIS_SETTINGS_FIELD => [
        'blog_cat_num' => 4,
        'breadcrumb_home' => 0,
        'breadcrumb_front_page' => 0,
        'breadcrumb_posts_page' => 0,
        'breadcrumb_single' => 0,
        'breadcrumb_page' => 0,
        'breadcrumb_archive' => 0,
        'breadcrumb_404' => 0,
        'breadcrumb_attachment' => 0,
        'content_archive' => 'full',
        'content_archive_limit' => 220,
        'content_archive_thumbnail' => 0,
        'entry_meta_after_content' => '[post_categories before=""] [post_tags before=""]',
        'entry_meta_before_content' => '[post_date]',
        'image_size' => 'medium',
        'image_alignment' => 'alignleft',
        'posts_nav' => 'numeric',
        'site_layout' => 'full-width-content',
    ],
    'posts_per_page' => 4,
];
