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

namespace Qubus\Support;

interface DataObjectCollection
{
    public function add(string $key, object $value): void;

    /**
     * @return object|null Returns the object or null if object does not exist.
     */
    public function get(string $key): ?object;
}
