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
use Qubus\Support\DataObjectCollection;
use RuntimeException;

use function array_map;
use function array_unshift;
use function call_user_func_array;
use function count;
use function func_get_args;
use function uniqid;

class DataContainer implements ArrayAccess, IteratorAggregate, Countable
{
    protected DataObjectCollection $dataType;
    /** @var DataContainer parent container, for inheritance */
    protected $parent;

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
    public function __construct(DataObjectCollection $dataType, array $data = [], bool $readOnly = false)
    {
        $this->dataType = $dataType;
        $this->data = $data;
        $this->readOnly = $readOnly;
    }

    /**
     * Get the parent of this container
     *
     * @return  DataContainer
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set the parent of this container, to support inheritance
     *
     * @param DataContainer  $parent  the parent container object
     * @return  $this
     */
    public function setParent(?DataContainer $parent = null)
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
     * Enable the use of the parent object, if set
     *
     * @return $this
     */
    public function enableParent()
    {
        if ($this->parent) {
            $this->parentEnabled = true;
        }

        return $this;
    }

    /**
     * Disable the use of the parent object
     *
     * @return $this
     */
    public function disableParent()
    {
        $this->parentEnabled = false;

        return $this;
    }

    /**
     * Check whether or not this container has an active parent
     */
    public function hasParent(): bool
    {
        return $this->parentEnabled;
    }

    /**
     * Retrieve the modified state of the container
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
    public function setContents(array $data)
    {
        if ($this->readOnly) {
            throw new RuntimeException('Changing values on this Data Container is not allowed.');
        }

        $this->data = $data;

        $this->isModified = true;

        return $this;
    }

    /**
     * Get the container's data
     *
     * @return array container's data
     */
    public function getContents()
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
    public function setReadOnly(bool $readOnly = true)
    {
        $this->readOnly = (bool) $readOnly;

        return $this;
    }

    /**
     * Merge arrays into the container.
     *
     * @param   array  $arg  array to merge with
     * @return  $this
     * @throws  RuntimeException
     */
    public function merge($arg)
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
     *
     * @param string $key
     */
    public function has($key): bool
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
     * Get a key's value from this bag's data
     *
     * @param mixed   $default
     * @return mixed
     */
    public function get(?string $key = null, $default = null)
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
     * Set a config value
     *
     * @param string  $key
     * @param mixed   $value
     * @throws RuntimeException
     */
    public function set($key, $value)
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
    public function delete(string $key)
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
     * Allow usage of isset() on the param bag as an array
     *
     * @param string  $key
     */
    public function offsetExists($key): bool
    {
        return $this->has($key);
    }

    /**
     * Allow fetching values as an array
     *
     * @param string $key
     * @return mixed
     * @throws OutOfBoundsException
     */
    public function offsetGet($key)
    {
        return $this->get($key, function () use ($key) {
            throw new OutOfBoundsException('Access to undefined index: ' . $key);
        });
    }

    /**
     * Disallow setting values like an array
     *
     * @param string $key
     * @param mixed $value
     */
    public function offsetSet($key, $value): void
    {
        $this->set($key, $value);
    }

    /**
     * Disallow unsetting values like an array
     *
     * @param string $key
     * @throws RuntimeException
     */
    public function offsetUnset($key): void
    {
        $this->delete($key);
    }

    /**
     * IteratorAggregate implementation
     *
     * @return  ArrayIterator  iterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->getContents());
    }

    /**
     * Countable implementation
     *
     * @return  int  number of items stored in the container
     */
    public function count(): int
    {
        return count($this->getContents());
    }

    /**
     * Checks if a return value is a Closure without params, and if
     * so executes it before returning it.
     *
     * @param mixed  $val
     * @return mixed  closure result
     */
    public function result($val)
    {
        if ($val instanceof Closure) {
            return $val();
        }

        return $val;
    }
}
