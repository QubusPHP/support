<?php

/**
 * Qubus\Support
 *
 * @link       https://github.com/QubusPHP/support
 * @copyright  2022
 * @author     Joshua Parker <joshua@joshuaparker.dev>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 * @since      2.2.2
 */

declare(strict_types=1);

namespace Qubus\Support;

use ReflectionException;

class DataType implements DataObjectCollection
{
    /**
     * list of instances
     *
     * @var array $instances
     */
    protected array $instances = [];

    /**
     * @throws ReflectionException
     * @property ArrayHelper $array
     * @property StringHelper $string
     */
    public function __construct()
    {
        $this->add('array', ArrayHelper::getInstance());
        $this->add('string', StringHelper::getInstance());
    }

    /**
     * @param string $key
     * @param object $value
     */
    public function add(string $key, object $value): void
    {
        $this->instances[$key] = $value;
    }

    /**
     * @param string $key
     * @return object|null
     */
    public function get(string $key): ?object
    {
        if (isset($this->instances[$key])) {
            return $this->instances[$key];
        }

        return null;
    }

    /**
     * @return object|null
     */
    public function __get(string $key)
    {
        return $this->get($key);
    }
}
