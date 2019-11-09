<?php
/**
 * ArrayAccessTrait
 *
 * @package php-deferred-callchain
 * @author  Jean Claveau
 */
namespace JClaveau\Async;
use       BadMethodCallException;

/**
 * Trait gathering unused array access required methods
 */
trait ArrayAccessTrait
{
    /**
     * Unused part of the ArrayAccess interface
     *
     * @param  $offset
     * @param  $value
     * @throws \BadMethodCallException
     */
    public function offsetSet(/** @scrutinizer ignore-unused */ $offset, $value)
    {
        throw new BadMethodCallException(
            "not implemented"
        );
    }

    /**
     * Unused part of the ArrayAccess interface
     *
     * @param  $offset
     * @throws \BadMethodCallException
     */
    public function offsetExists(/** @scrutinizer ignore-unused */ $offset)
    {
        throw new BadMethodCallException(
            "not implemented"
        );
    }

    /**
     * Unused part of the ArrayAccess interface
     *
     * @param  $offset
     * @throws \BadMethodCallException
     */
    public function offsetUnset(/** @scrutinizer ignore-unused */ $offset)
    {
        throw new BadMethodCallException(
            "not implemented"
        );
    }

    /**/
}
