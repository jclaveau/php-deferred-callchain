# PHP Deferred Callchain
This class simply provides a way to define fluent chain of method calls before having
the instance you wan't to applty it to.
Once the expected instance is available, simply call the chain on it.


Quality
--------------
[![Build Status](https://travis-ci.org/jclaveau/php-deferred-callchain.png?branch=master)](https://travis-ci.org/jclaveau/php-deferred-callchain)
[![codecov](https://codecov.io/gh/jclaveau/php-deferred-callchain/branch/master/graph/badge.svg)](https://codecov.io/gh/jclaveau/php-deferred-callchain)
[![Maintainability](https://api.codeclimate.com/v1/badges/eb85279bcfb224b7af1c/maintainability)](https://codeclimate.com/github/jclaveau/php-deferred-callchain/maintainability)
[![contributions welcome](https://img.shields.io/badge/contributions-welcome-brightgreen.svg?style=flat)](https://github.com/jclaveau/php-deferred-callchain/issues)
[![Viewed](http://hits.dwyl.com/jclaveau/php-deferred-callchain.svg)](http://hits.dwyl.com/jclaveau/php-deferred-callchain)

## Installation
php-deferred-callchain is installable via [Composer](http://getcomposer.org)

    composer require jclaveau/php-deferred-callchain

## Usage
```php
$nameRobert = (new DeferredCallChain)
    ->setName('Muda')
    ->setFirstName('Robert')
    ;

$mySubjectIMissedBefore = new Human;
$robert = $nameRobert( $mySubjectIMissedBefore );

echo $robert->getFullName(); // => Robert Muda
```
