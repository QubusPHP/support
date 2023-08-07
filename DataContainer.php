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

use ArrayAccess;
use ArrayIterator;
use Closure;
use Countable;
use IteratorAggregate;
use OutOfBoundsException;
use Qubus\Exception\Data\TypeException;
use ReturnTypeWillChange;
use RuntimeException;

use function array_map;
use function array_unshift;
use function call_user_func_array;
use function count;
use function func_get_args;
use function uniqid;

class DataContainer implements ArrayAccess, IteratorAggregate, Countable
{
    /** @var DataContainer parent container, for inheritance */
    protected DataContainer $parent;

    /** @var bool whether we want to use parent cascading */
    protected bool $parentEnabled = false;

    /** @var array container data */
    protected array $data = [];

    /** @var bool whether the container is read-only */
    protected bool $readOnly = false;

    /** @var bool whether the container data has been modified */
    protected bool $isModified = false;

    /**
     * Constructor
     *
     * @param DataObjectCollection $dataType String or Array data type.
     * @param array                $data     Container data.
     * @param bool                 $readOnly Whether the container is read-only.
     */
    public function __construct(
        public readonly DataObjectCollection $dataType,
        array $data = [],
        bool $readOnly = false
    ) {
        $this->data = $data;
        $this->readOnly = $readOnly;
    }

    /**
     * Get the parent of this container.
     */
    public function getParent(): DataContainer
    {
        return $this->parent;
    }

    /**
     * Set the parent of this container, to support inheritance.
     *
     * @param DataContainer|null $parent the parent container object
     * @return  $this
     */
    public function setParent(?DataContainer $parent = null): static
    {
        $this->parent = $parent;

        if ($this->parent) {
            $this->enableParent();
        } else {
            $this->disableParent();
        }

        return $this;
    }

    /**
     * Enable the use of the parent object, if set.
     *
     * @return $this
     */
    public function enableParent(): static
    {
        if ($this->parent) {
            $this->parentEnabled = true;
        }

        return $this;
    }

    /**
     * Disable the use of the parent object.
     *
     * @return $this
     */
    public function disableParent(): static
    {
        $this->parentEnabled = false;

        return $this;
    }

    /**
     * Check whether this container has an active parent.
     */
    public function hasParent(): bool
    {
        return $this->parentEnabled;
    }

    /**
     * Retrieve the modified state of the container.
     */
    public function isModified(): bool
    {
        return $this->isModified;
    }

    /**
     * Replace the container's data.
     *
     * @param   array  $data  new data
     * @return $this
     * @throws RuntimeException
     */
    public function setContents(array $data): static
    {
        if ($this->readOnly) {
            throw new RuntimeException('Changing values on this Data Container is not allowed.');
        }

        $this->data = $data;

        $this->isModified = true;

        return $this;
    }

    /**
     * Get the container's data.
     *
     * @return array container's data
     * @throws TypeException
     */
    public function getContents(): array
    {
        if ($this->parentEnabled) {
            return $this->dataType->array->merge($this->parent->getContents(), $this->data);
        } else {
            return $this->data;
        }
    }

    /**
     * Set whether the container is read-only.
     *
     * @param bool $readOnly whether it's a read-only container
     * @return  $this
     */
    public function setReadOnly(bool $readOnly = true): static
    {
        $this->readOnly = (bool) $readOnly;

        return $this;
    }

    /**
     * Merge arrays into the container.
     *
     * @param array $arg  array to merge with
     * @return  $this
     * @throws  RuntimeException|TypeException
     */
    public function merge(array $arg): static
    {
        if ($this->readOnly) {
            throw new RuntimeException('Changing values on this Data Container is not allowed.');
        }

        $arguments = array_map(function ($array) {
            if ($array instanceof DataContainer) {
                return $array->getContents();
            }

            return $array;
        }, func_get_args());

        array_unshift($arguments, $this->data);
        $this->data = call_user_func_array([$this->dataType->array, 'merge'], $arguments);

        $this->isModified = true;

        return $this;
    }

    /**
     * Check whether the container is read-only.
     *
     * @return bool $readOnly  whether it's a read-only container
     */
    public function isReadOnly(): bool
    {
        return $this->readOnly;
    }

    /**
     * isset magic method
     */
    public function __isset($key)
    {
        return $this->has($key);
    }

    /**
     * Check if a key was set upon this bag's data
     * @throws TypeException
     */
    public function has(string $key): bool
    {
        $result = $this->dataType->array->keyExists($this->data, $key);

        if (! $result && $this->parentEnabled) {
            $result = $this->parent->has($key);
        }

        return $result;
    }

    /**
     * get magic method
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * Get a key's value from the container.
     * @throws TypeException
     */
    public function get(?string $key = null, mixed $default = null): mixed
    {
        $fail = uniqid('__FAIL__', true);

        $result = $this->dataType->array->get($this->data, $key, $fail);

        if ($result === $fail) {
            if ($this->parentEnabled) {
                $result = $this->parent->get($key, $default);
            } else {
                $result = $this->result($default);
            }
        }

        return $result;
    }

    /**
     * set magic method
     */
    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Set a config value.
     *
     * @throws RuntimeException
     */
    public function set(?string $key, mixed $value): static
    {
        if ($this->readOnly) {
            throw new RuntimeException('Changing values on this Data Container is not allowed.');
        }

        $this->isModified = true;

        if ($key === null) {
            $this->data[] = $value;

            return $this;
        }

        $this->dataType->array->set($this->data, $key, $value);

        return $this;
    }

    /**
     * Delete data from the container.
     *
     * @param string   $key  key to delete.
     * @return bool  delete success bool
     */
    public function delete(string $key): bool
    {
        if ($this->readOnly) {
            throw new RuntimeException('Changing values on this Data Container is not allowed.');
        }

        $this->isModified = true;

        if (($result = $this->dataType->array->delete($this->data, $key)) === false && $this->parentEnabled) {
            $result = $this->parent->delete($key);
        }

        return $result;
    }

    /**
     * Allow usage of isset() on the param bag as an array.
     *
     * @param string $offset
     * @throws TypeException
     */
    public function offsetExists($offset): bool
    {
        return $this->has($offset);
    }

    /**
     * Allow fetching values as an array.
     *
     * @param string $offset
     * @throws OutOfBoundsException
     * @throws TypeException
     */
    #[ReturnTypeWillChange]
    public function offsetGet($offset): mixed
    {
        return $this->get($offset, function () use ($offset) {
            throw new OutOfBoundsException('Access to undefined index: ' . $offset);
        });
    }

    /**
     * Disallow setting values like an array.
     *
     * @param string $offset
     */
    public function offsetSet($offset, mixed $value): void
    {
        $this->set($offset, $value);
    }

    /**
     * Disallow unsetting values like an array.
     *
     * @param string $offset
     * @throws RuntimeException
     */
    public function offsetUnset($offset): void
    {
        $this->delete($offset);
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return  ArrayIterator  iterator
     * @throws TypeException
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->getContents());
    }

    /**
     * Countable implementation.
     *
     * @return  int  number of items stored in the container
     * @throws TypeException
     */
    public function count(): int
    {
        return count($this->getContents());
    }

    /**
     * Checks if a return value is a Closure without params, and if
     * so executes it before returning it.
     *
     * @return mixed  closure result
     */
    public function result(mixed $val): mixed
    {
        if ($val instanceof Closure) {
            return $val();
        }

        return $val;
    }
}
