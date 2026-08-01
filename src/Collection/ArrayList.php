<?php

declare(strict_types=1);

namespace Qubus\Support\Collection;

use OutOfRangeException;
use Qubus\Exception\Data\TypeException;

use function array_key_exists;
use function array_splice;
use function array_values;
use function is_int;
use function is_iterable;
use function sprintf;

final class ArrayList extends Collection
{
    protected string $type;

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
     * @return self
     * @throws TypeException
     */
    public function add(mixed $item): ArrayList
    {
        $this->assertType($item);
        parent::add($item);
        return $this;
    }

    /**
     * Set an element at a specific index.
     *
     * @param int $index
     * @param mixed $element
     * @return self
     * @throws TypeException
     */
    public function set(int $index, mixed $element): self
    {
        $this->assertExistingIndex($index);
        $this->assertType($element);
        $this->items[$index] = $element;

        return $this;
    }

    /**
     * Get an element at a specific index.
     *
     * @param mixed $key
     * @return mixed
     * @throws TypeException
     */
    public function get(mixed $key): mixed
    {
        if (!is_int($key)) {
            throw new TypeException(sprintf("Index must be an integer; got %s.", get_debug_type($key)));
        }

        $this->assertExistingIndex($key);

        return parent::get($key);
    }

    /**
     * Set an element using array-access syntax.
     *
     * A null offset appends; an integer offset must already exist so that the
     * list can never become sparse.
     *
     * @throws TypeException
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($offset === null) {
            $this->add($value);
            return;
        }

        if (!is_int($offset)) {
            throw new TypeException(sprintf("Index must be an integer; got %s.", get_debug_type($offset)));
        }

        $this->set($offset, $value);
    }

    /**
     * Remove an element using array-access syntax and reindex the list.
     *
     * @throws TypeException
     */
    public function offsetUnset(mixed $offset): void
    {
        if (!is_int($offset)) {
            throw new TypeException(sprintf("Index must be an integer; got %s.", get_debug_type($offset)));
        }

        $this->remove($offset);
    }

    /**
     * Remove an element at a specific index.
     *
     * @param int $index
     * @return self
     */
    public function remove(int $index): self
    {
        $this->assertExistingIndex($index);
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

        if ($fromIndex > $toIndex) {
            throw new TypeException("fromIndex must not be greater than toIndex.");
        }

        $size = $this->size();
        if ($fromIndex > $size || $toIndex > $size) {
            throw new OutOfRangeException(
                sprintf(
                    "Range [%d, %d) is out of bounds for list of size %d.",
                    $fromIndex,
                    $toIndex,
                    $size
                )
            );
        }

        if ($fromIndex === $toIndex) {
            return;
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
     * Add an element to the end of the list.
     *
     * @throws TypeException
     */
    public function push(mixed $value): self
    {
        return $this->add($value);
    }

    /**
     * Replace the element at an existing integer index.
     *
     * @throws TypeException
     */
    public function put(mixed $key, mixed $value): self
    {
        if (!is_int($key)) {
            throw new TypeException(sprintf("Index must be an integer; got %s.", get_debug_type($key)));
        }

        return $this->set($key, $value);
    }

    /**
     * Replace the contents after validating every value.
     *
     * Validation is completed before mutation, so a failure leaves the list
     * unchanged. Keys are discarded to maintain list semantics.
     *
     * @param array<mixed> $items
     * @throws TypeException
     */
    public function unserialize(array $items): void
    {
        foreach ($items as $item) {
            $this->assertType($item);
        }

        $this->items = array_values($items);
    }

    /**
     * Type check helper.
     *
     * @param mixed $value
     * @throws TypeException
     */
    private function assertType(mixed $value): void
    {
        if ($this->checkType($this->type, $value) || ($this->type === 'iterable' && is_iterable($value))) {
            return;
        }

        throw new TypeException(
            sprintf("Invalid type: expected %s, got %s.", $this->type, get_debug_type($value))
        );
    }

    private function assertExistingIndex(int $index): void
    {
        if ($index < 0 || !array_key_exists($index, $this->items)) {
            throw new OutOfRangeException(sprintf("Index %s does not exist.", $index));
        }
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
