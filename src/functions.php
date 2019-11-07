<?php
/**
 * File gathering functions
 */

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
