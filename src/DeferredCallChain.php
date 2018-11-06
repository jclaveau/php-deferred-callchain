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
class DeferredCallChain implements \JsonSerializable
{
    protected $stack = [];

    /**
     */
    public static function new_()
    {
        return new static;
    }

    /**
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

        foreach ($this->stack as $call) {
            $string .= '->';
            $string .= $call['method'].'(';
            $string .= implode(', ', array_map(function($argument) {
                return var_export($argument, true);
            }, $call['arguments']));
            $string .= ')';
        }

        return $string;
    }

    /**
     * Invoking the instance produces the call of the stack
     */
    public function __invoke($target)
    {
        $out = $target;
        foreach ($this->stack as $call) {
            try {
                $out = call_user_func_array([$out, $call['method']], $call['arguments']);
            }
            catch (\Exception $e) {
                // Throw $e with the good stack (usage exception)
                throw $e;
            }
        }

        return $out;
    }

    /**/
}
