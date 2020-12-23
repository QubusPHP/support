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

namespace Qubus\Support\Serializer;

use Closure;
use DatePeriod;
use Qubus\Support\Serializer\Strategy\Strategy;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use SplObjectStorage;

use function array_key_exists;
use function array_keys;
use function array_map;
use function array_merge;
use function array_unique;
use function class_exists;
use function floatval;
use function get_class;
use function get_object_vars;
use function gettype;
use function intval;
use function is_array;
use function is_object;
use function is_resource;
use function is_string;
use function is_subclass_of;
use function method_exists;
use function preg_match;
use function preg_replace;
use function serialize;
use function strcmp;
use function strlen;
use function strpos;
use function strtolower;
use function substr;
use function trim;
use function unserialize;

class Serializer implements Serializeable
{
    public const CLASS_IDENTIFIER_KEY = '@type';
    public const CLASS_PARENT_KEY = '@parent';
    public const SCALAR_TYPE = '@scalar';
    public const SCALAR_VALUE = '@value';
    public const NULL_VAR = null;
    public const MAP_TYPE = '@map';

    /**
     * Storage for object.
     *
     * Used for recursion
     */
    protected SplObjectStorage $storage;

    /**
     * Object mapping for recursion.
     *
     * @var array
     */
    protected array $mapping = [];

    /**
     * Object mapping index.
     */
    protected int $mappingIndex = 0;

    protected Strategy $strategy;

    /** @var array */
    private $dateTimeClassType = ['DateTime', 'DateTimeImmutable', 'DateTimeZone', 'DateInterval', 'DatePeriod'];

    /** @var array */
    protected $serializationMap = [
        'array'   => 'serializeArray',
        'integer' => 'serializeScalar',
        'double'  => 'serializeScalar',
        'boolean' => 'serializeScalar',
        'string'  => 'serializeScalar',
    ];

