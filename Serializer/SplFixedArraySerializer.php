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
use SplFixedArray;

use function count;

class SplFixedArraySerializer
{
    /**
     * @param Serializer $serializer
     * @param SplFixedArray $splFixedArray
     * @return array
     * @throws ReflectionException
     */
    public static function serialize(Serializer $serializer, SplFixedArray $splFixedArray): array
    {
        $toArray = [
            Serializer::CLASS_IDENTIFIER_KEY => $splFixedArray::class,
            Serializer::CLASS_PARENT_KEY     => 'SplFixedArray',
            Serializer::SCALAR_VALUE         => [],
        ];
        foreach ($splFixedArray->toArray() as $key => $field) {
            $toArray[Serializer::SCALAR_VALUE][$key] = $serializer->serialize($field);
        }

        return $toArray;
    }

    /**
     * @param Serializer $serializer
     * @param string $className
     * @param array $value
     * @return mixed
     * @throws ReflectionException
     */
    public static function unserialize(Serializer $serializer, string $className, array $value): mixed
    {
        $data = $serializer->unserialize($value[Serializer::SCALAR_VALUE]);

        /** @var SplFixedArray $instance */
        $ref = new ReflectionClass($className);
        $instance = $ref->newInstanceWithoutConstructor();

        $instance->setSize(count($data));
        foreach ($data as $k => $v) {
            $instance[$k] = $v;
        }

        return $instance;
    }
}
