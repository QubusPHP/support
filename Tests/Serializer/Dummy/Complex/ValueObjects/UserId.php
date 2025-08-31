<?php

declare(strict_types=1);

namespace Qubus\Tests\Support\Serializer\Dummy\Complex\ValueObjects;

class UserId
{
    private mixed $userId {
        get => $this->userId;
    }

    /**
     * @param $id
     */
    public function __construct($id)
    {
        $this->userId = $id;
    }
}
