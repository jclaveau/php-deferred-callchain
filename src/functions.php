<?php

if ( ! function_exists('spl_object_id')) {
    /**
     * @see https://secure.php.net/manual/en/function.spl-object-id.php
     * This method doesn't exist before PHP 7.2.0
     */
    function spl_object_id($object)
    {
        ob_start();
        var_dump($object); // object(foo)#INSTANCE_ID (0) { }
        return preg_replace('~.+#(\d+).+~s', '$1', ob_get_clean());
    }
}

function type_exists($type_name)
{
    return in_array($type_name, [
        "boolean", 
        "integer", 
        "double", 
        "string", 
        "array", 
        "object", 
        "resource", 
        "resource (closed)", 
        "NULL", 
        "unknown type"
    ]);
}
