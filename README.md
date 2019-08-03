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
use Udaltsov\Exceptionally\Exception\NoticeException;

try {
    $value = exceptionallyCall(static function (array $array, string $offset): string {
        return $array[$offset];
    }, ['a' => 1], 'b');
} catch (NoticeException $exception) {
    throw new OutOfRangeException($exception->getMessage(), 0, $exception);
}
```

### exceptionally()

In advanced cases use the `exceptionally(): Exceptionally` function. It returns an **immutable** configurator. 

For instance, you can specify the error severity level (see [`set_error_handler(..., $error_types)`](https://www.php.net/manual/en/function.set-error-handler.php) for details). By default all errors (`E_ALL`) are captured.

```php
<?php

use function Udaltsov\Exceptionally\exceptionally;
use Udaltsov\Exceptionally\Exception\WarningException;

$fopen = exceptionally()
    ->callable('fopen')
    ->level(E_WARNING)
;

try {
    $handle = $fopen('list.json', 'wb');
} catch (WarningException $exception) {
    throw new TargetFileNotFoundException($exception->getMessage(), 0, $exception);
}
```

You can even set the default arguments.

```php
<?php

use function Udaltsov\Exceptionally\exceptionally;

$fopen = exceptionally()
    ->callable('fopen')
    ->args('list.json', 'wb')
;

$handle = $fopen();
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
