<?php

/**
 * Qubus\Support
 *
 * @link       https://github.com/QubusPHP/support
 * @copyright  2020 Joshua Parker
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 *
 * @since      1.0.0
 */

declare(strict_types=1);

namespace Qubus\Support\Serializer\Strategy;

use SimpleXMLElement;
use Qubus\Support\Serializer\Serializer;

class XmlStrategy implements Strategy
{
    /**
     * @var array
     */
    private $replacements = [
        Serializer::CLASS_IDENTIFIER_KEY => 'serializer_type',
        Serializer::SCALAR_TYPE => 'serializer_scalar',
        Serializer::SCALAR_VALUE => 'serializer_value',
        Serializer::MAP_TYPE => 'serializer_map',
    ];

    /**
     * @param mixed $value
     * @return string
     */
    public function serialize($value)
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
    private function replaceKeys(array &$replacements, array $input)
    {
        $return = [];
        foreach ($input as $key => $value) {
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
     * @param array            $data
     * @param SimpleXMLElement $xmlData
     */
    private function arrayToXml(array &$data, SimpleXMLElement $xmlData)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if (is_numeric($key)) {
                    $key = 'serializer_element_'.gettype($key).'_'.$key;
                }
                $subnode = $xmlData->addChild($key);
                $this->arrayToXml($value, $subnode);
            } else {
                $xmlData->addChild("$key", "$value");
            }
        }
    }

    /**
     * @param $value
     *
     * @return array
     */
    public function unserialize($value)
    {
        $array = (array) simplexml_load_string($value);
        $this->castToArray($array);
        $this->recoverArrayNumericKeyValues($array);
        $replacements = array_flip($this->replacements);
        $array = $this->replaceKeys($replacements, $array);

        return $array;
    }

    /**
     * @param array $array
     */
    private function castToArray(array &$array)
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
    private function recoverArrayNumericKeyValues(array &$array)
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
     * @param $key
     *
     * @return float|int
     */
    private static function getNumericKeyValue($key)
    {
        $newKey = str_replace('serializer_element_', '', $key);
        [$type, $index] = explode('_', $newKey);

        if ('integer' === $type || 'int' === $type) {
            $index = (int) $index;
        }

        return $index;
    }
}
