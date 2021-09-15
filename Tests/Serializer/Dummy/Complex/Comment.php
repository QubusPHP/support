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

namespace Qubus\Tests\Support\Serializer\Dummy\Complex;

use JsonSerializable;
use Qubus\Tests\Support\Serializer\Dummy\Complex\ValueObjects\CommentId;
use Qubus\Tests\Support\Serializer\Dummy\Complex\User;

class Comment implements JsonSerializable
{
    /**
     * @var
     */
    private $commentId;
    /**
     * @var array
     */
    private $dates;
    /**
     * @var string
     */
    private $comment;

    /**
     * @param CommentId $id
     * @param           $comment
     * @param User      $user
     * @param array     $dates
     */
    public function __construct(CommentId $id, $comment, User $user, array $dates)
    {
        $this->commentId = $id;
        $this->comment = $comment;
        $this->user = $user;
        $this->dates = $dates;
    }

    /**
     * @return mixed
     */
    public function getCommentId()
    {
        return $this->commentId;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    public function jsonSerialize()
    {
        return
        [
            'commentId'   => $this->getCommentId(),
            'comment' => $this->getComment()
        ];
    }
}
