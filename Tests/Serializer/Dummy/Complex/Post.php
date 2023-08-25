<?php

declare(strict_types=1);

namespace Qubus\Tests\Support\Serializer\Dummy\Complex;

use Qubus\Tests\Support\Serializer\Dummy\Complex\ValueObjects\PostId;

class Post
{
    private PostId $postId;
    private string $title;
    private string $content;
    private User $author;
    private array $comments;

    /**
     * @param PostId $id
     * @param string $title
     * @param string $content
     * @param User $user
     * @param array $comments
     */
    public function __construct(PostId $id, string $title, string $content, User $user, array $comments)
    {
        $this->postId = $id;
        $this->title = $title;
        $this->content = $content;
        $this->author = $user;
        $this->comments = $comments;
    }

    /**
     * @return array
     */
    public function getComments(): array
    {
        return $this->comments;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return PostId
     */
    public function getPostId(): PostId
    {
        return $this->postId;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->author;
    }
}
