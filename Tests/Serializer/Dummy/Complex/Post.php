<?php

declare(strict_types=1);

namespace Qubus\Tests\Support\Serializer\Dummy\Complex;

use Qubus\Tests\Support\Serializer\Dummy\Complex\ValueObjects\PostId;
use Qubus\Tests\Support\Serializer\Dummy\Complex\User;

class Post
{
    /**
     * @param PostId $id
     * @param $title
     * @param $content
     * @param User  $user
     * @param array $comments
     */
    public function __construct(PostId $id, $title, $content, User $user, array $comments)
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
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return PostId
     */
    public function getPostId()
    {
        return $this->postId;
    }

    /**
     * @return mixed
     */
    public function getTitle()
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
