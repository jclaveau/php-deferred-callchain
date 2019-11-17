<?php
/**
 */ 
function scandir_r($directory) {
    // $files = array_slice(scandir($directory, SCANDIR_SORT_NONE), 2);
    $files = array_slice(scandir($directory), 2);

    $map = [];
    foreach ($files as $file) {
        if (is_dir( $directory . '/' . $file)) {
            $submap = scandir_r($directory . '/' . $file);
            $map = array_merge($map, $submap);
        }
        else {
            $map[] = $directory . '/' . $file;
        }
    }
    
    return $map;
}

if (PHP_VERSION_ID >= 70000) {
    $root = 'src';
}
else {
    $root = 'src_5.6';
}

$map = scandir_r($root);
foreach ($map as &$map_entry) {
    $map_entry = preg_replace("/^src(_\d+\.\d+)?\//", '', $map_entry);
}
// var_export($map); exit;

/**
 * Dependency map has to be ordered to declare dependencies before
 * the code needing it.
 */
$sorted_map = [
  'ArrayAccessTrait.php',
  'FunctionCallTrait.php',
  'ExportTrait.php',
  'DeferredCallChain.php',
  'Exceptions/BadTargetClassException.php',
  'Exceptions/BadTargetInterfaceException.php',
  'Exceptions/BadTargetTypeException.php',
  'Exceptions/TargetAlreadyDefinedException.php',
  'Exceptions/UndefinedTargetClassException.php',
  'functions.php',
];

if ($missing = array_diff($sorted_map, $map)) {
    throw new \Exception(
        "Missing file in your classmap. Please add it manually to "
        . __FILE__ . "\n"
        . var_export($missing, true)
    );
}

foreach ($sorted_map as $filepath) {
    require_once($root . '/' . $filepath);
}
