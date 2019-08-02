<?php

declare(strict_types=1);

namespace Udaltsov\Exceptionally\Exception;

class NoCallableException extends \LogicException
{
    public static function create(): self
    {
        return new self('Callable is not set.');
    }
}
