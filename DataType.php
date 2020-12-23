<?php

/**
 * Qubus\Support
 *
 * @link       https://github.com/QubusPHP/support
 * @copyright  2020 Joshua Parker
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 *
 * @since      1.0.0
 */

declare(strict_types=1);

namespace Qubus\Support;

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
        $this->add('array', ArrayHelper::getInstance());
        $this->add('string', StringHelper::getInstance());
    }

    /**
     * @param object $value
     */
    public function add(string $key, $value): void
    {
        $this->instances[$key] = $value;
    }

    /**
     * @return object
     */
    public function get(string $key)
    {
        if (isset($this->instances[$key])) {
            return $this->instances[$key];
        }

        return null;
    }

    /**
     * @return object
     */
    public function __get(string $key)
    {
        return $this->get($key);
    }
}
