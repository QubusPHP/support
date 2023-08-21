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

class DataType implements DataObjectCollection
{
    /** @var StringHelper|null */
    public ?StringHelper $string = null;

    /** @var ArrayHelper|null */
    public ?ArrayHelper $array = null;

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
