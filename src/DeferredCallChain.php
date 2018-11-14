<?php
/**
 * DeferredCallChain
 *
 * @package php-deferred-callchain
 * @author  Jean Claveau
 */
namespace JClaveau\Async;
use       BadMethodCallException;

/**
 * This class stores an arbitrary stack of calls (methods or array entries access)
 * that will be callable on any future variable.
 */
class DeferredCallChain implements \JsonSerializable, \ArrayAccess
{
    /** @var array $stack The stack of deferred calls */
    protected $stack = [];

    /**
     * Simple factory to avoid (new DeferredCallChain)
     *
     * @return $this
     */
    public static function new_()
    {
        return new static;
    }

    /**
     * ArrayAccess interface
     *
     * @param string $key The entry to acces
     */
    public function &offsetGet($key)
    {
        $this->stack[] = [
            'entry' => $key,
        ];

        return $this;
    }

    /**
     * Stores any call in the the stack.
     *
     * @param  string $method
     * @param  array  $arguments
     *
     * @return $this
     */
    public final function __call($method, array $arguments)
    {
        $this->stack[] = [
            'method'    => $method,
            'arguments' => $arguments,
        ];

        return $this;
    }

    /**
     * For implementing JsonSerializable interface.
     *
     * @see https://secure.php.net/manual/en/jsonserializable.jsonserialize.php
     */
    public function jsonSerialize()
    {
        return $this->stack;
    }

    /**
     * Outputs the PHP code producing the current call chain while it's casted
     * as a string.
     *
     * @return string The PHP code corresponding to this call chain
     */
    public function __toString()
    {
        $string = '(new ' . get_called_class() . ')';

        foreach ($this->stack as $i => $call) {
            if (isset($call['method'])) {
                $string .= '->';
                $string .= $call['method'].'(';
                $string .= implode(', ', array_map(function($argument) {
                    return var_export($argument, true);
                }, $call['arguments']));
                $string .= ')';
            }
            else {
                $string .= '[' . var_export($call['entry'], true) . ']';
            }
        }

        return $string;
    }

    /**
     * Invoking the instance produces the call of the stack
     *
     * @param  $target The target to apply the callchain on
     * @return The value returned once the call chain is called uppon $target
     */
    public function __invoke($target)
    {
        $out = $target;
        foreach ($this->stack as $i => $call) {
            try {
                if (isset($call['method'])) {
                    $out = call_user_func_array([$out, $call['method']], $call['arguments']);
                }
                else {
                    $out = $out[ $call['entry'] ];
                }
            }
            catch (\Exception $e) {
                // Throw $e with the good stack (usage exception)
                throw $e;
            }
        }

        return $out;
    }

    /**
     * Unused part of the ArrayAccess interface
     *
     * @param  $offset
     * @param  $value
     * @throws \BadMethodCallException
     */
    public function offsetSet($offset, $value)
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
    public function offsetExists($offset)
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
    public function offsetUnset($offset)
    {
        throw new BadMethodCallException(
            "not implemented"
        );
    }

    /**/
}
