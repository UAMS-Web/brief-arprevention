<?php

/**
 * The color helpers in lib/helper-functions.php, which call no WordPress function.
 *
 * @see lib/helper-functions.php
 */

declare(strict_types=1);

it('picks dark grey for a light color and white for a dark one', function (string $color, string $expected): void {
    expect(brief_color_contrast($color))->toBe($expected);
})->with([
    'white' => ['#ffffff', '#333333'],
    'yellow' => ['#ffff00', '#333333'],
    'black' => ['#000000', '#ffffff'],
    'navy' => ['#000080', '#ffffff'],
    'without the hash' => ['ffffff', '#333333'],
]);

it('lightens a color and stops each channel at 255', function (): void {
    expect(brief_color_brightness('#102030', 16))->toBe('#203040')
        ->and(brief_color_brightness('#fffffe', 10))->toBe('#ffffff');
});

it('darkens a color', function (): void {
    expect(brief_color_brightness('#203040', -16))->toBe('#102030');
});
