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
[![Viewed](http://hits.dwyl.com/jclaveau/php-deferred-callchain.svg)](http://hits.dwyl.com/jclaveau/php-deferred-callchain)

## Installation
php-deferred-callchain is installable via [Composer](http://getcomposer.org)

    composer require jclaveau/php-deferred-callchain

## Usage
### Fluent call chain
```php
$nameRobert = (new DeferredCallChain)
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

## More
+ [Docs](docs)
+ [Tests](tests/unit/DeferredCallChainTest.php)

## Related
+ https://github.com/danielsdeboer/pipe
