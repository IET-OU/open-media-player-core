#!/usr/bin/env php
<?php
/**
 * Dynamically modify 'phpcs.xml' CodeSniffer configurations, based on conditional comments.
 *
 * Usage: dyn-phpcs-xml --no-ns vendor/iet-ou/open-media-player-core/phpcs.xml > phpcs-1.xml
 *
 * @copyright Copyright 2015 The Open University.
 * @author  N.D.Freear, 22 July 2015.
 */

fprintf(STDERR, "dyn-php-xml\n");

if ($argc < 3) {
    fprintf(STDERR, "Error, insufficient arguments.\n");
    exit(1);
}

$filename = $argv[ $argc - 1 ];

$xml = file_get_contents($filename);

if (in_array('--no-ns', $argv)) {
    $xml = preg_replace('@<!--\[if DYN:NO-NS\]>(?P<dyn>[^\[]+)<!\[endif\]-->@msi', '$1', $xml);
}
if (in_array('--cfn', $argv)) {
    $xml = preg_replace('@<!--\[if DYN:CFN\]>(?P<dyn>[^\[]+)<!\[endif\]-->@msi', '$1', $xml);
}

echo $xml;


//End.
