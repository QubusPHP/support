<?php

/**
 * Qubus\Support
 *
 * @link       https://github.com/QubusPHP/support
 * @copyright  2022
 * @author     Joshua Parker <joshua@joshuaparker.dev>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 */

declare(strict_types=1);

namespace Qubus\Support\Serializer\Strategy;

use function json_decode;
use function json_encode;

use const JSON_UNESCAPED_UNICODE;

class JsonStrategy implements Strategy
{
    public function serialize(mixed $data): bool|string
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function unserialize(mixed $data): bool|string|array|object
    {
        return json_decode($data, true);
    }
}
