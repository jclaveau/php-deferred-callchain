<?php
/**
 * BadTargetClassException
 *
 * @package php-deferred-callchain
 * @author  Jean Claveau
 */
namespace JClaveau\Async\Exceptions;
use       JClaveau\Async\DeferredCallChain;
use       LogicException;

/**
 * Thrown when applying a deferred call chain on a target which is not
 * an instance of the expected class.
 */
class BadTargetClassException extends LogicException
{
    /**
     * Constructor.
     * 
     * @param DeferredCallChain $callchain
     * @param mixed             $expected_target The expected class
     * @param mixed             $target
     */
    public function __construct(DeferredCallChain $callchain, $expected_target, $target)
    {
        $this->message = "You are trying to define a target of class ".get_class($target)." for the $callchain allowing only targets of class ".$expected_target;
    }
    
    /**/
}
