<?php

declare(strict_types=1);

namespace Udaltsov\Exceptionally;

use PHPUnit\Framework\TestCase;
use Udaltsov\Exceptionally\Exception\DeprecatedException;
use Udaltsov\Exceptionally\Exception\NoticeException;
use Udaltsov\Exceptionally\Exception\UserDeprecatedException;
use Udaltsov\Exceptionally\Exception\UserErrorException;
use Udaltsov\Exceptionally\Exception\UserNoticeException;
use Udaltsov\Exceptionally\Exception\UserWarningException;
use Udaltsov\Exceptionally\Exception\WarningException;

/**
 * @internal
 * @covers \Udaltsov\Exceptionally\Exceptionally
 *
 * @small
 */
final class ExceptionallyTest extends TestCase
{
    public function testWarning(): void
    {
        $this->expectException(WarningException::class);
        $this->expectExceptionMessage('Invalid argument');
        $this->expectExceptionCode(0);

        try {
            (new Exceptionally())
                ->callable(static function (): void {
                    readlink(__FILE__);
                })
                ->call()
            ;
        } catch (\ErrorException $exception) {
            static::assertSame(E_WARNING, $exception->getSeverity());

            throw $exception;
        }
    }

    public function testNotice(): void
    {
        $this->expectException(NoticeException::class);
        $this->expectExceptionMessage('Undefined index: a');
        $this->expectExceptionCode(0);

        try {
            (new Exceptionally())
                ->callable(static function (): void {
                    []['a'];
                })
                ->call()
            ;
        } catch (\ErrorException $exception) {
            static::assertSame(E_NOTICE, $exception->getSeverity());

            throw $exception;
        }
    }

    public function testDeprecated(): void
    {
        $this->expectException(DeprecatedException::class);
        $this->expectExceptionMessage('define(): Declaration of case-insensitive constants is deprecated');
        $this->expectExceptionCode(0);

        try {
            (new Exceptionally())
                ->callable(static function (): void {
                    \define('constant', 1, true);
                })
                ->call()
            ;
        } catch (\ErrorException $exception) {
            static::assertSame(E_DEPRECATED, $exception->getSeverity());

            throw $exception;
        }
    }

    /**
     * @dataProvider userErrorData
     */
    public function testUserError(string $error, string $exception): void
    {
        $this->expectException($exception);
        $this->expectExceptionMessage($error);
        $this->expectExceptionCode(0);

        try {
            (new Exceptionally())
                ->callable(static function () use ($error): void {
                    trigger_error($error, \constant($error));
                })
                ->call()
            ;
        } catch (\ErrorException $exception) {
            static::assertSame(\constant($error), $exception->getSeverity());

            throw $exception;
        }
    }

    public function userErrorData(): \Generator
    {
        yield ['E_USER_ERROR', UserErrorException::class];
        yield ['E_USER_WARNING', UserWarningException::class];
        yield ['E_USER_NOTICE', UserNoticeException::class];
        yield ['E_USER_DEPRECATED', UserDeprecatedException::class];
    }

    public function testDefaultArgs(): void
    {
        (new Exceptionally())
            ->callable(static function ($arg): void {
                self::assertSame('offset', $arg);
            })
            ->args('offset')
            ->call()
        ;
    }

    public function testThrowSuppressed(): void
    {
        $this->expectException(NoticeException::class);
        $this->expectExceptionMessage('Undefined index: a');
        $this->expectExceptionCode(0);

        (new Exceptionally())
            ->callable(static function (): void {
                @[]['a'];
            })
            ->throwSuppressed()
            ->call()
        ;
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testLevel(): void
    {
        (new Exceptionally())
            ->callable(static function (): void {
                @[]['a'];
            })
            ->throwSuppressed()
            ->level(E_WARNING)
            ->call()
        ;
    }
}
