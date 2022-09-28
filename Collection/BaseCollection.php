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

use Qubus\Exception\Data\TypeException;
use Qubus\Support\ArrayHelper;
use ReflectionException;

use function count;
use function is_callable;
use function Qubus\Support\Helpers\is_null__;

abstract class BaseCollection extends BaseArray implements Collectionable
{
    use CollectionTypeAware;
    use ValueToStringAware;
    use ValueExtractorAware;

    public function __construct(protected string $collectionType, protected array $items)
    {
        parent::__construct($this->items);
    }

    /**
     * Add an item to the collection.
     *
     * @param mixed $item
     * @return $this
     */
    public function add(mixed $item): static
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * @throws TypeException
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($this->checkType($this->getType(), $value) === false) {
            throw new TypeException(
                'Value must be of type ' . $this->getType() . '; value is '
                . $this->toolValueToString($value)
            );
        }

        if ($offset === null) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    /**
     * Returns the type of the collection.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->collectionType;
    }

    /**
     * Returns `true` if this collection contains the specified element.
     *
     * @param mixed $element The element to check whether the collection contains.
     * @param bool $strict Whether to perform a strict type check on the value.
     */
    public function contains(mixed $element, bool $strict = true): bool
    {
        return in_array($element, $this->items, $strict);
    }

    /**
     * Returns a new instance of the collection with the callback function
     * $callable applied to each item
     *
     * @param callable $callable
     * @return $this
     */
    public function map(callable $callable): BaseCollection
    {
        $keys = array_keys($this->items);
        $results = array_map($callable, $this->items, $keys);

        return new static($results);
    }

    /**
     * Applies the callback function $callable to each item in the collection.
     *
     * @param callable $callable
     * @return $this
     */
    public function each(callable $callable): static
    {
        foreach ($this->items as $key => $item) {
            if ($callable($item, $key) === false) {
                break;
            }
        }

        return $this;
    }

    /**
     * Flip the items in the collection.
     *
     * @return static
     */
    public function flip(): static
    {
        return new static(array_flip($this->items));
    }

    /**
     * Filter the collection items through the callable.
     *
     * @param callable $callable
     * @return BaseCollection
     */
    public function filter(callable $callable): BaseCollection
    {
        $results = [];

        foreach ($this->items as $key => $item) {
            if ($callable($item, $key)) {
                $results[] = $item;
            }
        }

        return new static($results);
    }

    /**
     * Get the specified item from the collection.
     *
     * @param mixed $key
     * @return mixed
     * @throws ReflectionException
     * @throws TypeException
     */
    public function get(mixed $key): mixed
    {
        return ArrayHelper::getInstance()->get($this->items, $key);
    }

    /**
     * Slice the underlying collection array.
     *
     * @param  int  $offset
     * @param  int|null  $length
     * @return static
     */
    public function slice(int $offset, ?int $length = null): static
    {
        return new static(array_slice($this->items, $offset, $length, true));
    }

    /**
     * Create a new collection consisting of every n-th element.
     *
     * @param  int  $step
     * @param  int  $offset
     * @return static
     */
    public function nth(int $step, int $offset = 0): static
    {
        $new = [];

        $position = 0;

        foreach ($this->slice($offset)->items as $item) {
            if ($position % $step === 0) {
                $new[] = $item;
            }

            $position++;
        }

        return new static($new);
    }

    /**
     * Reject the collection items through the callable.
     *
     * @param callable $callable
     * @return Collection|BaseCollection
     */
    public function reject(callable $callable): Collection|BaseCollection
    {
        return $this->filter(function ($item, $key) use ($callable) {
            return ! $callable($item, $key);
        });
    }

    /**
     * Push an item to the collection.
     *
     * @param mixed $value
     * @return BaseCollection
     */
    public function push(mixed $value): static
    {
        $this->itemSet($value);

        return $this;
    }

    /**
     * Put the specified item in the collection with the given key.
     *
     * @param mixed $key
     * @param mixed $value
     * @return BaseCollection
     */
    public function put(mixed $key, mixed $value): static
    {
        $this->itemSet($value, $key);

        return $this;
    }

    /**
     * Returns a new Collection instance containing an
     * indexed array of values.
     *
     * @return self
     */
    public function values(): static
    {
        return new static(array_values($this->items));
    }

