# PHP Deferred Callchain
This class simply provides a way to define fluent chain of method calls before having
the instance you wan't to applty it to.
Once the expected instance is available, simply call the chain on it.
It can now also handle function calls on non-objects and access array entries.

[![Latest Stable Version](https://poser.pugx.org/jclaveau/php-deferred-callchain/v/stable)](https://packagist.org/packages/jclaveau/php-deferred-callchain)
[![License](https://poser.pugx.org/jclaveau/php-deferred-callchain/license)](https://packagist.org/packages/jclaveau/php-deferred-callchain)
[![Total Downloads](https://poser.pugx.org/jclaveau/php-deferred-callchain/downloads)](https://packagist.org/packages/jclaveau/php-deferred-callchain)
[![contributions welcome](https://img.shields.io/badge/contributions-welcome-brightgreen.svg?style=flat)](https://github.com/jclaveau/php-deferred-callchain/issues)

## Quality
[![Build Status](https://travis-ci.org/jclaveau/php-deferred-callchain.png?branch=master)](https://travis-ci.org/jclaveau/php-deferred-callchain)
[![codecov](https://codecov.io/gh/jclaveau/php-deferred-callchain/branch/master/graph/badge.svg)](https://codecov.io/gh/jclaveau/php-deferred-callchain)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jclaveau/php-deferred-callchain/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jclaveau/php-deferred-callchain/?branch=master)


## Installation
php-deferred-callchain is installable via [Composer](http://getcomposer.org)

    composer require jclaveau/php-deferred-callchain


## Usage

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


### Allowing a specific class, interface, type or a predefined instance as target of the later call.
You can force the target of your call chain to:

+ be an instance of a specific class
```php
$nameRobert = DeferredCallChain::new_("Alien")
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
When applying (new JClaveau\Async\DeferredCallChain( <instance id> ))->previousSuccessfullCall()->buggyCall('Robert') called in <file>:<line>
```

### Static calls
Static calls can be useful, especially for singletons. For some technical reasons explained here (https://github.com/jclaveau/php-deferred-callchain/issues/9),
the only way to support it is to call them as normal methods (e.g. with -> )
and look for it as a static method once we know it doesn't exist as as regular one.
```php
later(MyModel)->getInstance()->myNormalGetter();
// or
later(MyModel::class)->getInstance()->myNormalGetter();
```


## More
+ [API Reference](api_reference)
+ [Tests](tests/unit/DeferredCallChainTest.php)
