<?php

$loader = require __DIR__ . '/../autoload.php';
$loader->addPsr4('JClaveau\\Async\\', __DIR__.'/src/');

require_once(__DIR__.'/src/functions.php');
