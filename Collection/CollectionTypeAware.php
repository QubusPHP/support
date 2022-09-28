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

/**
 * Borrowed from ramsey/collection.
 */
trait CollectionTypeAware
{
    /**
     * Returns true if value is of specified type.
     *
     * @param string $type
     * @param mixed $value
     * @return bool
     */
    protected function checkType(string $type, mixed $value): bool
    {
        return match ($type) {
            'array' => is_array($value),
            'bool', 'boolean' => is_bool($value),
            'callable' => is_callable($value),
            'float', 'double' => is_float($value),
            'int', 'integer' => is_int($value),
            'null' => $value === null,
            'numeric' => is_numeric($value),
            'object' => is_object($value),
            'resource' => is_resource($value),
            'scalar' => is_scalar($value),
            'string' => is_string($value),
            'mixed' => true,
            default => $value instanceof $type,
        };
    }
}
