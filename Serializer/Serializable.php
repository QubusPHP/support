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

interface Serializable
{
    /**
     * Serializes data if necessary.
     *
     * @param string|array|object $data Data to be serialized.
     * @return bool|string Serialized data or original string.
     * @throws SerializerException
     */
    public function serialize(string|array|object $data): bool|string;

    /**
     * Unserializes data if necessary.
     *
     * @since 1.0.0
     * @param string|array|object $data Data that should be unserialzed.
     * @return mixed Unserialized data or original string.
     */
    public function unserialize(string|array|object $data): mixed;
}
