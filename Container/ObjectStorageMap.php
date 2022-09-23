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

namespace Qubus\Support\Container;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Psr\Container\ContainerInterface;
use Qubus\Exception\Data\TypeException;
use Qubus\Exception\Exception;
use RuntimeException;
use SplObjectStorage;

use function array_keys;
use function count;
use function is_object;
use function method_exists;
use function sprintf;

class ObjectStorageMap implements ContainerInterface, ArrayAccess, Countable, IteratorAggregate
{
    private array $items = [];
    private SplObjectStorage $factories;
    private SplObjectStorage $protected;
    private array $frozen = [];
    private array $raw = [];
    private array $keys = [];

    /**
     * @param array $items Pre-populate set with this key-value array
     */
    public function __construct(array $items = [])
    {
        $this->factories = new SplObjectStorage();
        $this->protected = new SplObjectStorage();

        $this->replace($items);
    }

    /**
     * Set data key to value.
     *
     * @param string $key   The data key
     * @param mixed  $value The data value
     */
    public function set(string $key, mixed $value): void
    {
        $this->offsetSet($key, $value);
    }

    /**
     * Get data value with key.
     *
     * @param string $key The data key
     * @return mixed The data value.
     * @throws Exception
     */
    public function get(string $key): mixed
    {
        return $this->offsetGet($key);
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
        return $this->items;
    }

    /**
     * Fetch set data keys.
     *
     * @return array This set's key-value data array keys
     */
    public function keys(): array
    {
        return array_keys($this->items);
    }

    /**
     * Does this set contain a key?
     *
     * @param string $key The data key
     */
    public function has(string $key): bool
    {
        return isset($this->keys[$key]);
    }

    /**
     * Remove value with key from this set.
     *
     * @param  string $key The data key
     */
    public function remove(string $key): void
    {
        if (isset($this->keys[$key])) {
            if (is_object($this->items[$key])) {
                unset($this->factories[$this->items[$key]], $this->protected[$this->items[$key]]);
            }

            unset($this->items[$key], $this->frozen[$key], $this->raw[$key], $this->keys[$key]);
        }
    }

    /**
     * @param string $key The data key.
     * @throws Exception
     */
    public function __get(string $key): mixed
    {
        return $this->get($key);
    }

    /**
     * @param string $key   The data key
     * @param mixed  $value The data value
     */
    public function __set(string $key, mixed $value)
    {
        $this->set($key, $value);
    }

    /**
     * @param string $key The data key
     */
    public function __isset(string $key): bool
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
     * Clear all items.
     *
     * @return void.
     */
    public function clear(): void
    {
        $this->items = [];
    }

    /**
     * Array Access
     */
    public function offsetExists(mixed $offset): bool
    {
        return $this->has($offset);
    }

    /**
     * @throws TypeException
     */
    public function offsetGet(mixed $key): mixed
    {
        if (empty($key)) {
            throw new TypeException(message: 'The key must be a non-empty string.');
        }

        if (
            isset($this->raw[$key])
            || ! is_object(value: $this->items[$key])
            || isset($this->protected[$this->items[$key]])
            || ! method_exists(object_or_class: $this->items[$key], method: '__invoke')
        ) {
            return $this->items[$key];
        }

        if (isset($this->factories[$this->items[$key]])) {
            return $this->items[$key]($this);
        }

        $raw = $this->items[$key];
        $value = $this->items[$key] = $raw($this);
        $this->raw[$key] = $raw;

        $this->frozen[$key] = true;

        return $value;
    }

    public function offsetSet(mixed $key, mixed $value): void
    {
        if (isset($this->frozen[$key])) {
            throw new RuntimeException(message: sprintf('Cannot override frozen service "%s".', $key));
        }

        $this->items[$key] = $value;
        $this->keys[$key] = true;
    }

    public function offsetUnset(mixed $key): void
    {
        $this->remove(key: $key);
    }

    /**
     * Countable
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * IteratorAggregate
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    /**
     * Ensure a value or object will remain globally unique.
     *
     * @param  string   $key   The value or object name
     * @param  callable $value The closure that defines the object
     */
    public function singleton(string $key, callable $value): void
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
     * Marks a callable as being a factory service.
     *
     * @throws TypeException
     */
    public function factory(callable $callable): callable
    {
        if (! is_object(value: $callable) || ! method_exists(object_or_class: $callable, method: '__invoke')) {
            throw new TypeException(message: 'Service definition is not a Closure or invokable object.');
        }

        $this->factories->attach(object: (object) $callable);

        return $callable;
    }

    /**
     * Protects a callable from being interpreted as a service.
     *
     * @param callable $callable A closure to keep from being invoked and evaluated.
     * @throws TypeException
     */
    public function protect(callable $callable): callable
    {
        if (! is_object(value: $callable) || ! method_exists(object_or_class: $callable, method: '__invoke')) {
            throw new TypeException(message: 'Callable is not a Closure or invokable object.');
        }

        $this->protected->attach(object: (object) $callable);

        return $callable;
    }

    /**
     * Gets a parameter or the closure defining an object.
     *
     * @throws TypeException
     */
    public function raw(string $key): mixed
    {
        if (! isset($this->keys[$key])) {
            throw new TypeException(message: sprintf('Identifier "%s" is not defined.', $key));
        }

        if (isset($this->raw[$key])) {
            return $this->raw[$key];
        }

        return $this->items[$key];
    }

    /**
     * Extends an object definition.
     *
     * Useful when you want to extend an existing object definition,
     * without necessarily loading that object.
     *
     * @throws TypeException
     */
    public function extend(string $key, callable $callable): callable
    {
        if (! isset($this->keys[$key])) {
            throw new TypeException(message: sprintf('Identifier "%s" is not defined.', $key));
        }

        if (
            ! is_object(value: $this->items[$key])
            || ! method_exists(object_or_class: $this->items[$key], method: '__invoke')
        ) {
            throw new TypeException(
                message: sprintf('Identifier "%s" does not contain an object definition.', $key)
            );
        }

        if (! is_object(value: $callable) || ! method_exists(object_or_class: $callable, method: '__invoke')) {
            throw new TypeException(message: 'Extension service definition is not a Closure or invokable object.');
        }

        $factory = $this->items[$key];

        $extended = fn($c) => $callable($factory($c), $c);

        if (isset($this->factories[$factory])) {
            $this->factories->detach(object: $factory);
            $this->factories->attach(object: $extended);
        }

        return $this[$key] = $extended;
    }

    /**
     * Registers a service provider.
     *
     * @param ServiceProvider $provider A ServiceProvider instance.
     * @param array $values   An array of values that customizes the provider
     *
     * @return static
     */
    public function register(ServiceProvider $provider, array $values = []): self
    {
        $provider->register($this);

        foreach ($values as $key => $value) {
            $this[$key] = $value;
        }

        return $this;
    }
}