    /**
     * Returns a new Collection instance containing a
     * flattened array of items.
     *
     * @return self
     * @throws ReflectionException
     */
    public function flatten(): static
    {
        return new static(ArrayHelper::getInstance()->flatten($this->items));
    }

    /**
     * Sort the collection of item values through a user-defined
     * comparison function.
     *
     * @param callable|null $callback
     * @return static
     */
    public function sort(callable $callback = null): static
    {
        $items = $this->items;

        $callback
        ? uasort($items, $callback)
        : asort($items);

        return new static($items);
    }

    /**
     * Sort the collection of item keys through a user-defined
     * comparison function.
     *
     * @param callable|null $callback
     * @return static
     */
    public function sortByKey(callable $callback = null): static
    {
        $items = $this->items;

        $callback
        ? uksort($items, $callback)
        : ksort($items);

        return new static($items);
    }

    /**
     * Reverse the collection items.
     *
     * @return static
     */
    public function reverse(): static
    {
        return new static(array_reverse($this->items, true));
    }

    /**
     * Search the collection for a given value and return the corresponding key if successful.
     *
     * @param mixed $value
     * @param bool  $strict
     * @return string|int|bool
     */
    public function search(mixed $value, bool $strict = false): string|int|bool
    {
        if (! is_callable($value)) {
            return array_search($value, $this->items, $strict);
        }

        foreach ($this->items as $key => $item) {
            if ($value($item, $key)) {
                return $key;
            }
        }

        return false;
    }

    /**
     * Group an associative array by a field or using a callback.
     *
     * @param callable $callback
     * @return static
     */
    public function groupBy(callable $callback): static
    {
        $results = [];

        foreach ($this->items as $key => $value) {
            $groupKey = $callback($value, $key, $this->items);

            if (! isset($results[$groupKey])) {
                $results[$groupKey] = [];
            }

            $results[$groupKey][$key] = $value;
        }

        return new static($results);
    }

    /**
     * Returns a new Collection instance containing an
     * indexed array of keys.
     *
     * @return self
     */
    public function keys(): static
    {
        return new static(array_keys($this->items));
    }

    /**
     * Count number of items in collection.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->all());
    }

    /**
     * Returns all items in collection.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * Get and remove the last item from the collection.
     *
     * @return mixed|null
     */
    public function pop(): mixed
    {
        return array_pop($this->items);
    }

    /**
     * Get the last item from the collection.
     *
     * @return mixed|null
     */
    public function last(): mixed
    {
        return count($this->items) > 0 ? end($this->items) : null;
    }

    /**
     * Get and remove the first item from the collection.
     *
     * @return mixed|null
     */
    public function shift(): mixed
    {
        return array_shift($this->items);
    }

    /**
     * Get the first item from the collection.
     *
     * @return mixed|null
     */
    public function first(): mixed
    {
        return count($this->items) > 0 ? reset($this->items) : null;
    }

    /**
     * Get the sum of the collection items.
     *
     * @param mixed|null $callback
     * @return int|float
     */
    public function sum(mixed $callback = null): int|float
    {
        if (is_null__($callback)) {
            return array_sum($this->items);
        }

        return array_reduce($this->items, function ($result, $item) use ($callback) {
            if (is_string($callback)) {
                return $result += $item->{$callback}();
            }

            return $result += $callback($item);
        }, 0);
    }

    /**
     * Merge items with current collection.
     *
     * @throws ReflectionException
     * @throws TypeException
     */
    public function merge(array $items): static
    {
        return new static(ArrayHelper::getInstance()->merge($this->items, $items));
    }

    /**
     * Returns the values from the given property or method.
     *
     * @param string $propertyOrMethod The property or method name to filter by.
     * @return array
     * @throws ValueExtractionException
     */
    public function column(string $propertyOrMethod): array
    {
        $temp = [];

        foreach ($this->items as $item) {
            $value = $this->extractValue($item, $propertyOrMethod);

            $temp[] = $value;
        }

        return $temp;
    }

    /**
     * Set the given array value with the provided key or index.
     *
     * @param mixed $value
     * @param mixed|null $key
     */
    private function itemSet(mixed $value, mixed $key = null): void
    {
        if (is_null__($key)) {
            $this->items[] = $value;
        } else {
            $this->items[$key] = $value;
        }
    }

    /**
     * Unset the given key or index from the array.
     *
     * @param mixed $key
     * @return void
     */
    private function itemUnset(mixed $key): void
    {
        unset($this->items[$key]);
    }
}
