<?php

declare(strict_types=1);

namespace Qubus\Tests\Support\Serializer\Dummy\Complex\ValueObjects;

class PostId
{
    private mixed $postId {
        get => $this->postId;
    }

    /**
     * @param $id
     */
    public function __construct($id)
    {
        $this->postId = $id;
    }
}
