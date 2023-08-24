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

use ReflectionClass;
use ReflectionException;

final class ClassInfo
{
    /**
     * ReflectionClass object.
     *
     * @throws ReflectionException
     */
    public static function reflect(object|string $objectOrClass): ReflectionClass
    {
        return new ReflectionClass($objectOrClass);
    }

    /**
     * Retrieve the class's name.
     *
     * @throws ReflectionException
     */
    public static function short(object|string $objectOrClass): string
    {
        return self::reflect($objectOrClass)->getShortName();
    }

    /**
     * Retrieve class's name its namespace name (i.e. Qubus\Support\ClassName).
     *
     * @throws ReflectionException
     */
    public static function name(object|string $objectOrClass): string
    {
        return self::reflect($objectOrClass)->getName();
    }

    /**
     * Retrieve the namespace name of a class.
     *
     * @throws ReflectionException
     */
    public static function namespace(object|string $objectOrClass): string
    {
        return self::reflect($objectOrClass)->getNamespaceName();
    }
}
