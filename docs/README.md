# PHP Deferred Callchain

## Table of Contents

* [DeferredCallChain](#deferredcallchain)
    * [new_](#new_)
    * [__call](#__call)
    * [jsonSerialize](#jsonserialize)
    * [__toString](#__tostring)
    * [__invoke](#__invoke)

## DeferredCallChain





* Full name: \JClaveau\Async\DeferredCallChain
* This class implements: \JsonSerializable


### new_



```php
DeferredCallChain::new_(  )
```



* This method is **static**.



---

### __call



```php
DeferredCallChain::__call(  $method, array $arguments )
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$method` | **** |  |
| `$arguments` | **array** |  |




---

### jsonSerialize

For implementing JsonSerializable interface.

```php
DeferredCallChain::jsonSerialize(  )
```






**See Also:**

* https://secure.php.net/manual/en/jsonserializable.jsonserialize.php 

---

### __toString



```php
DeferredCallChain::__toString(  )
```







---

### __invoke

Invoking the instance produces the call of the stack

```php
DeferredCallChain::__invoke(  $target )
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$target` | **** |  |




---



--------
> This document was automatically generated from source code comments on 2018-11-06 using [phpDocumentor](http://www.phpdoc.org/) and [cvuorinen/phpdoc-markdown-public](https://github.com/cvuorinen/phpdoc-markdown-public)
