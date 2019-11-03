<?php
/**
 * UndefinedTargetClassException
 *
 * @package php-deferred-callchain
 * @author  Jean Claveau
 */
namespace JClaveau\Async\Exceptions;
use       LogicException;

/**
 */
class UndefinedTargetClassException extends LogicException
{
    public function __construct($callchain, $expected_target)
    {
        $this->message = "The expected target of $callchain is neither a defined class nor a native type: ". $expected_target;
    }
    
    /**/
}
