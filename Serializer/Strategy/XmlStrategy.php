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

namespace Qubus\Support\Serializer\Strategy;

use Qubus\Support\Serializer\Serializer;
use SimpleXMLElement;

use function array_flip;
use function array_keys;
use function array_values;
use function explode;
use function gettype;
use function is_array;
use function is_int;
use function is_numeric;
use function is_string;
use function simplexml_load_string;
use function str_replace;
use function strpos;

class XmlStrategy implements Strategy
{
    /** @var array */
    private array $replacements = [
        Serializer::CLASS_IDENTIFIER_KEY => 'serializer_type',
        Serializer::SCALAR_TYPE          => 'serializer_scalar',
        Serializer::SCALAR_VALUE         => 'serializer_value',
        Serializer::MAP_TYPE             => 'serializer_map',
    ];

    /**
     * @param mixed $value
     * @return bool|string
     */
    public function serialize(mixed $value): bool|string
    {
        $value = $this->replaceKeys($this->replacements, $value);
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><data></data>');
        $this->arrayToXml($value, $xml);

        return $xml->asXML();
    }

    /**
     * @param array $replacements
     * @param array $input
     * @return array
     */
    private function replaceKeys(array $replacements, array $input): array
    {
        $return = [];
        foreach ($input as $key => $value) {
            if (is_string($key) || is_int($key)) {
                $key = (string) $key;
            } else {
                $key = (array) $key;
            }
            $key = str_replace(array_keys($replacements), array_values($replacements), $key);

            if (is_array($value)) {
                $value = $this->replaceKeys($replacements, $value);
            }

            $return[$key] = $value;
        }

        return $return;
    }

    /**
     * Converts an array to XML using SimpleXMLElement.
     *
     * @param array $data
     * @param SimpleXMLElement $xmlData
     */
    private function arrayToXml(array &$data, SimpleXMLElement $xmlData): void
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if (is_numeric($key)) {
                    $key = 'serializer_element_' . gettype($key) . '_' . $key;
                }
                $subnode = $xmlData->addChild($key);
                $this->arrayToXml($value, $subnode);
            } else {
                $xmlData->addChild("$key", "$value");
            }
        }
    }

    /**
     * @param mixed $value
     * @return bool|string|array|object
     */
    public function unserialize(mixed $value): bool|string|array|object
    {
        $array = (array) simplexml_load_string($value);
        $this->castToArray($array);
        $this->recoverArrayNumericKeyValues($array);
        $replacements = array_flip($this->replacements);
        return $this->replaceKeys($replacements, $array);
    }

    /**
     * @param array $array
     */
    private function castToArray(array &$array): void
    {
        foreach ($array as &$value) {
            if ($value instanceof SimpleXMLElement) {
                $value = (array) $value;
            }

            if (is_array($value)) {
                $this->castToArray($value);
            }
        }
    }

    /**
     * @param array $array
     */
    private function recoverArrayNumericKeyValues(array &$array): void
    {
        $newArray = [];
        foreach ($array as $key => &$value) {
            if (false !== strpos($key, 'serializer_element_')) {
                $key = $this->getNumericKeyValue($key);
            }

            $newArray[$key] = $value;

            if (is_array($newArray[$key])) {
                $this->recoverArrayNumericKeyValues($newArray[$key]);
            }
        }
        $array = $newArray;
    }

    /**
     * @param mixed $key
     * @return float|int
     */
    private static function getNumericKeyValue(mixed $key): float|int
    {
        $newKey = str_replace('serializer_element_', '', $key);
        [$type, $index] = explode('_', $newKey);

        if ('integer' === $type || 'int' === $type) {
            $index = (int) $index;
        }

        return $index;
    }
}
