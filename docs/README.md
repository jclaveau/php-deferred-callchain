# PHP Deferred Callchain

## Table of Contents

* [DeferredCallChain](#deferredcallchain)
    * [new_](#new_)
    * [offsetGet](#offsetget)
    * [__call](#__call)
    * [jsonSerialize](#jsonserialize)
    * [__toString](#__tostring)
    * [__invoke](#__invoke)
    * [offsetSet](#offsetset)
    * [offsetExists](#offsetexists)
    * [offsetUnset](#offsetunset)

## DeferredCallChain

This class stores an arbitrary stack of calls (methods or array entries access)
that will be callable on any future variable.



* Full name: \JClaveau\Async\DeferredCallChain
* This class implements: \JsonSerializable, \ArrayAccess


### new_

Simple factory to avoid (new DeferredCallChain)

```php
DeferredCallChain::new_(  ): $this
```



* This method is **static**.



---

### offsetGet

ArrayAccess interface

```php
DeferredCallChain::offsetGet( string $key )
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$key` | **string** | The entry to acces |




---

### __call

Stores any call in the the stack.

```php
DeferredCallChain::__call( string $method, array $arguments ): $this
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$method` | **string** |  |
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

Outputs the PHP code producing the current call chain while it's casted
as a string.

```php
DeferredCallChain::__toString(  ): string
```





**Return Value:**

The PHP code corresponding to this call chain



---

### __invoke

Invoking the instance produces the call of the stack

```php
DeferredCallChain::__invoke(  $target ): \JClaveau\Async\The
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$target` | **** | The target to apply the callchain on |


**Return Value:**

value returned once the call chain is called uppon $target



---

### offsetSet

Unused part of the ArrayAccess interface

```php
DeferredCallChain::offsetSet(  $offset,  $value )
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$offset` | **** |  |
| `$value` | **** |  |




---

### offsetExists

Unused part of the ArrayAccess interface

```php
DeferredCallChain::offsetExists(  $offset )
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$offset` | **** |  |




---

### offsetUnset

Unused part of the ArrayAccess interface

```php
DeferredCallChain::offsetUnset(  $offset )
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$offset` | **** |  |




---



--------
> This document was automatically generated from source code comments on 2018-11-14 using [phpDocumentor](http://www.phpdoc.org/) and [cvuorinen/phpdoc-markdown-public](https://github.com/cvuorinen/phpdoc-markdown-public)
