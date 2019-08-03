<?php

declare(strict_types=1);

namespace VUdaltsov\Exceptionally;

use PHPUnit\Framework\TestCase;
use VUdaltsov\Exceptionally\Exception\DeprecatedException;
use VUdaltsov\Exceptionally\Exception\NoticeException;
use VUdaltsov\Exceptionally\Exception\UserDeprecatedException;
use VUdaltsov\Exceptionally\Exception\UserErrorException;
use VUdaltsov\Exceptionally\Exception\UserNoticeException;
use VUdaltsov\Exceptionally\Exception\UserWarningException;
use VUdaltsov\Exceptionally\Exception\WarningException;

/**
 * @internal
 * @covers \VUdaltsov\Exceptionally\Exceptionally
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

    public function testClosureFileAndLine(): void
    {
        try {
            (new Exceptionally())
                ->callable(static function (): void {
                    []['a'];
                })
                ->call()
            ;
        } catch (\ErrorException $exception) {
            static::assertSame(__FILE__, $exception->getFile());
            static::assertSame(__LINE__ - 6, $exception->getLine());
        }
    }

    public function testInvokableFileAndLine(): void
    {
        try {
            (new Exceptionally())
                ->callable(new class() {
                    public function __invoke(): void
                    {
                        []['a'];
                    }
                })
                ->call()
            ;
        } catch (\ErrorException $exception) {
            static::assertSame(__FILE__, $exception->getFile());
            static::assertSame(__LINE__ - 7, $exception->getLine());
        }
    }

    public function testStringFileAndLine(): void
    {
        try {
            (new Exceptionally())
                ->callable('readlink')
                ->call(__FILE__)
            ;
        } catch (\ErrorException $exception) {
            static::assertSame(__FILE__, $exception->getFile());
            static::assertSame(__LINE__ - 4, $exception->getLine());
        }
    }
}
