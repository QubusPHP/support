<?php

declare(strict_types=1);

namespace Qubus\Tests\Support\Serializer\Dummy\Complex;

use JsonSerializable;
use Qubus\Tests\Support\Serializer\Dummy\Complex\ValueObjects\UserId;

class User implements JsonSerializable
{
    /**
     * @var UserId
     */
    private $userId;
    /**
     * @var
     */
    private $name;

    /**
     * @param UserId $id
     * @param $name
     */
    public function __construct(UserId $id, $name)
    {
        $this->userId = $id;
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getUserId(): UserId
    {
        return $this->userId;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    public function jsonSerialize()
    {
        return
        [
            'userId'   => $this->getUserId(),
            'name' => $this->getName()
        ];
    }
}
