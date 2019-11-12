<?php
/**
 * ExportTrait
 *
 * @package php-deferred-callchain
 * @author  Jean Claveau
 */
namespace JClaveau\Async;

/**
 * Trait gathering support of export functions like toString() or
 * jsonSerialize()
 */
trait ExportTrait
{
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
     * @param  array  $options target: mixed | max_parameter_length: int | short_objects: bool
     * @return string The PHP code corresponding to this call chain
     */
    public function toString(array $options=[])
    {
        $target = isset($options['target']) ? $options['target'] : $this->expectedTarget;
        $max_param_length = isset($options['max_parameter_length']) ? $options['max_parameter_length'] : 56;
        $short_objects = isset($options['short_objects']) ? $options['short_objects'] : true;
        
        $string = '(new ' . get_called_class();
        $target && $string .= '(' . static::varExport($target, [
            'short_objects' => $short_objects, 
            'max_length' => $max_param_length,
        ]) . ')';
        $string .= ')';

        foreach ($this->stack as $i => $call) {
            if (isset($call['method'])) {
                $string .= '->';
                $string .= $call['method'].'(';
                $string .= implode(', ', array_map(function($argument) use ($max_param_length, $short_objects) {
                    return static::varExport($argument, [
                        'short_objects' => $short_objects, 
                        'max_length' => $max_param_length
                    ]);
                }, $call['arguments']));
                $string .= ')';
            }
            else {
                $string .= '[' . static::varExport($call['entry'], [
                    'short_objects' => $short_objects, 
                    'max_length' => $max_param_length,
                ]) . ']';
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
     * @param  array  $options max_length: int | short_objects: bool
     * @return string The PHP code of the variable
     */
    protected static function varExport($variable, array $options=[])
    {
        $options['max_length']    = isset($options['max_length']) ? $options['max_length'] : 56;
        $options['short_objects'] = (! empty($options['short_objects'])) || in_array('short_objects', $options);
        
        $export = var_export($variable, true);
        
        if ($options['short_objects']) {
            if (is_object($variable)) {
                $export = ' ' . get_class($variable) . ' #' . spl_object_id($variable) . ' ';
            }
        }
        
        if (strlen($export) > $options['max_length']) {
            
            if (is_object($variable)) {
                // shortening short objects would only slow the workflow
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

    /**/
}
