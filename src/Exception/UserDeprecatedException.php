<?php

declare(strict_types=1);

namespace VUdaltsov\Exceptionally\Exception;

class UserDeprecatedException extends \ErrorException
{
    public function __construct(
        string $message,
        string $file,
        int $line,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, E_USER_DEPRECATED, $file, $line, $previous);
    }
}
