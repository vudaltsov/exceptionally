<?php

declare(strict_types=1);

namespace Udaltsov\Exceptionally;

function exceptionally(): Exceptionally
{
    return new Exceptionally();
}

/**
 * @param mixed[] $args
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
