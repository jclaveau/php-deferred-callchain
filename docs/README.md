# PHP Deferred Callchain

## Table of Contents

* [BadTargetClassException](#badtargetclassexception)
    * [__construct](#__construct)
* [BadTargetInterfaceException](#badtargetinterfaceexception)
    * [__construct](#__construct-1)
* [BadTargetTypeException](#badtargettypeexception)
    * [__construct](#__construct-2)
* [DeferredCallChain](#deferredcallchain)
    * [__construct](#__construct-3)
    * [offsetGet](#offsetget)
    * [__call](#__call)
    * [jsonSerialize](#jsonserialize)
    * [__toString](#__tostring)
    * [__invoke](#__invoke)
    * [offsetSet](#offsetset)
    * [offsetExists](#offsetexists)
    * [offsetUnset](#offsetunset)
* [TargetAlreadyDefinedException](#targetalreadydefinedexception)
    * [__construct](#__construct-4)
* [UndefinedTargetClassException](#undefinedtargetclassexception)
    * [__construct](#__construct-5)

## BadTargetClassException

Thrown when applying a deferred call chain on a target which is not
an instance of the expected class.



* Full name: \JClaveau\Async\Exceptions\BadTargetClassException
* Parent class: 


### __construct

Constructor.

```php
BadTargetClassException::__construct( \JClaveau\Async\DeferredCallChain $callchain, mixed $expected_target, mixed $target )
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$callchain` | **\JClaveau\Async\DeferredCallChain** |  |
| `$expected_target` | **mixed** | The expected class |
| `$target` | **mixed** |  |




---

## BadTargetInterfaceException

Thrown when applying a deferred call chain on a target which doesn't
implement the expected interface.



* Full name: \JClaveau\Async\Exceptions\BadTargetInterfaceException
* Parent class: 


### __construct

Constructor.

```php
BadTargetInterfaceException::__construct( \JClaveau\Async\DeferredCallChain $callchain, mixed $expected_target, mixed $target )
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$callchain` | **\JClaveau\Async\DeferredCallChain** |  |
| `$expected_target` | **mixed** | The expected interface |
| `$target` | **mixed** |  |




---

## BadTargetTypeException

Thrown when applying a deferred call chain on a target which is not
of the expected type.



* Full name: \JClaveau\Async\Exceptions\BadTargetTypeException
* Parent class: 


### __construct

Constructor.

```php
BadTargetTypeException::__construct( \JClaveau\Async\DeferredCallChain $callchain, mixed $expected_target, mixed $target )
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$callchain` | **\JClaveau\Async\DeferredCallChain** |  |
| `$expected_target` | **mixed** | The expected type |
| `$target` | **mixed** |  |




---

## DeferredCallChain

This class stores an arbitrary stack of calls (methods or array entries access)
that will be callable on any future variable.



* Full name: \JClaveau\Async\DeferredCallChain
* This class implements: \JsonSerializable, \ArrayAccess


### __construct

Constructor

```php
DeferredCallChain::__construct( string $class_type_interface_or_instance = null )
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$class_type_interface_or_instance` | **string** | The expected target class/type/interface/instance |




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
DeferredCallChain::__invoke( mixed $target = null ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$target` | **mixed** | The target to apply the callchain on |


**Return Value:**

The value returned once the call chain is called uppon $target



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

## TargetAlreadyDefinedException

Thrown when applying a deferred call chain on a target which is already
defined.



* Full name: \JClaveau\Async\Exceptions\TargetAlreadyDefinedException
* Parent class: 


### __construct

Constructor.

```php
TargetAlreadyDefinedException::__construct( \JClaveau\Async\DeferredCallChain $callchain, mixed $expected_target, mixed $target )
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$callchain` | **\JClaveau\Async\DeferredCallChain** |  |
| `$expected_target` | **mixed** | The target instance |
| `$target` | **mixed** |  |




---

## UndefinedTargetClassException

Thrown when defining an expected target which is not an existing class,
an existing interface or native type.



* Full name: \JClaveau\Async\Exceptions\UndefinedTargetClassException
* Parent class: 


### __construct

Constructor.

```php
UndefinedTargetClassException::__construct( \JClaveau\Async\DeferredCallChain $callchain, mixed $expected_target )
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$callchain` | **\JClaveau\Async\DeferredCallChain** |  |
| `$expected_target` | **mixed** | The wrong expected target |




---



--------
> This document was automatically generated from source code comments on 2019-11-07 using [phpDocumentor](http://www.phpdoc.org/) and [cvuorinen/phpdoc-markdown-public](https://github.com/cvuorinen/phpdoc-markdown-public)
