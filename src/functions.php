<?php

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
