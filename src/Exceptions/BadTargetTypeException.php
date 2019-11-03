<?php
/**
 * BadTargetTypeException
 *
 * @package php-deferred-callchain
 * @author  Jean Claveau
 */
namespace JClaveau\Async\Exceptions;
use       LogicException;

/**
 */
class BadTargetTypeException extends LogicException
{
    public function __construct($callchain, $expected_target, $target)
    {
        $this->message = "You are trying to define a target of type ".gettype($target)." for the $callchain allowing only: ".$expected_target;
    }
    
    /**/
}
