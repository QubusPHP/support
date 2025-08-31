<?php

declare(strict_types=1);

namespace Qubus\Tests\Support\Serializer\Dummy\Complex;

use JsonSerializable;
use Qubus\Tests\Support\Serializer\Dummy\Complex\ValueObjects\UserId;

class User implements JsonSerializable
{
    private UserId $userId {
        get => $this->userId;
    }

    private string $name {
        get => $this->name;
    }

    /**
     * @param UserId $id
     * @param $name
     */
    public function __construct(UserId $id, $name)
    {
        $this->userId = $id;
        $this->name = $name;
    }

    public function jsonSerialize(): array
    {
        return
        [
            'userId'   => $this->userId,
            'name' => $this->name
        ];
    }
}
