<?php
/**
 * File gathering functions
 */

if (! function_exists('type_exists')) {
    /**
     * Checks if a type is a valid PHP one.
     * 
     * @param  string $type_name The type name to check
     * @return bool   The result
     */
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
}

if (! function_exists('later')) {
    /**
     * Create a deferred call chain in a functionnal way.
     * 
     * @param  string $class_type_interface_or_instance The expected target class/type/interface/instance
     * @return \JClaveau\Async\DeferredCallChain
     */
    function later($class_type_interface_or_instance=null)
    {
        return new \JClaveau\Async\DeferredCallChain(...func_get_args());
    }
}
