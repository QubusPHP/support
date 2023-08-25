<?php

declare(strict_types=1);

namespace Qubus\Tests\Support\Serializer\Dummy\Complex\ValueObjects;

class PostId
{
    private mixed $postId;

    /**
     * @param $id
     */
    public function __construct($id)
    {
        $this->postId = $id;
    }

    /**
     * @return mixed
     */
    public function getPostId(): PostId
    {
        return $this->postId;
    }
}
