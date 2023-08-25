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

namespace Qubus\Support\Serializer\Transformer;

use Qubus\Exception\Data\TypeException;
use Qubus\Support\Serializer\Serializer;
use Qubus\Support\Serializer\Strategy\Strategy;

use function array_key_exists;
use function array_pop;
use function count;
use function end;
use function is_array;
use function is_scalar;
use function sprintf;

abstract class BaseTransformer implements Strategy
{
    /**
     * @param array $array
     * @param array $unwantedKey
     */
    protected function recursiveUnset(array &$array, array $unwantedKey): void
    {
        foreach ($unwantedKey as $key) {
            if (array_key_exists($key, $array)) {
                unset($array[$key]);
            }
        }

        foreach ($array as &$value) {
            if (is_array($value)) {
                $this->recursiveUnset($value, $unwantedKey);
            }
        }
    }

    /**
     * @param array $array
     */
    protected function recursiveSetValues(array &$array): void
    {
        if (array_key_exists(Serializer::SCALAR_VALUE, $array)) {
            $array = $array[Serializer::SCALAR_VALUE];
        }

        if (is_array($array) && ! array_key_exists(Serializer::SCALAR_VALUE, $array)) {
            foreach ($array as &$value) {
                if (is_array($value)) {
                    $this->recursiveSetValues($value);
                }
            }
        }
    }

    /**
     * @param array $array
     * @param null $parentKey
     * @param null $currentKey
     */
    protected function recursiveFlattenOneElementObjectsToScalarType(
        array &$array,
        $parentKey = null,
        $currentKey = null
    ): void {
        if (1 === count($array) && is_scalar(end($array))) {
            if ($parentKey === $currentKey) {
                $array = array_pop($array);
            }
        }

        if (is_array($array)) {
            foreach ($array as $parentKey => &$value) {
                if (is_array($value)) {
                    $key = null;
                    foreach ($value as $key => $v) {
                        if (is_array($v)) {
                            $this->recursiveFlattenOneElementObjectsToScalarType($v, $parentKey, $key);
                        }
                    }
                    $this->recursiveFlattenOneElementObjectsToScalarType($value, $parentKey, $key);
                }
            }
        }
    }

    /**
     * @param mixed $value
     * @return array
     * @throws TypeException
     */
    public function unserialize(mixed $value): array
    {
        throw new TypeException(sprintf('%s does not perform unserializations.', self::class));
    }
}
