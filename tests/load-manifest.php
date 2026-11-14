<?php

/**
 * What loading functions.php registers and calls at file scope.
 *
 * `hooks` is every callback left registered, as `<type> <hook> <callback> <priority>`.
 * A hook the theme removes (the Genesis navigation, entry info and entry meta
 * defaults, which the loader registers first) is absent by design. `calls` is
 * every file-scope call to a function the loader records, in order.
 *
 * Regenerate it only after changing the theme on purpose: it is the expected
 * value, and tests/Pest.php fails the first test of every process when the load
 * differs from it.
 */

declare(strict_types=1);

return [
    'hooks' => [
        'actions after_setup_theme brief_localization_setup 10',
        'actions after_setup_theme genesis_child_gutenberg_support 10',
        'actions after_setup_theme brief_theme_support 9',
        'actions after_setup_theme brief_post_type_support 9',
        'actions after_setup_theme brief_content_width 0',
        'actions customize_register brief_customizer_register 10',
        'actions wp_enqueue_scripts brief_css 10',
        'actions wp_enqueue_scripts brief_woocommerce_css 10',
        'actions wp_enqueue_scripts brief_enqueue_scripts_styles 10',
        'actions wp_enqueue_scripts brief_enqueue_gutenberg_frontend_styles 10',
        'actions wp_enqueue_scripts brief_custom_gutenberg_css 10',
        'actions after_switch_theme brief_woocommerce_image_dimensions_after_theme_setup 1',
        'actions activated_plugin brief_woocommerce_image_dimensions_after_woo_activation 10',
        'actions admin_print_styles brief_remove_woocommerce_notice 10',
        'actions admin_notices brief_woocommerce_theme_notice 10',
        'actions wp_ajax_brief_dismiss_woocommerce_notice brief_dismiss_woocommerce_notice 10',
        'actions admin_enqueue_scripts brief_notice_script 10',
        'actions switch_theme brief_reset_woocommerce_notice 10',
        'actions deactivated_plugin brief_reset_woocommerce_notice_on_deactivation 10',
        'actions genesis_header genesis_do_nav 12',
        'actions genesis_footer genesis_do_subnav 5',
        'actions genesis_entry_header genesis_post_info 9',
        'actions genesis_entry_header genesis_post_meta 14',
        'actions admin_menu hide_menu 10',
        'actions enqueue_block_editor_assets brief_block_editor_styles 10',
        'actions enqueue_block_editor_assets brief_custom_gutenberg_admin_css 10',
        'filters simple_social_default_styles brief_social_default_styles 10',
        'filters woocommerce_style_smallscreen_breakpoint brief_woocommerce_breakpoint 10',
        'filters genesiswooc_products_per_page brief_default_products_per_page 10',
        'filters woocommerce_pagination_args brief_woocommerce_pagination 10',
        'filters woocommerce_get_image_size_gallery_thumbnail brief_gallery_image_thumbnail 10',
        'filters woocommerce_product_description_heading __return_null 10',
        'filters woocommerce_enqueue_styles brief_woocommerce_styles 10',
        'filters wp_nav_menu_args brief_secondary_menu_args 10',
        'filters genesis_edit_post_link __return_false 10',
        'filters get_the_content_more_link brief_read_more_link 10',
        'filters genesis_author_box_gravatar_size brief_author_box_gravatar 10',
        'filters genesis_comment_list_args brief_comments_gravatar 10',
        'filters gform_submit_button_1 __return_false 10',
        'filters gform_submit_button_2 __return_false 10',
        'filters body_class brief_blocks_body_classes 10',
    ],
    'calls' => [
        'genesis_register_responsive_menus' => [
            [
                [
                    'script' => [
                        'mainMenu' => '',
                        'menuClasses' => [
                            'others' => ['.nav-primary'],
                        ],
                    ],
                    'extras' => [
                        'media_query_width' => '960px',
                    ],
                ],
            ],
        ],
        'add_image_size' => [
            ['sidebar-featured', 75, 75, true],
            ['genesis-singular-images', 702, 526, true],
        ],
        'unregister_sidebar' => [
            ['header-right'],
            ['sidebar-alt'],
        ],
        'genesis_unregister_layout' => [
            ['content-sidebar-sidebar'],
            ['sidebar-content-sidebar'],
            ['sidebar-sidebar-content'],
        ],
        'add_theme_support' => [
            ['editor-styles'],
            ['align-wide'],
            ['responsive-embeds'],
            [
                'editor-font-sizes',
                [
                    [
                        'name' => 'Small',
                        'size' => 14.4,
                        'slug' => 'small',
                    ],
                    [
                        'name' => 'Normal',
                        'size' => 18,
                        'slug' => 'normal',
                    ],
                    [
                        'name' => 'Large',
                        'size' => 20,
                        'slug' => 'large',
                    ],
                    [
                        'name' => 'Larger',
                        'size' => 24,
                        'slug' => 'larger',
                    ],
                ],
            ],
            [
                'editor-color-palette',
                [
                    [
                        'name' => 'Text color',
                        'slug' => 'theme-primary',
                        'color' => '#333',
                    ],
                    [
                        'name' => 'Link color',
                        'slug' => 'theme-secondary',
                        'color' => '#fff385',
                    ],
                ],
            ],
        ],
        'add_editor_style' => [
            ['/lib/gutenberg/style-editor.css'],
        ],
    ],
];
