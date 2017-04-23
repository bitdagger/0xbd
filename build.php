#!/usr/bin/env php
<?php

namespace ZxBD;

use Twig_Environment;
use Twig_Extension_Debug;
use Twig_Loader_Filesystem;
use Twig_SimpleFunction;

printf("Compiling static pages...\n\n");
$timer_start = microtime(true);

/*
 * -----------------------------------------------------------------------------
 * Setup the autoloader
 * -----------------------------------------------------------------------------
 */
if (!file_exists($autoloder = sprintf('%s/vendor/autoload.php', __DIR__))) {
    echo 'Setup incomplete. Run `make`';
    exit(1);
}
require_once $autoloder;
unset($autoloder);

/*
 * -----------------------------------------------------------------------------
 * Initialize Twig
 * -----------------------------------------------------------------------------
 */
$loader = new Twig_Loader_Filesystem(sprintf('%s/templates', __DIR__));
$twig = new Twig_Environment($loader, [
    'debug'            => true,
    'charset'          => 'utf-8',
    'cache'            => false,
    'auto_reload'      => true,
    'strict_variables' => true,
    'autoescape'       => false,
    'optimizations'    => -1,
]);
$twig->addExtension(new Twig_Extension_Debug());
unset($loader);


/*
 * -----------------------------------------------------------------------------
 * Build Pages
 * -----------------------------------------------------------------------------
 */
$dh = opendir(sprintf('%s/posts', __DIR__));
while ($file = readdir($dh)) {
    if (!preg_match('/\.md$/i', $file)) {
        continue;
    }

    // Calculate the destination filename
    $destname = preg_replace('/[^a-z0-9\-\.]+/i', '', $file);
    $destname = preg_replace('/\.md$/i', '', $destname);
    $destname = sprintf('%s.html', str_replace('.', '/', $destname));

    // Create the folder structure if it doesn't exist
    if (!is_dir($abspath = sprintf('%s/public/%s', __DIR__, dirname($destname)))) {
        mkdir($abspath, 0777, true);
    }

    // Default metadata
    $meta = [
        'title' => 'Untitled',
        'type'  => 'base',
        'date'  => date('Y-m-d H:i:s'),
    ];

    // Read the raw content
    $contents = file_get_contents(sprintf('%s/posts/%s', __DIR__, $file));

    // Parse the metadata
    $pattern = '/^([\s\S]+?)---/i';
    preg_match($pattern, $contents, $matches);
    if (!empty($matches[1])) {
        $contents = preg_replace($pattern, '', $contents);
        $m = explode("\n", $matches[1]);
        foreach ($m as $item) {
            if (empty($item)) {
                continue;
            }
            if (preg_match('/^([a-z]+)[\s]*:[\s]*([^\n\r]+)/i', $item, $matches)) {
                $meta[strtolower($matches[1])] = $matches[2];
            }
        }
    }

    // Determine the possible templates
    $templates = [
        sprintf('%s.twig', $meta['type']),
        'base.twig', // Always have a valid fallback
    ];

    // Render the twig template
    $output = $twig->render('_init.twig', [
        'meta'      => $meta,
        'content'   => $contents,
        'templates' => $templates,
    ]);

    // Write the file contents
    file_put_contents(sprintf('%s/%s', $abspath, basename($destname)), $output);
    printf("Wrote %s\n", $destname);
}
closedir($dh);

$timer_end = microtime(true);

$time = $timer_end - $timer_start;
$unit = 's';
if ($time < 1) {
    $time = $time * 1000;
    $unit = 'ms';
}

printf("\nDONE Compiled successfully in %d%s\n", $time, $unit);
