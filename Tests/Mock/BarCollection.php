<?php

declare(strict_types=1);

namespace Qubus\Tests\Support\Mock;

use Qubus\Support\Collection\Collection;

class BarCollection extends Collection
{
    protected function type(): string
    {
        return Bar::class;
    }
}
