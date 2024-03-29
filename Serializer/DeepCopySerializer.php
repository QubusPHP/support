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

namespace Qubus\Support\Serializer;

use ReflectionClass;
use ReflectionException;

class DeepCopySerializer extends Serializer
{
    /**
     * Extract the data from an object.
     *
     * @param mixed $data
     * @return array
     * @throws ReflectionException
     */
    protected function serializeObject(mixed $data): array
    {
        if ($this->storage->contains($data)) {
            return $this->storage[$data];
        }

        $reflection = new ReflectionClass($data);
        $className = $reflection->getName();

        $serialized = $this->serializeInternalClass($data, $className, $reflection);
        $this->storage->attach($data, $serialized);

        return $serialized;
    }
}
