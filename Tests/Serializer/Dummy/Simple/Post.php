<?php

/**
 * Qubus\Support
 *
 * @link       https://github.com/QubusPHP/support
 * @copyright  2020 Joshua Parker <josh@joshuaparker.blog>
 * @copyright  2015 Nil Portugués Calderó
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 *
 * @since      1.0.0
 */

declare(strict_types=1);

namespace Qubus\Tests\Support\Serializer\Dummy\Simple;

class Post
{
    /**
     * @var
     */
    private $postId;
    /**
     * @var
     */
    private $title;
    /**
     * @var
     */
    private $body;
    /**
     * @var
     */
    private $authorId;
    /**
     * @var array
     */
    private $comments = [];

    /**
     * @param $postId
     * @param $title
     * @param $body
     * @param $authorId
     */
    public function __construct($postId, $title, $body, $authorId)
    {
        $this->postId = $postId;
        $this->title = $title;
        $this->body = $body;
        $this->authorId = $authorId;
    }

    /**
     * @param $commentId
     * @param $user
     * @param $comment
     * @param $created_at
     */
    public function addComment($commentId, $user, $comment, $created_at)
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
    public function setAuthorId($authorId)
    {
        $this->authorId = $authorId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAuthorId()
    {
        return $this->authorId;
    }

    /**
     * @param mixed $body
     *
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed $commentId
     *
     * @return $this
     */
    public function setPostId($commentId)
    {
        $this->postId = $commentId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPostId()
    {
        return $this->postId;
    }

    /**
     * @param mixed $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }
}
