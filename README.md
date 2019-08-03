# Exceptionally

A PHP library that converts errors into exceptions easily.

```php
<?php

use function Udaltsov\Exceptionally\exceptionallyCall;
use Udaltsov\Exceptionally\Exception\WarningException;

try {
    $handle = exceptionallyCall('fopen', 'data.xml', 'rb');
} catch (WarningException $exception) {
    throw new FailedToOpenFileException($exception->getMessage(), 0, $exception);
}
```

## Installation

```bash
composer require udaltsov/exceptionally
```

## Usage

### exceptionallyCall()

The `exceptionallyCall(callable $callable, mixed ...args): mixed` function allows to execute callables immediately. 

```php
<?php

use function Udaltsov\Exceptionally\exceptionallyCall;
use Udaltsov\Exceptionally\Exception\WarningException;

try {
    $file = 'data.xml';
    $handle = exceptionallyCall('touch', $file);
} catch (WarningException $exception) {
    throw FailedToTouchFileException::fromFile($file, $exception);
}
```

### exceptionally()

In advanced cases use the `exceptionally(): Exceptionally` function. It returns an [**immutable**](#immutability) configurator. 

For instance, you can specify the error severity level (see [`set_error_handler(..., $error_types)`](https://www.php.net/manual/en/function.set-error-handler.php) for details). By default all errors (`E_ALL`) are captured.

```php
<?php

use function Udaltsov\Exceptionally\exceptionally;
use Udaltsov\Exceptionally\Exception\NoticeException;

$accessor = exceptionally()
    ->callable(static function (array $array, string $offset): string {
        return $array[$offset];
    })
    ->level(E_NOTICE)
;

try {
    $value = $accessor(['a' => 1], 'b');
} catch (NoticeException $exception) {
    throw new OutOfRangeException($exception->getMessage(), 0, $exception);
}
```

You can even set the default arguments.

```php
<?php

use function Udaltsov\Exceptionally\exceptionally;

$mkdir = exceptionally()
    ->callable('mkdir')
    ->args(__DIR__.'/a', 0777, true)
;

$mkdir();
```

By default suppressed errors are not thrown, but you can enable that.

```php
<?php

use function Udaltsov\Exceptionally\exceptionally;

exceptionally()
    ->callable(static function (): void {
        @include __DIR__.'/script.php';
    })
    ->throwSuppressed()
    ->call()
;
```

### Immutability

The `exceptionally()` configurator is immutable. It returns a new object on each call (same as [PSR-7](https://www.php-fig.org/psr/psr-7/) Messages). Hence, you can safely reuse a preconfigured instance.

### Exceptions

Exceptionally throws subclasses of the native [`ErrorException`](https://www.php.net/manual/en/class.errorexception.php).

| Error level | Exception class |
| --- | --- |
| `E_WARNING` | `Udaltsov\Exceptionally\Exception\WarningException` |
| `E_NOTICE` | `Udaltsov\Exceptionally\Exception\NoticeException` |
| `E_RECOVERABLE_ERROR` | `Udaltsov\Exceptionally\Exception\RecoverableErrorException` |
| `E_DEPRECATED` | `Udaltsov\Exceptionally\Exception\DeprecatedException` |
| `E_USER_ERROR` | `Udaltsov\Exceptionally\Exception\UserErrorException` |
| `E_USER_WARNING` | `Udaltsov\Exceptionally\Exception\UserWarningException` |
| `E_USER_NOTICE` | `Udaltsov\Exceptionally\Exception\UserNoticeException` |
| `E_USER_DEPRECATED` | `Udaltsov\Exceptionally\Exception\UserDeprecatedException` |

Note that `E_ERROR`, `E_PARSE`, `E_CORE_ERROR`, `E_CORE_WARNING`, `E_COMPILE_ERROR`, `E_COMPILE_WARNING` levels are not supported, because they can not be handled with the [`set_error_handler`](https://www.php.net/manual/en/function.set-error-handler.php) function.

`E_STRICT` is not supported as well, because [it is not used in PHP 7 anymore](https://wiki.php.net/rfc/reclassify_e_strict).
