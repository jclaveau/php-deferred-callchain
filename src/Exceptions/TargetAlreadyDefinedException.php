<?php
/**
 * TargetAlreadyDefinedException
 *
 * @package php-deferred-callchain
 * @author  Jean Claveau
 */
namespace JClaveau\Async\Exceptions;
use       JClaveau\Async\DeferredCallChain;
use       LogicException;

/**
 * Thrown when applying a deferred call chain on a target which is already
 * defined.
 */
class TargetAlreadyDefinedException extends LogicException
{
    /**
     * Constructor.
     * 
     * @param DeferredCallChain $callchain
     * @param mixed             $expected_target The target instance
     * @param mixed             $target
     */
    public function __construct(DeferredCallChain $callchain, $expected_target, $target)
    {
        $this->message = "You are trying to define the target ".spl_object_id($target)." for the $callchain which already has one: ".spl_object_id($expected_target);
    }
    
    /**/
}
