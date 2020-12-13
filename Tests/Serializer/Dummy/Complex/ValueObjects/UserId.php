<?php

declare(strict_types=1);

namespace Qubus\Tests\Support\Serializer\Dummy\Complex\ValueObjects;

class UserId
{
    /**
     * @param $id
     */
    public function __construct($id)
    {
        $this->userId = $id;
    }

    /**
     * @return mixed
     */
    public function getUserId(): UserId
    {
        return $this->userId;
    }
}
