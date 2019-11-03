<?php
/**
 * TargetAlreadyDefinedException
 *
 * @package php-deferred-callchain
 * @author  Jean Claveau
 */
namespace JClaveau\Async\Exceptions;
use       LogicException;

/**
 */
class TargetAlreadyDefinedException extends LogicException
{
    public function __construct($callchain, $expected_target, $target)
    {
        $this->message = "You are trying to define the target ".spl_object_id($target)." for the $callchain which already has one: ".spl_object_id($expected_target)
    }
    
    /**/
}
