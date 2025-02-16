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

namespace Qubus\Support;

/**
 * @property ArrayHelper $array
 * @property StringHelper $string
 */
class DataType implements DataObjectCollection
{
    /**
     * list of instances
     *
     * @var array $instances
     */
    protected array $instances = [];

    public function __construct()
    {
        $this->add('array', new ArrayHelper());
        $this->add('string', new StringHelper());
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