    public function __construct(Strategy $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * This is handly specially in order to add additional data before the
     * serialization process takes place using the transformer public methods, if any.
     *
     * @return Strategy
     */
    public function getTransformer()
    {
        return $this->strategy;
    }

    /**
     * Serialize the data.
     *
     * @param mixed $data
     * @return string Seralized data.
     * @throws SerializerException
     */
    public function serialize($data)
    {
        $this->reset();

        return $this->strategy->serialize($this->serializeData($data));
    }

    /**
     * Reset variables.
     */
    protected function reset()
    {
        $this->storage = new SplObjectStorage();
        $this->mapping = [];
        $this->mappingIndex = 0;
    }

    /**
     * Parse the data to be serialized.
     *
     * @param mixed $data
     * @return mixed
     * @throws SerializerException
     */
    protected function serializeData($data)
    {
        $this->guardForUnsupportedValues($data);

        if ($this->isInstanceOf($data, 'SplFixedArray')) {
            return SplFixedArraySerializer::serialize($this, $data);
        }

        if (is_object($data)) {
            return $this->serializeObject($data);
        }

        $type = gettype($data) && $data !== null ? gettype($data) : 'string';
        $func = $this->serializationMap[$type];

        return $this->$func($data);
    }

    /**
     * Check if a class is instance or extends from the expected instance.
     *
     * @param mixed  $value
     * @param string $classFqn
     * @return bool
     */
    private function isInstanceOf($value, $classFqn)
    {
        return is_object($value)
        && (strtolower(get_class($value)) === strtolower($classFqn) || is_subclass_of($value, $classFqn, true));
    }

    /**
     * @param mixed $data
     * @throws SerializerException
     */
    protected function guardForUnsupportedValues($data)
    {
        if ($data instanceof Closure) {
            throw new SerializerException('Closures are not supported in Serializer.');
        }

        if ($data instanceof DatePeriod) {
            throw new SerializerException(
                'DatePeriod is not supported in Serializer. Loop through it and serialize the output.'
            );
        }

        if (is_resource($data)) {
            throw new SerializerException('Resource is not supported in Serializer.');
        }
    }

    /**
     * Unserialize the value from string.
     *
     * @param mixed $data
     * @return mixed
     */
    public function unserialize($data)
    {
        if (is_array($data) && isset($data[self::SCALAR_TYPE])) {
            return $this->unserializeData($data);
        }

        $this->reset();

        return $this->unserializeData($this->strategy->unserialize($data));
    }

    /**
     * Parse the json decode to convert to objects again.
     *
     * @param mixed $data
     * @return mixed
     */
    protected function unserializeData($data)
    {
        if (null === $data || ! is_array($data)) {
            return $data;
        }

        if (isset($data[self::MAP_TYPE]) && ! isset($data[self::CLASS_IDENTIFIER_KEY])) {
            $data = $data[self::SCALAR_VALUE];

            return $this->unserializeData($data);
        }

        if (isset($data[self::SCALAR_TYPE])) {
            return $this->getScalarValue($data);
        }

        if (isset($data[self::CLASS_PARENT_KEY]) && 0 === strcmp($data[self::CLASS_PARENT_KEY], 'SplFixedArray')) {
            return SplFixedArraySerializer::unserialize($this, $data[self::CLASS_IDENTIFIER_KEY], $data);
        }

        if (isset($data[self::CLASS_IDENTIFIER_KEY])) {
            return $this->unserializeObject($data);
        }

        return array_map([$this, __METHOD__], $data);
    }

    /**
     * @param mixed $value
     * @return float|int|null|bool
     */
    protected function getScalarValue($value)
    {
        switch ($value[self::SCALAR_TYPE]) {
            case 'integer':
                return intval($value[self::SCALAR_VALUE]);
            case 'float':
                return floatval($value[self::SCALAR_VALUE]);
            case 'boolean':
                return $value[self::SCALAR_VALUE];
            case 'NULL':
                return self::NULL_VAR;
        }

        return $value[self::SCALAR_VALUE];
    }

    /**
     * Convert the serialized array into an object.
     *
     * @param array $data
     * @return object
     * @throws SerializerException
     */
    protected function unserializeObject(array $data)
    {
        $className = $data[self::CLASS_IDENTIFIER_KEY];
        unset($data[self::CLASS_IDENTIFIER_KEY]);

        if (isset($data[self::MAP_TYPE])) {
            unset($data[self::MAP_TYPE]);
            unset($data[self::SCALAR_VALUE]);
        }

        if ($className[0] === '@') {
            return $this->mapping[substr($className, 1)];
        }

        if (! class_exists($className)) {
            throw new SerializerException('Unable to find class ' . $className);
        }

        return null === ($obj = $this->unserializeDateTimeFamilyObject($data, $className))
        ? $this->unserializeUserDefinedObject($data, $className) : $obj;
    }

    /**
     * @param array  $data
     * @param string $className
     * @return mixed
     */
    protected function unserializeDateTimeFamilyObject(array $data, $className)
    {
        $obj = null;

        if ($this->isDateTimeFamilyObject($className)) {
            $obj = $this->restoreUsingUnserialize($className, $data);
            $this->mapping[$this->mappingIndex++] = $obj;
        }

        return $obj;
    }

    /**
     * @param string $className
     * @return bool
     */
    protected function isDateTimeFamilyObject($className)
    {
        $isDateTime = false;

        foreach ($this->dateTimeClassType as $class) {
            $isDateTime = $isDateTime || is_subclass_of($className, $class, true) || $class === $className;
        }

        return $isDateTime;
    }

    /**
     * @param string $className
     * @param array  $attributes
     * @return mixed
     */
    protected function restoreUsingUnserialize($className, array $attributes)
    {
        foreach ($attributes as &$attribute) {
            $attribute = $this->unserializeData($attribute);
        }

        $obj = (object) $attributes;
        $serialized = preg_replace(
            '|^O:\d+:"\w+":|',
            'O:' . strlen($className) . ':"' . $className . '":',
            serialize($obj)
        );

        return unserialize($serialized);
    }

    /**
     * @param array  $data
     * @param string $className
     * @return object
     */
    protected function unserializeUserDefinedObject(array $data, $className)
    {
        $ref = new ReflectionClass($className);
        $obj = $ref->newInstanceWithoutConstructor();

        $this->mapping[$this->mappingIndex++] = $obj;
        $this->setUnserializedObjectProperties($data, $ref, $obj);

        if (method_exists($obj, '__wakeup')) {
            $obj->__wakeup();
        }

        return $obj;
    }

    /**
     * @param array           $data
     * @param mixed           $obj
     * @return mixed
     */
    protected function setUnserializedObjectProperties(array $data, ReflectionClass $ref, $obj)
    {
        foreach ($data as $property => $propertyValue) {
            try {
                $propRef = $ref->getProperty($property);
                $propRef->setAccessible(true);
                $propRef->setValue($obj, $this->unserializeData($propertyValue));
            } catch (ReflectionException $e) {
                $obj->$property = $this->unserializeData($propertyValue);
            }
        }

        return $obj;
    }

    /**
     * @param $data
     * @return string
     */
    protected function serializeScalar($data)
    {
        $type = gettype($data);
        if ($type === 'double') {
            $type = 'float';
        }

        return [
            self::SCALAR_TYPE  => $type,
            self::SCALAR_VALUE => $data,
        ];
    }

    /**
     * @param array $data
     * @return array
     */
    protected function serializeArray(array $data)
    {
        if (array_key_exists(self::MAP_TYPE, $data)) {
            return $data;
        }

        $toArray = [self::MAP_TYPE => 'array', self::SCALAR_VALUE => []];
        foreach ($data as $key => $field) {
            $toArray[self::SCALAR_VALUE][$key] = $this->serializeData($field);
        }

        return $this->serializeData($toArray);
    }

    /**
     * Extract the data from an object.
     *
     * @param mixed $data
     * @return array
     */
    protected function serializeObject($data)
    {
        if ($this->storage->contains($data)) {
            return [self::CLASS_IDENTIFIER_KEY => '@' . $this->storage[$data]];
        }

        $this->storage->attach($data, $this->mappingIndex++);

        $reflection = new ReflectionClass($data);
        $className = $reflection->getName();

        return $this->serializeInternalClass($data, $className, $reflection);
    }

    /**
     * @param mixed           $value
     * @param string          $className
     * @return array
     */
    protected function serializeInternalClass($value, $className, ReflectionClass $ref)
    {
        $paramsToSerialize = $this->getObjectProperties($ref, $value);
        $data = [self::CLASS_IDENTIFIER_KEY => $className];
        $data += array_map([$this, 'serializeData'], $this->extractObjectData($value, $ref, $paramsToSerialize));

        return $data;
    }

    /**
     * Return the list of properties to be serialized.
     *
     * @param object $data
     * @return array
     */
    protected function getObjectProperties(ReflectionClass $ref, $data)
    {
        $props = [];
        foreach ($ref->getProperties() as $prop) {
            $props[] = $prop->getName();
        }

        return array_unique(array_merge($props, array_keys(get_object_vars($data))));
    }

    /**
     * Extract the object data.
     *
     * @param mixed            $value
     * @param array            $properties
     * @return array
     */
    protected function extractObjectData($value, ReflectionClass $rc, array $properties)
    {
        $data = [];

        $this->extractCurrentObjectProperties($value, $rc, $properties, $data);
        $this->extractAllInhertitedProperties($value, $rc, $data);

        return $data;
    }

    /**
     * @param mixed           $value
     * @param array           $properties
     * @param array           $data
     */
    protected function extractCurrentObjectProperties($value, ReflectionClass $rc, array $properties, array &$data)
    {
        foreach ($properties as $propertyName) {
            try {
                $propRef = $rc->getProperty($propertyName);
                $propRef->setAccessible(true);
                $data[$propertyName] = $propRef->getValue($value);
            } catch (ReflectionException $e) {
                $data[$propertyName] = $value->$propertyName;
            }
        }
    }

    /**
     * @param mixed           $value
     * @param array           $data
     */
    protected function extractAllInhertitedProperties($value, ReflectionClass $rc, array &$data)
    {
        do {
            $rp = [];
            /** @var ReflectionProperty $property */
            foreach ($rc->getProperties() as $property) {
                $property->setAccessible(true);
                $rp[$property->getName()] = $property->getValue($value);
            }
            $data = array_merge($rp, $data);
        } while ($rc = $rc->getParentClass());
    }

    /**
     * Checks if data is serialized.
     *
     * @param string|array|object $data
     */
    private function isSerialized($data, bool $strict = true): bool
    {
        // if it isn't a string, it isn't serialized.
        if (! is_string($data)) {
            return false;
        }
        $data = trim($data);
        if ('N;' === $data) {
            return true;
        }
        if (strlen($data) < 4) {
            return false;
        }
        if (':' !== $data[1]) {
            return false;
        }
        if ($strict) {
            $lastc = substr($data, -1);
            if (';' !== $lastc && '}' !== $lastc) {
                return false;
            }
        } else {
            $semicolon = strpos($data, ';');
            $brace     = strpos($data, '}');
            // Either ; or } must exist.
            if (false === $semicolon && false === $brace) {
                return false;
            }
            // But neither must be in the first X characters.
            if (false !== $semicolon && $semicolon < 3) {
                return false;
            }
            if (false !== $brace && $brace < 4) {
                return false;
            }
        }
        $token = $data[0];
        switch ($token) {
            case 's':
                if ($strict) {
                    if ('"' !== substr($data, -2, 1)) {
                        return false;
                    }
                } elseif (false === strpos($data, '"')) {
                    return false;
                }
            // or else fall through
            // no break
            case 'a':
            case 'O':
                return (bool) preg_match("/^{$token}:[0-9]+:/s", $data);
            case 'b':
            case 'i':
            case 'd':
                $end = $strict ? '$' : '';
                return (bool) preg_match("/^{$token}:[0-9.E-]+;$end/", $data);
        }
        return false;
    }
}
