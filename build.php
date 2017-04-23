#!/usr/bin/env php
<?php

namespace ZxBD;

/*
 * -----------------------------------------------------------------------------
 * Sanity Check
 * -----------------------------------------------------------------------------
 */
if (!file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    echo 'Setup incomplete. Run `make`';
    exit(1);
}

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

    // Read the raw content
    $contents = file_get_contents(sprintf('%s/posts/%s', __DIR__, $file));
    
    // Write the file contents
    file_put_contents(sprintf('%s/%s', $abspath, basename($destname)), $contents);
}
closedir($dh);
