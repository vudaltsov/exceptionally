<?php

declare(strict_types=1);

namespace Udaltsov\Exceptionally;

function exceptionally(): Exceptionally
{
    return new Exceptionally();
}

/**
 * @return mixed
 */
function exceptionallyRun(callable $callable, ...$args)
{
    return
        (new Exceptionally())
            ->callable($callable)
            ->run(...$args)
        ;
}
