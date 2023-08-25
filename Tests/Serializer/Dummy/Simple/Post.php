<?php

declare(strict_types=1);

namespace Qubus\Tests\Support\Serializer\Dummy\Simple;

use Qubus\Tests\Support\Serializer\Dummy\Complex\ValueObjects\CommentId;
use Qubus\Tests\Support\Serializer\Dummy\Complex\ValueObjects\PostId;
use Qubus\Tests\Support\Serializer\Dummy\Complex\ValueObjects\UserId;

class Post
{
    private PostId $postId;

    private string $title;

    private string $body;

    private UserId $authorId;

    private array $comments = [];

    /**
     * @param PostId $postId
     * @param string $title
     * @param string $body
     * @param UserId $authorId
     */
    public function __construct(PostId $postId, string $title, string $body, UserId $authorId)
    {
        $this->postId = $postId;
        $this->title = $title;
        $this->body = $body;
        $this->authorId = $authorId;
    }

    /**
     * @param CommentId $commentId
     * @param UserId $user
     * @param string $comment
     * @param $created_at
     */
    public function addComment(CommentId $commentId, UserId $user, string $comment, $created_at): void
    {
        $this->comments[] = [
            'comment_id' => $commentId,
            'comment' => $comment,
            'user_id' => $user,
            'created_at' => $created_at,
        ];
    }

    /**
     * @param mixed $authorId
     *
     * @return $this
     */
    public function setAuthorId(UserId $authorId): static
    {
        $this->authorId = $authorId;

        return $this;
    }

    /**
     * @return UserId
     */
    public function getAuthorId(): UserId
    {
        return $this->authorId;
    }

    /**
     * @param string $body
     *
     * @return $this
     */
    public function setBody(string $body): static
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param PostId $postId
     * @return $this
     */
    public function setPostId(PostId $postId): static
    {
        $this->postId = $postId;

        return $this;
    }

    /**
     * @return PostId
     */
    public function getPostId(): PostId
    {
        return $this->postId;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }
}
