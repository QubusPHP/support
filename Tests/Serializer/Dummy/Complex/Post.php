<?php

declare(strict_types=1);

namespace Qubus\Tests\Support\Serializer\Dummy\Complex;

use Qubus\Tests\Support\Serializer\Dummy\Complex\ValueObjects\PostId;

class Post
{
    public PostId $postId {
        get => $this->postId;
    }
    public string $title {
        get => $this->title;
    }
    public string $content {
        get => $this->content;
    }
    public User $author;
    public array $comments {
        get => $this->comments;
    }

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
     * @return User
     */
    public function getUser(): User
    {
        return $this->author;
    }
}
