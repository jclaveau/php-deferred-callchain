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
use       JClaveau\VisibilityViolator\VisibilityViolator;
use       BadMethodCallException;

/**
 * This class stores an arbitrary stack of calls (methods or array entries access)
 * that will be callable on any future variable.
 */
class DeferredCallChain implements \JsonSerializable, \ArrayAccess
{
    use \JClaveau\Traits\Fluent\New_;
    use FunctionCallTrait;
    
    /** @var array $stack The stack of deferred calls */
    protected $stack = [];

    /** @var mixed $expectedTarget The stack of deferred calls */
    protected $expectedTarget;

    /**
     * Constructor 
     * 
     * @param string $class_type_interface_or_instance The expected target class/type/interface/instance
     */
    public function __construct($class_type_interface_or_instance=null)
    {
        if ($class_type_interface_or_instance) {
            $this->expectedTarget = $class_type_interface_or_instance;
        }
    }

    /**
     * ArrayAccess interface
     *
     * @param string $key The entry to acces
     */
    public function &offsetGet($key)
    {
        $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];
        
        $this->stack[] = [
            'entry' => $key,
            'file'  => isset($caller['file']) ? $caller['file'] : null,
            'line'  => isset($caller['line']) ? $caller['line'] : null,
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
        $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];
        
        $this->stack[] = [
            'method'    => $method,
            'arguments' => $arguments,
            'file'      => isset($caller['file']) ? $caller['file'] : null,
            'line'      => isset($caller['line']) ? $caller['line'] : null,
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
        return $this->toString();
    }

    /**
     * Outputs the PHP code producing the current call chain while it's casted
     * as a string.
     *
     * @return string The PHP code corresponding to this call chain
     */
    protected function toString(array $options=[])
    {
        $target = isset($options['target']) ? $options['target'] : $this->expectedTarget;
        
        $string = '(new ' . get_called_class();
        $target && $string .= '(' . static::varExport($target, ['short_objects']) . ')';
        $string .= ')';

        foreach ($this->stack as $i => $call) {
            if (isset($call['method'])) {
                $string .= '->';
                $string .= $call['method'].'(';
                $string .= implode(', ', array_map(function($argument) {
                    return static::varExport($argument, ['short_objects']);
                }, $call['arguments']));
                $string .= ')';
            }
            else {
                $string .= '[' . static::varExport($call['entry'], ['short_objects']) . ']';
            }
            
            if (! empty($options['limit']) && $options['limit'] == $i) {
                break;
            }
        }

        return $string;
    }
    
    /**
     * Enhanced var_export() required for dumps.
     * 
     * @param  mixed  $variable
     * @param  array  $options max_length | alias_instances
     * @return string The PHP code of the variable
     */
    protected static function varExport($variable, array $options=[])
    {
        $options['max_length']    = isset($options['max_length']) ? $options['max_length'] : 512;
        $options['short_objects'] = ! empty($options['short_objects']) || in_array('short_objects', $options);
        
        $export = var_export($variable, true);
        
        if ($options['short_objects']) {
            if (is_object($variable)) {
                $export = ' ' . get_class($variable) . ' #' . spl_object_id($variable) . ' ';
            }
        }
        
        if (strlen($export) > $options['max_length']) {
            
            if (is_object($variable)) {
                $export = get_class($variable) . ' #' . spl_object_id($variable);
            }
            elseif (is_string($variable)) {
                $keep_length = floor(($options['max_length'] - 5) / 2);
                
                $export = substr($variable, 0, (int) $keep_length)
                    . ' ... '
                    . substr($variable, -$keep_length)
                    ;
            }
        }
        
        return $export;
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
     * Calling a method coded inside a magic __call can produce a 
     * BadMethodCallException and thus not be a callable.
     * 
     * @param mixed  $current_chained_subject
     * @param string $method_name
     * @param array  $arguments
     * 
     * @return bool $is_called
     */
    protected function checkMethodIsReallyCallable(
        &$current_chained_subject, 
        $method_name,
        $arguments
    ) {
        $is_called = true;
        try {
            $current_chained_subject = call_user_func_array(
                [$current_chained_subject, $method_name], 
                $arguments
            );
        }
        catch (\BadMethodCallException $e) {
            if ($this->exceptionTrownFromMagicCall(
                $e->getTrace(),
                $current_chained_subject,
                $method_name
            )) {
                $is_called = false;
            }
            else {
                throw $e;
            }
        }
        
        return $is_called;
    }

    /**
     * Checks if the exception having $trace is thrown from à __call
     * magic method.
     * 
     * @param  array  $trace
     * @param  object $current_chained_subject
     * @param  string $method_name
     * 
     * @return bool Whether or not the exception having the $trace has been
     *              thrown from a __call() method.
     */
    protected function exceptionTrownFromMagicCall(
        $trace, 
        $current_chained_subject,
        $method_name
    ) {
        // Before PHP 7, there is a raw for the non existing method called
        $call_user_func_array_position = PHP_VERSION_ID < 70000 ? 2 : 1;
        
        return  
                $trace[0]['function'] == '__call'
            &&  $trace[0]['class']    == get_class($current_chained_subject)
            &&  $trace[0]['args'][0]  == $method_name
            && (
                    $trace[$call_user_func_array_position]['file'] == __FILE__
                &&  $trace[$call_user_func_array_position]['function'] == 'call_user_func_array'
            )
            ;
    }

    /**
     * Invoking the instance produces the call of the stack
     *
     * @param  mixed $target The target to apply the callchain on
     * @return mixed The value returned once the call chain is called uppon $target
     */
    public function __invoke($target=null)
    {
        $out = $this->checkTarget($target);
        
        foreach ($this->stack as $i => $call) {
            $is_called = false;
            try {
                if (isset($call['method'])) {
                    if (is_callable([$out, $call['method']])) {
                        $is_called = $this->checkMethodIsReallyCallable(
                            $out,
                            $call['method'],
                            $call['arguments']
                        );
                    }
                    
                    if (! $is_called && is_callable($call['method'])) {
                        $arguments = $this->prepareArgs($call['arguments'], $out);
                        $out = call_user_func_array($call['method'], $arguments);
                        $is_called = true;
                    }
                    
                    if (! $is_called) {
                        throw new \BadMethodCallException(
                            $call['method'] . "() is neither a method of " . get_class($out)
                            . " nor a function"
                        );
                    }
                }
                else {
                    $out = $out[ $call['entry'] ];
                }
            }
            catch (\Exception $e) {
                
                $callchain_description = $this->toString([
                    'target' => $target,
                    'limit'  => $i,
                ]);
                
                VisibilityViolator::setHiddenProperty(
                    $e,
                    'message',
                    $e->getMessage()
                    . "\nWhen applying $callchain_description called in "
                    . $call['file'] . ':' . $call['line']
                );
                
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
