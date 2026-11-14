<?php

/**
 * The color helpers in lib/helper-functions.php, which call no WordPress function.
 *
 * `brief_color_brightness()` writes each channel with `dechex()` and no padding,
 * so a channel under 16 is one digit and the result is not a valid six-digit
 * color. That is pinned as it behaves today, not as a contract to keep.
 *
 * @see lib/helper-functions.php
 */

declare(strict_types=1);

use Brain\Monkey\Functions;

use function Brain\Monkey\Functions\when;

it('picks dark grey for a light color and white for a dark one', function (string $color, string $expected): void {
    expect(brief_color_contrast($color))->toBe($expected);
})->with([
    'white' => ['#ffffff', '#333333'],
    'yellow' => ['#ffff00', '#333333'],
    'black' => ['#000000', '#ffffff'],
    'navy' => ['#000080', '#ffffff'],
    'without the hash' => ['ffffff', '#333333'],
    'red is dark' => ['#ff0000', '#ffffff'],
    'green is light' => ['#00ff00', '#333333'],
    'blue is dark' => ['#0000ff', '#ffffff'],
    'channels are read in order' => ['#0000ff', '#ffffff'],
    'luminosity exactly 128 is dark' => ['#1da535', '#ffffff'],
    'luminosity just over 128 is light' => ['#009bf5', '#333333'],
    'blue decides the boundary' => ['#1da536', '#333333'],
    'an alpha pair is ignored' => ['#1da535ff', '#ffffff'],
]);

it('weights each channel by its own luminosity coefficient', function (string $color, string $expected): void {
    expect(brief_color_contrast($color))->toBe($expected);
})->with([
    // 0.2126 * 255 = 54, 0.7152 * 255 = 182, 0.0722 * 255 = 18: only green passes 128 alone.
    'red and blue together stay dark' => ['#ff00ff', '#ffffff'],
    'green with a little red goes light' => ['#800080', '#ffffff'],
    'grey 129 is light' => ['#818181', '#333333'],
    'grey 128 is dark' => ['#808080', '#ffffff'],
]);

describe('the theme mod readers', function (): void {
    it('reads a text theme mod by name, with its default', function (): void {
        Functions\expect('get_theme_mod')->once()->with('brief_text_color', '#333')->andReturn('#112233');

        expect(brief_theme_mod_string('brief_text_color', '#333'))->toBe('#112233');
    });

    it('falls back to the default for a text theme mod that is not text', function (mixed $stored): void {
        when('get_theme_mod')->justReturn($stored);

        expect(brief_theme_mod_string('brief_text_color', '#333'))->toBe('#333');
    })->with([
        'false' => [false],
        'null' => [null],
        'a number' => [7],
        'an array' => [['#111']],
    ]);

    it('reads a whole-number theme mod by name, with its default', function (): void {
        when('absint')->alias('brief_absint');
        Functions\expect('get_theme_mod')->once()->with('brief_logo_width', 350)->andReturn('400');

        expect(brief_theme_mod_int('brief_logo_width', 350))->toBe(400);
    });

    it('makes a whole-number theme mod non-negative', function (): void {
        when('absint')->alias('brief_absint');
        when('get_theme_mod')->justReturn(-120);

        expect(brief_theme_mod_int('brief_logo_width', 350))->toBe(120);
    });

    it('falls back to the default for a whole-number theme mod that is not numeric', function (mixed $stored): void {
        when('absint')->alias('brief_absint');
        when('get_theme_mod')->justReturn($stored);

        expect(brief_theme_mod_int('brief_logo_width', 350))->toBe(350);
    })->with([
        'false' => [false],
        'text' => ['wide'],
        'an array' => [[400]],
    ]);
});

it('lightens a color and stops each channel at 255', function (): void {
    expect(brief_color_brightness('#102030', 16))->toBe('#203040')
        ->and(brief_color_brightness('#fffffe', 10))->toBe('#ffffff')
        ->and(brief_color_brightness('#ff1010', 10))->toBe('#ff1a1a')
        ->and(brief_color_brightness('#00ff00', 1))->toBe('#1ff1');
});

it('darkens a color and stops each channel at 0', function (): void {
    expect(brief_color_brightness('#203040', -16))->toBe('#102030')
        ->and(brief_color_brightness('#050a10', -20))->toBe('#000')
        ->and(brief_color_brightness('#ff0000', -1))->toBe('#fe00');
});

it('reads each channel from its own pair of digits, with or without the hash', function (): void {
    expect(brief_color_brightness('#1a242e', 1))->toBe('#1b252f')
        ->and(brief_color_brightness('1a242e', 1))->toBe('#1b252f');
});

it('ignores an alpha pair after the three channels', function (): void {
    expect(brief_color_brightness('#1a242eff', 1))->toBe('#1b252f');
});

it('leaves a color unchanged for a change of zero', function (): void {
    expect(brief_color_brightness('#336699', 0))->toBe('#336699');
});

it('writes a channel under 16 as one digit', function (): void {
    expect(brief_color_brightness('#000000', 5))->toBe('#555');
});
