<?php

declare(strict_types=1);

namespace Qubus\Tests\Support\Serializer\Dummy\Complex\ValueObjects;

class CommentId
{
    /**
     * @param $id
     */
    public function __construct($id)
    {
        $this->commentId = $id;
    }

    /**
     * @return mixed
     */
    public function getCommentId(): CommentId
    {
        return $this->commentId;
    }
}
