<?php

declare(strict_types=1);

namespace Qubus\Tests\Support\Mock;

class Bar
{
    public int $id {
        get => $this->id;
    }

    public string $name {
        get => $this->name;
    }

    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
}
