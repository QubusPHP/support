<?php

/**
 * Qubus\Support
 *
 * @link       https://github.com/QubusPHP/support
 * @copyright  2022
 * @author     Joshua Parker <joshua@joshuaparker.dev>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 * @since      2.2.2
 */

declare(strict_types=1);

namespace Qubus\Support\Serializer\Strategy;

interface Strategy
{
    /**
     * @param mixed $data
     * @return bool|string
     */
    public function serialize(mixed $data): bool|string;

    /**
     * @param mixed $data
     * @return bool|string|array|object
     */
    public function unserialize(mixed $data): bool|string|array|object;
}
