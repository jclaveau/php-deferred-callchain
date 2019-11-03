<?php
/**
 * BadTargetInterfaceException
 *
 * @package php-deferred-callchain
 * @author  Jean Claveau
 */
namespace JClaveau\Async\Exceptions;
use       LogicException;

/**
 */
class BadTargetInterfaceException extends LogicException
{
    public function __construct($callchain, $expected_target, $target)
    {
        $this->message = "You are trying to define a target of class ".get_class($target)." for the $callchain allowing only targets implementing ".$expected_target;
    }
    
    /**/
}
