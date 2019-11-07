<?php
/**
 * FunctionCallTrait
 *
 * @package php-deferred-callchain
 * @author  Daniel S. Deboer
 * @author  Jean Claveau
 */
namespace JClaveau\Async;

/**
 * This trait is an almost pure copy of the the argument handling of the 
 * Pipe class https://github.com/danielsdeboer/pipe.
 */
trait FunctionCallTrait
{
    /**
     * @var string $placeholder The value that will be replaced by the chain subject
     */
    protected $placeholder = '$$';
    
    /**
     * Prepare the arguments list.
     * @param array $args
     * @param mixed $value
     * @return array
     */
    protected function prepareArgs (array $args, $value)
    {
        return $this->hasPlaceholder($args)
            ? $this->replacePlaceholderWithValue($args, $value)
            : $this->addValueAsFirstArg($args, $value);
    }

    /**
     * Check if an array contains an element matching the placeholder.
     * @param array $args
     * @return bool
     */
    protected function hasPlaceholder (array $args)
    {
        return in_array($this->placeholder, $args, true);
    }

    /**
     * Add the value as the first argument in the arguments list.
     * @param array $args
     * @param mixed $value
     * @return array
     */
    protected function addValueAsFirstArg (array $args, $value)
    {
        array_unshift($args, $value);

        return $args;
    }

    /**
     * Replace any occurrence of the placeholder with the value.
     * @param array $args
     * @param mixed $value
     * @return array
     */
    protected function replacePlaceholderWithValue (array $args, $value)
    {
        return array_map(function ($arg) use ($value) {
            return $arg === $this->placeholder
                ? $value
                : $arg;
        }, $args);
    }

    /**/
}
