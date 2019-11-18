<?php

$version = explode('.',phpversion());
$version = $version[0] . '.' . $version[1];

if (PHP_VERSION_ID >= 70000) {
    $root = 'src';
}
else {
    $root = 'src_5.6';
}

$loader = require "./vendor_$version/autoload.php";
$loader->addPsr4('JClaveau\\Async\\', __DIR__.'/'.$root.'/');

require_once(__DIR__.'/'.$root.'/functions.php');
