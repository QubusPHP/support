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

namespace Qubus\Support\Collection;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Qubus\Support\Serializable;

interface Arrayable extends ArrayAccess, Countable, IteratorAggregate, Serializable
{
    /**
     * Removes all items from array instance.
     */
    public function clear(): void;

    /**
     * Returns an instance as an array.
     *
     * @return array
     */
    public function toArray(): array;

    /**
     * Returns `true` if array is empty.
     */
    public function isEmpty(): bool;
}
