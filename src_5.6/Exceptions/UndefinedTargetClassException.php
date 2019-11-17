<?php

/**
 * UndefinedTargetClassException
 *
 * @package php-deferred-callchain
 * @author  Jean Claveau
 */
namespace JClaveau\Async\Exceptions;

use JClaveau\Async\DeferredCallChain;
use LogicException;
/**
 * Thrown when defining an expected target which is not an existing class,
 * an existing interface or native type.
 */
class UndefinedTargetClassException extends LogicException
{
    /**
     * Constructor.
     * 
     * @param DeferredCallChain $callchain
     * @param mixed             $expected_target The wrong expected target
     */
    public function __construct(DeferredCallChain $callchain, $expected_target)
    {
        $this->message = "The expected target of {$callchain} is neither a existing class or interface nor a native type: " . $expected_target;
    }
}