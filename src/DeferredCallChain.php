<?php
/**
 * DeferredCallChain
 *
 * @package php-deferred-callchain
 * @author  Jean Claveau
 */
namespace JClaveau\Async;

/**
 */
class DeferredCallChain implements \JsonSerializable, \ArrayAccess
{
    /** @var */
    protected $stack = [];

    /**
     */
    public static function new_()
    {
        return new static;
    }

    /**
     * ArrayAccess interface
     */
    public function &offsetGet($key)
    {
        $this->stack[] = [
            'entry' => $key,
        ];

        return $this;
    }

    /**
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
     * ArrayAccess interface
     */
    public function offsetSet($offset, $value)
    {
        throw new BadMethodCallException(
            "not implemented"
        );
    }

    /**
     * ArrayAccess interface
     */
    public function offsetExists($offset)
    {
        throw new BadMethodCallException(
            "not implemented"
        );
    }

    /**
     * ArrayAccess interface
     */
    public function offsetUnset($offset)
    {
        throw new BadMethodCallException(
            "not implemented"
        );
    }

    /**/
}
