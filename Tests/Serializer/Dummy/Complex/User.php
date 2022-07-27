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

use JetBrains\PhpStorm\ArrayShape;
use JsonSerializable;
use Qubus\Tests\Support\Serializer\Dummy\Complex\ValueObjects\UserId;

class User implements JsonSerializable
{
    private UserId $userId;

    private string $name;

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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    #[ArrayShape([
        'userId' => "mixed|\Qubus\Tests\Support\Serializer\Dummy\Complex\ValueObjects\UserId",
        'name' => "string"
    ])]
    public function jsonSerialize(): mixed
    {
        return
        [
            'userId'   => $this->getUserId(),
            'name' => $this->getName()
        ];
    }
}
