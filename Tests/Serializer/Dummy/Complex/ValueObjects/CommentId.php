<?php

declare(strict_types=1);

namespace Qubus\Tests\Support\Serializer\Dummy\Complex\ValueObjects;

class CommentId
{
    private mixed $commentId {
        get => $this->commentId;
    }

    /**
     * @param $id
     */
    public function __construct($id)
    {
        $this->commentId = $id;
    }
}
