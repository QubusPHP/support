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

class NullStrategy implements Strategy
{
    public function serialize(mixed $value): bool|string
    {
        return json_encode($value);
    }

    public function unserialize(mixed $value): bool|string|array|object
    {
        return $value;
    }
}
