<?php

declare(strict_types=1);

namespace VUdaltsov\Exceptionally\Exception;

class NoCallableException extends \LogicException
{
    public static function create(): self
    {
        return new self('Callable is not set.');
    }
}
