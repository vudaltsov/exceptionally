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

final class ExceptionallyTest extends TestCase
{
    public function testWarning(): void
    {
        $this->expectException(WarningException::class);
        $this->expectExceptionMessage('Invalid argument');
        $this->expectExceptionCode(0);

        (new Exceptionally())
            ->callable(static function (): void {
                readlink(__FILE__);
            })
            ->run()
        ;
    }

    public function testNotice(): void
    {
        $this->expectException(NoticeException::class);
        $this->expectExceptionMessage('Undefined index: a');
        $this->expectExceptionCode(0);

        (new Exceptionally())
            ->callable(static function (): void {
                []['a'];
            })
            ->run()
        ;
    }

    public function testDeprecated(): void
    {
        $this->expectException(DeprecatedException::class);
        $this->expectExceptionMessage('define(): Declaration of case-insensitive constants is deprecated');
        $this->expectExceptionCode(0);

        (new Exceptionally())
            ->callable(static function (): void {
                define('constant', 1, true);
            })
            ->run()
        ;
    }

    /**
     * @dataProvider userErrorData
     */
    public function testUserError(string $error, string $exception): void
    {
        $this->expectException($exception);
        $this->expectExceptionMessage($error);
        $this->expectExceptionCode(0);

        (new Exceptionally())
            ->callable(static function () use ($error): void {
                trigger_error($error, constant($error));
            })
            ->run()
        ;
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
            ->callable(static function ($arg) {
                self::assertSame('offset', $arg);
            })
            ->args('offset')
            ->run()
        ;
    }

    public function testIgnoreSuppressed(): void
    {
        $this->expectException(NoticeException::class);
        $this->expectExceptionMessage('Undefined index: a');
        $this->expectExceptionCode(0);

        (new Exceptionally())
            ->callable(static function (): void {
                @[]['a'];
            })
            ->ignoreSuppressed(false)
            ->run()
        ;
    }

    public function testLevel(): void
    {
        (new Exceptionally())
            ->callable(static function (): void {
                @[]['a'];
            })
            ->ignoreSuppressed(false)
            ->level(E_WARNING)
            ->run()
        ;

        self::assertSame('Undefined index: a', error_get_last()['message']);
    }

    public function testExceptionClass(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Undefined index: a');
        $this->expectExceptionCode(0);

        try {
            (new Exceptionally())
                ->callable(static function (): void {
                    []['a'];
                })
                ->exception(\RuntimeException::class)
                ->run()
            ;
        } catch (\Throwable $exception) {
            self::assertInstanceOf(NoticeException::class, $exception->getPrevious());

            throw $exception;
        }
    }

    public function testExceptionFactory(): void
    {
        $this->expectException(\RuntimeException::class);

        (new Exceptionally())
            ->callable(static function (): void {
                []['a'];
            })
            ->exception(static function (\ErrorException $error) {
                self::assertSame(E_NOTICE, $error->getSeverity());

                throw new \RuntimeException();
            })
            ->run()
        ;
    }
}
