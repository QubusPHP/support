<?php

declare(strict_types=1);

namespace Qubus\Support\Collection;

use OutOfRangeException;
use Qubus\Exception\Data\TypeException;

use function array_splice;
use function class_exists;
use function interface_exists;
use function is_array;
use function is_bool;
use function is_callable;
use function is_float;
use function is_int;
use function is_iterable;
use function is_object;
use function is_string;
use function sprintf;

class ArrayList extends Collection
{
    protected ?string $type = null;

    /**
     * @param string $type The expected type of elements
     *                     (e.g. 'string', 'int', 'float', 'bool', 'array', 'object', 'MyClass').
     */
    public function __construct(string $type)
    {
        $this->type = $type;
        parent::__construct([]);
    }

    /**
     * Add an element to the list.
     *
     * @param mixed $item
     * @return ArrayList
     * @throws TypeException
     */
    public function add(mixed $item): static
    {
        $this->assertType($item);

        return parent::add($item);
    }

    /**
     * Set an element at a specific index.
     *
     * @param int $index
     * @param mixed $element
     * @return ArrayList
     * @throws TypeException
     */
    public function set(int $index, mixed $element): static
    {
        $this->assertType($element);
        if (!isset($this->items[$index])) {
            throw new OutOfRangeException(sprintf("Index %s does not exist.", $index));
        }
        $this->items[$index] = $element;

        return $this;
    }

    /**
     * Get an element at a specific index.
     *
     * @param int $key
     * @return mixed
     * @throws TypeException
     */
    public function get(mixed $key): mixed
    {
        if (!is_int($key)) {
            throw new TypeException(sprintf("Index %s must be an integer.", $key));
        }

        if (!isset($this->items[$key])) {
            throw new OutOfRangeException(sprintf("Index %s does not exist.", $key));
        }

        return parent::get($key);
    }

    /**
     * Remove an element at a specific index.
     *
     * @param int $index
     * @return ArrayList
     */
    public function remove(int $index): static
    {
        if (!isset($this->items[$index])) {
            throw new OutOfRangeException(sprintf("Index %s does not exist.", $index));
        }
        array_splice($this->items, $index, 1);

        return $this;
    }

    /**
     * Remove a range of elements from the list.
     *
     * @param int $fromIndex Inclusive start index.
     * @param int $toIndex Exclusive end index.
     * @throws TypeException
     */
    public function removeRange(int $fromIndex, int $toIndex): void
    {
        if ($fromIndex < 0 || $toIndex < 0) {
            throw new OutOfRangeException("Indices cannot be negative.");
        }

        if ($fromIndex >= $toIndex) {
            throw new TypeException("fromIndex must be less than toIndex.");
        }

        $size = $this->size();
        if ($fromIndex >= $size || $toIndex > $size) {
            throw new OutOfRangeException(
                sprintf(
                    "Range [%d, %d) is out of bounds for list of size %d.",
                    $fromIndex,
                    $toIndex,
                    $size
                )
            );
        }

        $length = $toIndex - $fromIndex;
        array_splice($this->items, $fromIndex, $length);
    }

    /**
     * Get the size of the list.
     *
     * @return int
     */
    public function size(): int
    {
        return count($this->items);
    }

    /**
     * Type check helper.
     *
     * @param mixed $value
     * @throws TypeException
     */
    private function assertType(mixed $value): void
    {
        $expected = $this->type;
        $actual = get_debug_type($value);

        // Handle primitive types
        if (
                ($expected === 'int' && is_int($value)) ||
                ($expected === 'integer' && is_int($value)) ||
                ($expected === 'string' && is_string($value)) ||
                ($expected === 'float' && is_float($value)) ||
                ($expected === 'double' && is_float($value)) ||
                ($expected === 'bool' && is_bool($value)) ||
                ($expected === 'array' && is_array($value)) ||
                ($expected === 'object' && is_object($value)) ||
                ($expected === 'callable' && is_callable($value)) ||
                ($expected === 'iterable' && is_iterable($value))
        ) {
            return;
        }

        // Handle specific class names
        if (
                class_exists($expected) && $value instanceof $expected
                || interface_exists($expected) && $value instanceof $expected
        ) {
            return;
        }

        throw new TypeException(
            sprintf("Invalid type: expected %s, got %s.", $expected, $actual)
        );
    }

    /**
     * Create a shallow copy of the ArrayList instance.
     *
     * @return self
     */
    public function clone(): self
    {
        $copy = new self($this->type);
        $copy->items = $this->items; // shallow copy (same element references)
        return $copy;
    }

    /**
     * Clears the collection.
     *
     * @return void
     */
    public function clear(): void
    {
        $this->items = [];
    }

    public function type(): string
    {
        return $this->type;
    }
}
