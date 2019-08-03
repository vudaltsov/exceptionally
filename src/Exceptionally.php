<?php

declare(strict_types=1);

namespace VUdaltsov\Exceptionally;

use VUdaltsov\Exceptionally\Exception\NoCallableException;

final class Exceptionally
{
    /**
     * @var null|callable
     */
    private $callable;

    /**
     * @var array
     */
    private $args = [];

    /**
     * @var int
     */
    private $level = E_ALL;

    /**
     * @var bool
     */
    private $throwSuppressed = false;

    /**
     * @param mixed[] $args
     *
     * @throws \ErrorException
     *
     * @return mixed
     */
    public function __invoke(...$args)
    {
        return $this->call(...$args);
    }

    public function callable(callable $callable): self
    {
        $new = clone $this;
        $new->callable = $callable;

        return $new;
    }

    /**
     * @param mixed[] $args
     */
    public function args(...$args): self
    {
        $new = clone $this;
        $new->args = $args;

        return $new;
    }

    public function level(int $level): self
    {
        $new = clone $this;
        $new->level = $level;

        return $new;
    }

    public function throwSuppressed(bool $throwSuppressed = true): self
    {
        $new = clone $this;
        $new->throwSuppressed = $throwSuppressed;

        return $new;
    }

    /**
     * @param mixed[] $args
     *
     * @throws \ErrorException
     *
     * @return mixed
     */
    public function call(...$args)
    {
        if (null === $this->callable) {
            throw NoCallableException::create();
        }

        set_error_handler([$this, 'handler'], $this->level);

        try {
            return ($this->callable)(...($args ?: $this->args));
        } finally {
            restore_error_handler();
        }
    }

    public function handler(int $level, string $message, string $file, int $line): bool
    {
        if (!$this->throwSuppressed && 0 === error_reporting()) {
            return false;
        }

        switch ($level) {
            // case E_STRICT: https://wiki.php.net/rfc/reclassify_e_strict
            case E_WARNING:
                throw new Exception\WarningException($message, $file, $line);
            case E_NOTICE:
                throw new Exception\NoticeException($message, $file, $line);
            case E_RECOVERABLE_ERROR:
                throw new Exception\RecoverableErrorException($message, $file, $line);
            case E_DEPRECATED:
                throw new Exception\DeprecatedException($message, $file, $line);
            case E_USER_ERROR:
                throw new Exception\UserErrorException($message, $file, $line);
            case E_USER_WARNING:
                throw new Exception\UserWarningException($message, $file, $line);
            case E_USER_NOTICE:
                throw new Exception\UserNoticeException($message, $file, $line);
            case E_USER_DEPRECATED:
                throw new Exception\UserDeprecatedException($message, $file, $line);
            default:
                throw new \ErrorException($message, 0, $level, $file, $line);
        }
    }
}
