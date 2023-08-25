<?php

declare(strict_types=1);

namespace Qubus\Tests\Support\Serializer\Dummy\Complex;

use JsonSerializable;
use Qubus\Tests\Support\Serializer\Dummy\Complex\ValueObjects\CommentId;

class Comment implements JsonSerializable
{
    private CommentId $commentId;

    private array $dates;

    private string $comment;

    private User $user;

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

    /**
     * @return CommentId
     */
    public function getCommentId(): CommentId
    {
        return $this->commentId;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @return User
     */
    public function getUser(): \Qubus\Tests\Support\Serializer\Dummy\Complex\User
    {
        return $this->user;
    }

    public function jsonSerialize(): array
    {
        return
        [
            'commentId'   => $this->getCommentId(),
            'comment' => $this->getComment()
        ];
    }
}
