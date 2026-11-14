<?php

/**
 * Unit bootstrap for the theme.
 *
 * Loads functions.php, which a theme cannot avoid, inside one Brain Monkey
 * session. The functions it defines outlive the session; the hooks it
 * registered are discarded by tearDown(), so every test starts clean.
 *
 * functions.php starts the Genesis parent theme by requiring
 * `lib/init.php` from the template directory. The parent is not part of this
 * repository, so the template directory is a scratch directory holding an empty
 * `lib/init.php`, and nothing from Genesis is loaded.
 */

declare(strict_types=1);

use function Brain\Monkey\Functions\when;
use function Brain\Monkey\setUp;
use function Brain\Monkey\tearDown;

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

setUp();

when('get_template_directory')->justReturn($genesis_stub);
when('get_stylesheet_directory')->justReturn(dirname(__DIR__));

// What functions.php calls at file scope that Brain Monkey does not define.
when('add_image_size')->justReturn();
when('unregister_sidebar')->justReturn();
when('genesis_unregister_layout')->justReturn();

require_once __DIR__.'/../functions.php';

tearDown();

// cspell:ignore ABSPATH
