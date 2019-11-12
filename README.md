# PHP Deferred Callchain
This class simply provides a way to define fluent chain of methods, functions, 
array access calls before having the target (object or native value) you wan't to apply it to.
Once the expected targets are available, simply call the chain on them as if it was a function.

[![Latest Stable Version](https://poser.pugx.org/jclaveau/php-deferred-callchain/v/stable)](https://packagist.org/packages/jclaveau/php-deferred-callchain)
[![License](https://poser.pugx.org/jclaveau/php-deferred-callchain/license)](https://packagist.org/packages/jclaveau/php-deferred-callchain)
[![Total Downloads](https://poser.pugx.org/jclaveau/php-deferred-callchain/downloads)](https://packagist.org/packages/jclaveau/php-deferred-callchain)
[![contributions welcome](https://img.shields.io/badge/contributions-welcome-brightgreen.svg?style=flat)](https://github.com/jclaveau/php-deferred-callchain/issues)

## Quality
[![Build Status](https://travis-ci.org/jclaveau/php-deferred-callchain.png?branch=master)](https://travis-ci.org/jclaveau/php-deferred-callchain)
[![codecov](https://codecov.io/gh/jclaveau/php-deferred-callchain/branch/master/graph/badge.svg)](https://codecov.io/gh/jclaveau/php-deferred-callchain)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jclaveau/php-deferred-callchain/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jclaveau/php-deferred-callchain/?branch=master)

## Overview 
```php
// having
class MyClass
{
    protected $name = 'unnamed';
    
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    
    public function nameEntries()
    {
        return [
            'my_entry_1' => $this->name . " 1",
            'my_entry_2' => $this->name . " 2",
        ];
    }
}

// We can define some chained calls (none is executed)
$doDummyCallsLater = DeferredCallChain::new_( MyClass::class )  // Targets must be MyClass instances
    ->nameEntries()['my_entry_2']                               // access array entry
    ->strtoupper()                                              // apply strtoupper() to it
    ;

// do whatever we want
// ...

// Get your targets
$myInstance1 = (new MyClass)->setName('foo');
$myInstance2 = (new MyClass)->setName('bar');

// Execute the callchain
echo $doDummyCallsLater( $myInstance1 ); // => FOO 2
echo $doDummyCallsLater( $myInstance2 ); // => BAR 2

```


## Installation
php-deferred-callchain is installable via [Composer](http://getcomposer.org)

    composer require jclaveau/php-deferred-callchain


## Testing
Tests are located [here](tests/unit/DeferredCallChainTest.php) and runnable
by calling

    ./phpunit


## Usage

  * [Fluent call chain](#fluent-call-chain)
  * [Functionnal and chained construction](#functionnal-and-chained-construction)
  * [Working with arrays](#working-with-arrays)
  * [Working with native types and functions](#working-with-native-types-and-functions)
  * [Specifying on which class, interface, type or instance, the chain is callable](#specifying-on-which-class-interface-type-or-instance-the-chain-is-callable)
  * [Calls provoking exceptions](#calls-provoking-exceptions)
  * [Static calls](#static-calls)
  * [API Reference](api_reference)


### Fluent call chain
```php
$nameRobert = DeferredCallChain::new_()
    ->setName('Muda')
    ->setFirstName('Robert')
    ;

$mySubjectIMissedBefore = new Human;
$robert = $nameRobert( $mySubjectIMissedBefore );

echo $robert->getFullName(); // => "Robert Muda"
echo (string) $nameRobert;   // => "(new JClaveau\Async\DeferredCallChain)->setName('Muda')->setFirstName('Robert')"
```


### Functionnal and chained construction

DeferredCallChain can be instanciated classically
```php
$nameRobert = (new DeferredCallChain(Human::class))
    ->setName('Muda')->setFirstName('Robert');
```

Statically
```php
$nameRobert = DeferredCallChain::new_(Human::class)
    ->setName('Muda')->setFirstName('Robert');
```

Or functionnaly
```php
$nameRobert = later(Human::class)
    ->setName('Muda')->setFirstName('Robert');
```


### Working with arrays
```php
$getSubColumnValue = (new DeferredCallChain)
    ['column_1']
    ['sub_column_3']
    ;

$sub_column_3_value = $getSubColumnValue( [
    'column_1' => [
        'sub_column_1' => 'lalala',
        'sub_column_2' => 'lololo',
        'sub_column_3' => 'lilili',
    ],
    'column_2' => [
        'sub_column_1' => 'lululu',
        'sub_column_2' => 'lelele',
        'sub_column_3' => 'lylyly',
    ],
] );

echo $sub_column_3_value;           // => "lilili"
echo (string) $getSubColumnValue;   // => "(new JClaveau\Async\DeferredCallChain)['column_1']['sub_column_3']"
```


### Working with native types and functions
The features above make calls to objects methods easy and async but when
their result is not an object, the fluent syntax has to stop, and the async
behavior also.

Based on Daniel S Deboer work https://github.com/danielsdeboer/pipe, 
support of chained function calls has been added.

```php
class MyClass
{
    public function getString()
    {
        return 'string';
    }
}

$upperifyMyClassString = DeferredCallChain::new_( MyClass::class )
    ->getString()
    ->strtoupper();

echo $upperifyMyClassString( new MyClass ); // prints "STRING"

```

Some functions do not use the subject of the fluent syntax as first argument.
In this case, giving '$$' as the parameter you want to be replaced by the subject. 

```php
class MyClass
{
    public function getSentence()
    {
        return 'such a funny lib to implement';
    }
}

$explodeMyClassSentence = DeferredCallChain::new_( MyClass::class )
    ->getSentence()
    ->explode(' ', '$$');

$explodeMyClassSentence( new MyClass ); // returns ['such', 'a', 'funny', 'lib', 'to', 'implement']

```


### Specifying on which class, interface, type or instance, the chain is callable
You can force the target of your call chain to:

+ be an instance of a specific class
```php
$nameRobert = DeferredCallChain::new_(Alien::class)
    ->setName('Muda')
    ->setFirstName('Robert')
    ;

$mySubjectIMissedBefore = new Human;
$robert = $nameRobert( $mySubjectIMissedBefore );

// throws BadTargetClassException

```

+ implement a specific interface

```php
$getCount = (new DeferredCallChain("\Traversable"))
    ->count()
    ;

$myCountableIMissedBefore = new CountableClass; // class implementing Countable

// throws BadTargetInterfaceException

```

+ be of a specific native type

```php
$nameRobert = DeferredCallChain::new_("array")
    ->setName('Muda')
    ->setFirstName('Robert')
    ;

$mySubjectIMissedBefore = new Human;
$robert = $nameRobert( $mySubjectIMissedBefore );

// throws BadTargetTypeException

```

+ be a specific instance given at construction

```php
$myTarget = new Human;
$nameRobert = DeferredCallChain::new_($myTarget)
    ->setName('Muda')
    ->setFirstName('Robert')
    ;

$robert = $nameRobert( new Human );

// throws TargetAlreadyDefinedException

```

### Calls provoking exceptions
As a call can be made far before it's effectivelly applied, exceptions
need more debug information for a smooth workflow. To achieve that, 
a line is added to every exception message thrown during a DeferredCallChain 
execution, pointing to the buggy call and where it is coded.

For example, an exception having as message ```An exception has been thrown by some user code```
will print
```
An exception has been thrown by some user code
When applying (new JClaveau\Async\DeferredCallChain( <instance id> ))->previousSuccessfullCall()->buggyCall('Robert') defined at <file>:<line>
```

### Static calls
Static calls can be useful, especially for singletons. For some technical reasons explained [here](https://github.com/jclaveau/php-deferred-callchain/issues/9),
the only way to support it is to call them as normal methods (e.g. with -> )
and look for it as a static method once we know it doesn't exist as a regular one.
```php
later(MyModel)->getInstance()->myNormalGetter();
// or
later(MyModel::class)->getInstance()->myNormalGetter();
```
