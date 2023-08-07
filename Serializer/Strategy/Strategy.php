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
