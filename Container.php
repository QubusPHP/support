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
use Countable;
use IteratorAggregate;
use Psr\Container\ContainerInterface;
use Qubus\Exception\Exception;

use function array_key_exists;
use function array_keys;
use function count;
use function is_object;
use function is_string;
use function method_exists;

class Container implements ContainerInterface, ArrayAccess, Countable, IteratorAggregate
{
    /**
     * Key-value array of arbitrary data.
     *
     * @var array
     */
    protected array $data = [];

    /**
     * @param array $items Pre-populate set with this key-value array
     */
    public function __construct(array $items = [])
    {
        $this->replace($items);
    }

    /**
     * Set data key to value.
     *
     * @param string $key   The data key
     * @param mixed  $value The data value
     */
    public function set(string $key, $value): void
    {
        $this->data[$key] = $value;
    }

    /**
     * Get data value with key.
     *
     * @param  string $key The data key
     * @return mixed The data value.
     * @throws Exception
     */
    public function get($key)
    {
        if (! is_string($id) || empty($id)) {
            throw new Exception('The key must be a non-empty string.');
        }

        if ($this->has($key)) {
            $isInvokable = is_object($this->data[$key]) && method_exists($this->data[$key], '__invoke');

            return $isInvokable ? $this->data[$key]($this) : $this->data[$key];
        }

        return null;
    }

    /**
     * Add data to set.
     *
     * @param array $items Key-value array of data to append to this set
     */
    public function replace(array $items): void
    {
        foreach ($items as $key => $value) {
            $this->set($key, $value); // Ensure keys are normalized
        }
    }

    /**
     * Fetch set data.
     *
     * @return array This set's key-value data array
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * Fetch set data keys.
     *
     * @return array This set's key-value data array keys
     */
    public function keys(): array
    {
        return array_keys($this->data);
    }

    /**
     * Does this set contain a key?
     *
     * @param  string  $key The data key
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Remove value with key from this set.
     *
     * @param  string $key The data key
     */
    public function remove(string $key): void
    {
        unset($this->data[$key]);
    }

    /**
     * @param  string $key The data key.
     */
    public function __get(string $key)
    {
        return $this->get($key);
    }

    /**
     * @param string $key   The data key
     * @param mixed  $value The data value
     */
    public function __set(string $key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * @param string $key The data key
     */
    public function __isset(string $key)
    {
        return $this->has($key);
    }

    /**
     * @param string $key The data key
     */
    public function __unset(string $key)
    {
        $this->remove($key);
    }

    /**
     * Clear all values.
     *
     * @return Empty array.
     */
    public function clear(): void
    {
        $this->data = [];
    }

    /**
     * Array Access
     */
    public function offsetExists($offset): bool
    {
        return $this->has($offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value): void
    {
        $this->set($offset, $value);
    }

    public function offsetUnset($offset): void
    {
        $this->remove($offset);
    }

    /**
     * Countable
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * IteratorAggregate
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->data);
    }

    /**
     * Ensure a value or object will remain globally unique.
     *
     * @param  string   $key   The value or object name
     * @param  callable $value The closure that defines the object
     * @return mixed
     */
    public function singleton(string $key, callable $value)
    {
        $this->set($key, function ($c) use ($value) {
            static $object;

            if (null === $object) {
                $object = $value($c);
            }

            return $object;
        });
    }

    /**
     * Protect closure from being directly invoked.
     *
     * @param callable $callable A closure to keep from being invoked and evaluated.
     */
    public function protect(callable $callable): callable
    {
        return fn () => $callable;
    }
}
