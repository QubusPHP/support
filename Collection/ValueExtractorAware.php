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

namespace Qubus\Support\Collection;

/**
 * Borrowed from ramsey/collection.
 *
 * Provides functionality to extract the value of a property or method from an object.
 */
trait ValueExtractorAware
{
    /**
     * Extracts the value of the given property or method from the object.
     *
     * @param mixed $object The object to extract the value from.
     * @param string|null $propertyOrMethod The property or method for which the
     *                                      value should be extracted.
     * @return mixed the value extracted from the specified property or method.
     * @throws ValueExtractionException if the method or property is not defined.
     */
    protected function extractValue(mixed $object, ?string $propertyOrMethod = null): mixed
    {
        if ($propertyOrMethod === null) {
            return $object;
        }

        if (!is_object(value: $object) && !is_array($object)) {
            throw new ValueExtractionException(message: 'Unable to extract a value from a non-object.');
        }

        if (is_array($object)) {
            return $object[$propertyOrMethod] ?? throw new InvalidPropertyOrMethodException(
                sprintf(
                    'Key or index "%s" not found in collection elements',
                    $propertyOrMethod,
                )
            );
        }

        if (property_exists($object, $propertyOrMethod)) {
            return $object->{$propertyOrMethod};
        }

        if (method_exists($object, $propertyOrMethod)) {
            return $object->{$propertyOrMethod}();
        }

        throw new ValueExtractionException(
            message: sprintf(
                'Method or property "%s" not defined in %s.',
                $propertyOrMethod,
                get_class(object: $object)
            )
        );
    }
}
