<?php

/**
 * Unit bootstrap for the theme.
 *
 * Prepares what functions.php needs and leaves loading it to the first test
 * (`brief_load_theme()` in tests/Pest.php). Loading it here would run every
 * file-scope statement before the coverage driver starts, so no mutation of one
 * could ever be attributed to a test.
 *
 * functions.php starts the Genesis parent theme by requiring
 * `lib/init.php` from the template directory. The parent is not part of this
 * repository, so the template directory is a scratch directory holding an empty
 * `lib/init.php`, and nothing from Genesis is loaded.
 */

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

require_once __DIR__.'/../vendor/antecedent/patchwork/Patchwork.php';

if (! defined('ABSPATH')) {
    define('ABSPATH', sys_get_temp_dir().'/brief-arprevention-tests-no-wordpress/');
}

$genesis_stub = sys_get_temp_dir().'/brief-arprevention-tests-genesis';

if (! is_dir($genesis_stub.'/lib') && ! mkdir($genesis_stub.'/lib', 0777, true) && ! is_dir($genesis_stub.'/lib')) {
    throw new RuntimeException('Could not create the Genesis stand-in directory '.$genesis_stub.'/lib');
}

if (file_put_contents($genesis_stub.'/lib/init.php', "<?php\n") === false) {
    throw new RuntimeException('Could not write the Genesis stand-in '.$genesis_stub.'/lib/init.php');
}

$GLOBALS['brief_genesis_stand_in'] = $genesis_stub;

// cspell:ignore ABSPATH
