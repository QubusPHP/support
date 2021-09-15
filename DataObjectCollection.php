<?php

/**
 * Qubus\Support
 *
 * @link       https://github.com/QubusPHP/support
 * @copyright  2020 Joshua Parker <josh@joshuaparker.blog>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 *
 * @since      1.0.0
 */

declare(strict_types=1);

namespace Qubus\Support;

interface DataObjectCollection
{
    /**
     * @param object $value
     */
    public function add(string $key, $value): void;

    /**
     * @return object|null Returns the object or null if object does not exist.
     */
    public function get(string $key);
}
