<?php

/**
 * BadTargetTypeException
 *
 * @package php-deferred-callchain
 * @author  Jean Claveau
 */
namespace JClaveau\Async\Exceptions;

use JClaveau\Async\DeferredCallChain;
use LogicException;
/**
 * Thrown when applying a deferred call chain on a target which is not
 * of the expected type.
 */
class BadTargetTypeException extends LogicException
{
    /**
     * Constructor.
     * 
     * @param DeferredCallChain $callchain
     * @param mixed             $expected_target The expected type
     * @param mixed             $target
     */
    public function __construct(DeferredCallChain $callchain, $expected_target, $target)
    {
        $this->message = "You are trying to define a target of type " . gettype($target) . " for the {$callchain} allowing only: " . $expected_target;
    }
}