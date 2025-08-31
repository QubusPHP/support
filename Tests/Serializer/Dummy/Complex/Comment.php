<?php

declare(strict_types=1);

namespace Qubus\Tests\Support\Serializer\Dummy\Complex;

use JsonSerializable;
use Qubus\Tests\Support\Serializer\Dummy\Complex\ValueObjects\CommentId;

class Comment implements JsonSerializable
{
    public CommentId $commentId {
        get => $this->commentId;
    }

    private array $dates;

    public string $comment {
        get => $this->comment;
    }

    public User $user {
        get => $this->user;
    }

    /**
     * @param CommentId $id
     * @param string $comment
     * @param User      $user
     * @param array     $dates
     */
    public function __construct(CommentId $id, string $comment, User $user, array $dates)
    {
        $this->commentId = $id;
        $this->comment = $comment;
        $this->user = $user;
        $this->dates = $dates;
    }

    public function jsonSerialize(): array
    {
        return
        [
            'commentId'   => $this->commentId,
            'comment' => $this->comment
        ];
    }
}
