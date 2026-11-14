<?php

/**
 * The Customizer settings in lib/customize.php and the defaults in lib/theme-defaults.php.
 *
 * @see lib/customize.php
 * @see lib/theme-defaults.php
 */

declare(strict_types=1);

use function Brain\Monkey\Functions\when;

it('registers the customizer and the social defaults on their hooks', function (): void {
    expect(brief_registered_hooks('actions', 'customize_register', 'brief_customizer_register'))->toBe(10)
        ->and(brief_registered_hooks('filters', 'simple_social_default_styles', 'brief_social_default_styles'))->toBe(10);
});

it('overrides the Simple Social Icons defaults with the theme config', function (): void {
    brief_stub_genesis();
    when('wp_parse_args')->alias(fn (array $args, array $defaults): array => array_merge($defaults, $args));

    expect(brief_social_default_styles(['size' => 12, 'new_window' => 1]))->toBe([
        'size' => 40,
        'new_window' => 1,
        'alignment' => 'aligncenter',
        'background_color' => '#ffffff',
        'background_color_hover' => '#ffffff',
        'border_radius' => 0,
        'border_width' => 0,
        'icon_color' => '#333333',
        'icon_color_hover' => '#666666',
    ]);
});

describe('brief_customizer_register', function (): void {
    beforeEach(function (): void {
        if (! class_exists('WP_Customize_Color_Control')) {
            eval('class WP_Customize_Color_Control { public function __construct(public $manager, public $id, public $args) {} }');
        }
    });

    it('adds the color settings, the logo width setting and their controls', function (): void {
        brief_stub_genesis();

        $settings = [];
        $controls = [];
        $manager = Mockery::mock();
        $manager->shouldReceive('add_setting')->times(3)->andReturnUsing(function (string $id, array $args) use (&$settings): void {
            $settings[$id] = $args;
        });
        $manager->shouldReceive('add_control')->times(3)->andReturnUsing(function (...$arguments) use (&$controls): void {
            $controls[] = $arguments;
        });

        brief_customizer_register($manager);

        expect($settings)->toBe([
            'brief_text_color' => ['default' => '#333', 'sanitize_callback' => 'sanitize_hex_color'],
            'brief_link_color' => ['default' => '#fff385', 'sanitize_callback' => 'sanitize_hex_color'],
            'brief_logo_width' => [
                'default' => 350,
                'sanitize_callback' => 'absint',
                'validate_callback' => 'brief_validate_logo_width',
            ],
        ])
            ->and($controls[0][0])->toBeInstanceOf('WP_Customize_Color_Control')
            ->and($controls[0][0]->manager)->toBe($manager)
            ->and($controls[0][0]->id)->toBe('brief_text_color')
            ->and($controls[0][0]->args)->toBe([
                'description' => 'Change the default color for body, heading, button background, and other text.',
                'label' => 'Text Color',
                'section' => 'colors',
                'settings' => 'brief_text_color',
            ])
            ->and($controls[1][0]->id)->toBe('brief_link_color')
            ->and($controls[1][0]->args)->toBe([
                'description' => 'Change the default link highlight color.',
                'label' => 'Link Color',
                'section' => 'colors',
                'settings' => 'brief_link_color',
            ])
            ->and($controls[2])->toBe([
                'brief_logo_width',
                [
                    'label' => 'Logo Width',
                    'description' => 'The maximum width of the logo in pixels.',
                    'priority' => 9,
                    'section' => 'title_tagline',
                    'settings' => 'brief_logo_width',
                    'type' => 'number',
                    'input_attrs' => ['min' => 100],
                ],
            ]);
    });
});

describe('brief_validate_logo_width', function (): void {
    it('accepts a width of 100 or more', function (mixed $width): void {
        $validity = Mockery::mock();
        $validity->shouldReceive('add')->never();

        expect(brief_validate_logo_width($validity, $width))->toBe($validity);
    })->with([
        'the minimum' => [100],
        'above it' => [350],
        'a numeric string' => ['600'],
    ]);

    it('rejects an empty or non-numeric width as required', function (mixed $width): void {
        $validity = Mockery::mock();
        $validity->shouldReceive('add')->once()->with('required', 'You must supply a valid number.');

        expect(brief_validate_logo_width($validity, $width))->toBe($validity);
    })->with([
        'empty string' => [''],
        'zero' => [0],
        'null' => [null],
        'text' => ['wide'],
    ]);

    it('rejects a width under 100', function (): void {
        $validity = Mockery::mock();
        $validity->shouldReceive('add')->once()->with('logo_too_small', 'The logo width cannot be less than 100.');

        expect(brief_validate_logo_width($validity, 99))->toBe($validity);
    });
});
