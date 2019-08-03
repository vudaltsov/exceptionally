<?php

declare(strict_types=1);

namespace VUdaltsov\Exceptionally;

function exceptionally(): Exceptionally
{
    return new Exceptionally();
}

/**
 * @param mixed[] $args
 *
 * @throws \ErrorException
 *
 * @return mixed
 */
function exceptionallyCall(callable $callable, ...$args)
{
    return
        (new Exceptionally())
            ->callable($callable)
            ->call(...$args)
        ;
}
