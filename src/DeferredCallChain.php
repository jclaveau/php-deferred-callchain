<?php
/**
 * DeferredCallChain
 *
 * @package php-deferred-callchain
 * @author  Jean Claveau
 */
namespace JClaveau\Async;
use       JClaveau\Async\Exceptions\BadTargetClassException;
use       JClaveau\Async\Exceptions\BadTargetTypeException;
use       JClaveau\Async\Exceptions\UndefinedTargetClassException;
use       JClaveau\Async\Exceptions\BadTargetInterfaceException;
use       JClaveau\Async\Exceptions\TargetAlreadyDefinedException;
use       BadMethodCallException;

/**
 * This class stores an arbitrary stack of calls (methods or array entries access)
 * that will be callable on any future variable.
 */
class DeferredCallChain implements \JsonSerializable, \ArrayAccess
{
    use \JClaveau\Traits\Fluent\New_;
    
    /** @var array $stack The stack of deferred calls */
    protected $stack = [];

    /** @var mixed $expectedTarget The stack of deferred calls */
    protected $expectedTarget;

    /**
     * Constructor 
     * 
     * @param string $key The entry to acces
     */
    public function __construct($class_type_or_instance=null)
    {
        if ($class_type_or_instance) {
            $this->expectedTarget = $class_type_or_instance;
        }
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
        $string = '(new ' . get_called_class();
        if (is_string($this->expectedTarget)) {
            $string .= '(' . var_export($this->expectedTarget, true) . ')';
        }
        elseif (is_object($this->expectedTarget)) {
            $string .= '( ' . get_class($this->expectedTarget) . '#' . spl_object_id($this->expectedTarget) . ' )';
        }
        $string .= ')';

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
     * Checks that the provided target matches the type/class/interface
     * given during construction.
     * 
     * @param  mixed $target
     * @return mixed $target Checked
     */
    protected function checkTarget($target)
    {
        if (is_object($this->expectedTarget)) {
            if ($target) {
                throw new TargetAlreadyDefinedException($this, $this->expectedTarget, $target);
            }
            
            $out = $this->expectedTarget;
        }
        elseif (is_string($this->expectedTarget)) {
            if (class_exists($this->expectedTarget)) {
                if (! $target instanceof $this->expectedTarget) {
                    throw new BadTargetClassException($this, $this->expectedTarget, $target);
                }
            }
            elseif (interface_exists($this->expectedTarget)) {
                if (! $target instanceof $this->expectedTarget) {
                    throw new BadTargetInterfaceException($this, $this->expectedTarget, $target);
                }
            }
            elseif (type_exists($this->expectedTarget)) {
                if (gettype($target) != $this->expectedTarget) {
                    throw new BadTargetTypeException($this, $this->expectedTarget, $target);
                }
            }
            else {
                throw new UndefinedTargetClassException($this, $this->expectedTarget);
            }
            
            $out = $target;
        }
        else {
            $out = $target;
        }
        
        return $out;
    }

    /**
     * Invoking the instance produces the call of the stack
     *
     * @param  $target The target to apply the callchain on
     * @return The value returned once the call chain is called uppon $target
     */
    public function __invoke($target=null)
    {
        $out = $this->checkTarget($target);
        
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
