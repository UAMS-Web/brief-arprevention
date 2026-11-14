<?php

declare(strict_types=1);
/**
 * Brief child theme.
 *
 * @author  Minimal Genesis
 * @license GPL-2.0-or-later
 *
 * @link    https://my.studiopress.com/themes/brief/
 */

/**
 * Genesis responsive menus settings. (Requires Genesis 3.0+.)
 */
return [
    'script' => [
        'mainMenu' => __('', 'brief'),
        'menuClasses' => [
            'others' => ['.nav-primary'],
        ],
    ],
    'extras' => [
        'media_query_width' => '960px',
    ],
];
