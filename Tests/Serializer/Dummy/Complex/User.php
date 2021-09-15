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
use Qubus\Tests\Support\Serializer\Dummy\Complex\ValueObjects\UserId;

class User implements JsonSerializable
{
    /**
     * @var UserId
     */
    private $userId;
    /**
     * @var
     */
    private $name;

    /**
     * @param UserId $id
     * @param $name
     */
    public function __construct(UserId $id, $name)
    {
        $this->userId = $id;
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getUserId(): UserId
    {
        return $this->userId;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    public function jsonSerialize()
    {
        return
        [
            'userId'   => $this->getUserId(),
            'name' => $this->getName()
        ];
    }
}
