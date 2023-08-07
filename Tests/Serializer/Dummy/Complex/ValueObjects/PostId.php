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

namespace Qubus\Tests\Support\Serializer\Dummy\Complex\ValueObjects;

class PostId
{
    private mixed $postId;

    /**
     * @param $id
     */
    public function __construct($id)
    {
        $this->postId = $id;
    }

    /**
     * @return mixed
     */
    public function getPostId(): PostId
    {
        return $this->postId;
    }
}
