<?php

/**
 * Qubus\Support
 *
 * @link       https://github.com/QubusPHP/support
 * @copyright  2020 Joshua Parker <josh@joshuaparker.blog>
 * @copyright  2015 Nil Portugués Calderó
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 *
 * @since      1.0.0
 */

declare(strict_types=1);

namespace Qubus\Support\Serializer;

use ReflectionClass;

class DeepCopySerializer extends Serializer
{
    /**
     * Extract the data from an object.
     *
     * @param mixed $value
     * @return array
     */
    protected function serializeObject($value)
    {
        if ($this->storage->contains($value)) {
            return $this->storage[$value];
        }

        $reflection = new ReflectionClass($value);
        $className = $reflection->getName();

        $serialized = $this->serializeInternalClass($value, $className, $reflection);
        $this->storage->attach($value, $serialized);

        return $serialized;
    }
}
