# PHP Deferred Callchain
This class simply provides a way to define fluent chain of method calls before having
the instance you wan't to applty it to.
Once the expected instance is available, simply call the chain on it.


Quality
--------------
[![Build Status](https://travis-ci.org/jclaveau/php-deferred-callchain.png?branch=master)](https://travis-ci.org/jclaveau/php-deferred-callchain)
[![codecov](https://codecov.io/gh/jclaveau/php-deferred-callchain/branch/master/graph/badge.svg)](https://codecov.io/gh/jclaveau/php-deferred-callchain)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jclaveau/php-deferred-callchain/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jclaveau/php-deferred-callchain/?branch=master)
[![contributions welcome](https://img.shields.io/badge/contributions-welcome-brightgreen.svg?style=flat)](https://github.com/jclaveau/php-deferred-callchain/issues)

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

$upperifyMyClassString = DeferredCallChain( MyClass:class )
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

$explodeMyClassSentence = DeferredCallChain( MyClass:class )
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

## More
+ [Docs](docs)
+ [Tests](tests/unit/DeferredCallChainTest.php)
