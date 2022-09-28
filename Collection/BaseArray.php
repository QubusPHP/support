<?php

/**
 * Qubus\Support
 *
 * @link       https://github.com/QubusPHP/support
 * @copyright  2022
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 *
 * @author     Joshua Parker <josh@joshuaparker.blog>
 * @since      2.2.2
 */

declare(strict_types=1);

namespace Qubus\Support\Collection;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Serializable;
use Traversable;

use function json_decode;
use function json_encode;
use function serialize;
use function unserialize;

use const JSON_PRETTY_PRINT;

/**
 * Borrowed from ramsey/collection
 */
abstract class BaseArray implements Arrayable
{
    /**
     * The items of this array.
     *
     * @var array
     */
    protected array $items = [];

    /**
     * Constructs a new array object.
     *
     * @param array $items The initial items to add to array.
     */
    public function __construct(array $items = [])
    {
        foreach ($items as $key => $value) {
            $this->items[$key] = $value;
        }
    }

    /**
     * Returns array as iterator.
     *
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    /**
     * Returns `true` if the given offset exists in the array.
     *
     * @param mixed $offset The offset to check.
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->items[$offset]);
    }

    /**
     * Returns the value at the specified offset.
     *
     * @param mixed $offset The offset for which a value should be returned.
     * @return mixed The value stored at the offset, or null if the offset
     *               does not exist.
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->items[$offset] ?? null;
    }

    /**
     * Sets the given value to the given offset in the array.
     *
     * @param mixed $offset The offset to set.
     * @param mixed $value The value to set at the given offset.
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($offset === null) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    /**
     * Removes the given offset and its value from the array.
     *
     * @param mixed $offset The offset to remove from the array.
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->items[$offset]);
    }

    /**
     * Returns a JSON string.
     *
     * @return string.
     */
    public function serialize(): string
    {
        return json_encode(value: $this->items, flags: JSON_PRETTY_PRINT);
    }

    /**
     * Converts a serialized string representation into an instance object.
     *
     * @param array $items A PHP array to unserialize.
     */
    public function unserialize(array $items): void
    {
        $items = json_decode(json: json_encode(value: $items));

        $this->items = $items;
    }

    /**
     * Returns the number of items in the array.
     */
    public function count(): int
    {
        return count($this->items);
    }

    public function clear(): void
    {
        $this->items = [];
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return $this->items;
    }

    /**
     * @inheritDoc
     */
    public function isEmpty(): bool
    {
        return count($this->items) === 0;
    }
}
