<?php

declare(strict_types=1);

namespace Qubus\Tests\Support\Serializer;

use PHPUnit\Framework\TestCase;
use Qubus\Support\Serializer\Serializer;
use Qubus\Support\Serializer\Strategy\JsonStrategy;
use Qubus\Support\Serializer\SerializerException;
use DateTimeImmutable;
use DateTimeZone;
use DateInterval;
use DatePeriod;
use DateTime;
use stdClass;

class SerializerTest extends TestCase
{
    protected $serializer;

    /**
     * Test case setup.
     */
    public function setUp()
    {
        $this->serializer = new Serializer(new JsonStrategy());
    }

    /**
     * Test serialization of DateTime classes.
     *
     * Some interal classes, such as DateTime, cannot be initialized with
     * ReflectionClass::newInstanceWithoutConstructor()
     */
    public function testSerializationOfDateTime()
    {
        $date = (new DateTime('2014-06-15 12:00:00', new DateTimeZone('UTC')))->format('c');

        $obj = $this->serializer->unserialize($this->serializer->serialize($date));
        $this->assertSame($date, $obj);
        $this->assertEquals($date, $obj);
    }

    /**
     * Test serialization of scalar values.
     *
     * @dataProvider scalarDataToJson
     *
     * @param mixed  $scalar
     * @param string $jsoned
     */
    public function testSerializeScalar($scalar, $jsoned)
    {
        $this->assertSame($jsoned, $this->serializer->serialize($scalar));
    }

    /**
     * Test unserialization of scalar values.
     *
     * @dataProvider scalarDataToJson
     *
     * @param mixed  $scalar
     * @param string $jsoned
     */
    public function testUnserializeScalar($scalar, $jsoned)
    {
        $this->assertSame($scalar, $this->serializer->unserialize($jsoned));
    }

    /**
     * List of scalar data.
     *
     * @return array
     */
    public function scalarDataToJson()
    {
        return [
            ['testing', '{"@scalar":"string","@value":"testing"}'],
            [123, '{"@scalar":"integer","@value":123}'],
            [0, '{"@scalar":"integer","@value":0}'],
            [0.0, '{"@scalar":"float","@value":0}'],
            [17.0, '{"@scalar":"float","@value":17}'],
            [17e1, '{"@scalar":"float","@value":170}'],
            [17.25, '{"@scalar":"float","@value":17.25}'],
            [true, '{"@scalar":"boolean","@value":true}'],
            [false, '{"@scalar":"boolean","@value":false}'],
            [null, '{"@scalar":"NULL","@value":null}'],
            // Non UTF8
            ['ßåö', '{"@scalar":"string","@value":"ßåö"}'],
        ];
    }

    /**
     * Test the serialization of resources.
     */
    public function testSerializeResource()
    {
        $this->expectException(SerializerException::class);
        $this->serializer->serialize(fopen(__FILE__, 'r'));
    }

    /**
     * Test the serialization of closures.
     */
    public function testSerializeClosure()
    {
        $this->expectException(SerializerException::class);
        $this->serializer->serialize(['func' => function () {
            echo 'whoops';
        }]);
    }

    /**
     * Test serialization of array without objects.
     *
     * @dataProvider arrayNoObjectData
     *
     * @param array  $array
     * @param string $jsoned
     */
    public function testSerializeArrayNoObject($array, $jsoned)
    {
        $this->assertSame($jsoned, $this->serializer->serialize($array));
    }

    /**
     * Test unserialization of array without objects.
     *
     * @dataProvider arrayNoObjectData
     *
     * @param array  $array
     * @param string $jsoned
     */
    public function testUnserializeArrayNoObject($array, $jsoned)
    {
        $this->assertSame($array, $this->serializer->unserialize($jsoned));
    }

    /**
     * List of array data.
     *
     * @return array
     */
    public function arrayNoObjectData()
    {
        return [
            [[1, 2, 3], '{"@map":"array","@value":[{"@scalar":"integer","@value":1},{"@scalar":"integer","@value":2},{"@scalar":"integer","@value":3}]}'],
            [[1, 'abc', false], '{"@map":"array","@value":[{"@scalar":"integer","@value":1},{"@scalar":"string","@value":"abc"},{"@scalar":"boolean","@value":false}]}'],
            [['a' => 1, 'b' => 2, 'c' => 3], '{"@map":"array","@value":{"a":{"@scalar":"integer","@value":1},"b":{"@scalar":"integer","@value":2},"c":{"@scalar":"integer","@value":3}}}'],
            [['integer' => 1, 'string' => 'abc', 'bool' => false], '{"@map":"array","@value":{"integer":{"@scalar":"integer","@value":1},"string":{"@scalar":"string","@value":"abc"},"bool":{"@scalar":"boolean","@value":false}}}'],
            [[1, ['nested']], '{"@map":"array","@value":[{"@scalar":"integer","@value":1},{"@map":"array","@value":[{"@scalar":"string","@value":"nested"}]}]}'],
            [['integer' => 1, 'array' => ['nested']], '{"@map":"array","@value":{"integer":{"@scalar":"integer","@value":1},"array":{"@map":"array","@value":[{"@scalar":"string","@value":"nested"}]}}}'],
            [['integer' => 1, 'array' => ['nested' => 'object']], '{"@map":"array","@value":{"integer":{"@scalar":"integer","@value":1},"array":{"@map":"array","@value":{"nested":{"@scalar":"string","@value":"object"}}}}}'],
            [[1.0, 2, 3e1], '{"@map":"array","@value":[{"@scalar":"float","@value":1},{"@scalar":"integer","@value":2},{"@scalar":"float","@value":30}]}'],
        ];
    }

    /**
     * Test serialization of DateTimeImmutable classes.
     *
     * Some interal classes, such as DateTimeImmutable, cannot be initialized with
     * ReflectionClass::newInstanceWithoutConstructor()
     */
    public function testSerializationOfDateTimeImmutable()
    {
        if (version_compare(PHP_VERSION, '5.5.0', '<')) {
            $this->markTestSkipped('Supported for PHP 5.5.0 and above');
        }

        $date = (new DateTimeImmutable('2014-06-15 12:00:00', new DateTimeZone('UTC')))->format('c');
        $obj = $this->serializer->unserialize($this->serializer->serialize($date));

        $this->assertSame($date, $obj);
        $this->assertEquals($date, $obj);
    }

    /**
     * Test serialization of DateInterval classes.
     *
     * Some interal classes, such as DateInterval, cannot be initialized with
     * ReflectionClass::newInstanceWithoutConstructor()
     */
    public function testSerializationOfDateInterval()
    {
        $date = new DateInterval('P2Y4DT6H8M');
        $obj = $this->serializer->unserialize($this->serializer->serialize($date));
        $this->assertEquals($date, $obj);
        $this->assertSame($date->d, $obj->d);
    }

    /**
     * Test serialization of DatePeriod classes.
     *
     * Some interal classes, such as DatePeriod, cannot be initialized with
     * ReflectionClass::newInstanceWithoutConstructor()
     */
    public function testSerializationOfDatePeriodException()
    {
        $this->expectException(
            SerializerException::class,
            'DatePeriod is not supported in Serializer. Loop through it and serialize the output.'
        );

        $period = new DatePeriod(new DateTime('2012-07-01'), new DateInterval('P7D'), 4);
        $this->serializer->serialize($period);
    }

    /**
     * Test unserialize of unknown class.
     */
    public function testUnserializeUnknownClass()
    {
        $this->expectException(SerializerException::class);
        $serialized = '{"@type":"UnknownClass"}';
        $this->serializer->unserialize($serialized);
    }

    /**
     * Test serialization of undeclared properties.
     */
    public function testSerializationUndeclaredProperties()
    {
        $obj = new stdClass();
        $obj->param1 = true;
        $obj->param2 = 'store me, please';
        $serialized = '{"@type":"stdClass","param1":{"@scalar":"boolean","@value":true},"param2":{"@scalar":"string","@value":"store me, please"}}';
        $this->assertSame($serialized, $this->serializer->serialize($obj));

        $obj2 = $this->serializer->unserialize($serialized);
        $this->assertInstanceOf('stdClass', $obj2);
        $this->assertTrue($obj2->param1);
        $this->assertSame('store me, please', $obj2->param2);

        $serialized = '{"@type":"stdClass","sub":{"@type":"stdClass","key":"value"}}';
        $obj = $this->serializer->unserialize($serialized);
        $this->assertInstanceOf('stdClass', $obj->sub);
        $this->assertSame('value', $obj->sub->key);
    }

    /**
     * Test serialize with recursion.
     */
    public function testSerializeRecursion()
    {
        $c1 = new stdClass();
        $c1->c2 = new stdClass();
        $c1->c2->c3 = new stdClass();
        $c1->c2->c3->c1 = $c1;
        $c1->something = 'ok';
        $c1->c2->c3->ok = true;

        $expected = '{"@type":"stdClass","c2":{"@type":"stdClass","c3":{"@type":"stdClass","c1":{"@type":"@0"},"ok":{"@scalar":"boolean","@value":true}}},"something":{"@scalar":"string","@value":"ok"}}';
        $this->assertSame($expected, $this->serializer->serialize($c1));

        $c1 = new stdClass();
        $c1->mirror = $c1;
        $expected = '{"@type":"stdClass","mirror":{"@type":"@0"}}';
        $this->assertSame($expected, $this->serializer->serialize($c1));
    }

    /**
     * Test unserialize with recursion.
     */
    public function testUnserializeRecursion()
    {
        $serialized = '{"@type":"stdClass","c2":{"@type":"stdClass","c3":{"@type":"stdClass","c1":{"@type":"@0"},"ok":{"@scalar":"boolean","@value":true}}},"something":{"@scalar":"string","@value":"ok"}}';
        $obj = $this->serializer->unserialize($serialized);
        $this->assertTrue($obj->c2->c3->ok);
        $this->assertSame($obj, $obj->c2->c3->c1);
        $this->assertNotSame($obj, $obj->c2);

        $serialized = '{"@type":"stdClass","c2":{"@type":"stdClass","c3":{"@type":"stdClass","c1":{"@type":"@0"},"c2":{"@type":"@1"},"c3":{"@type":"@2"}},"c3_copy":{"@type":"@2"}}}';
        $obj = $this->serializer->unserialize($serialized);
        $this->assertSame($obj, $obj->c2->c3->c1);
        $this->assertSame($obj->c2, $obj->c2->c3->c2);
        $this->assertSame($obj->c2->c3, $obj->c2->c3->c3);
        $this->assertSame($obj->c2->c3_copy, $obj->c2->c3);
    }

    public function testItCanGetTransformer()
    {
        $strategy = new JsonStrategy();
        $serializer = new Serializer($strategy);

        $this->assertSame($strategy, $serializer->getTransformer());
    }

    public function testSerializationOfAnArrayOfScalars()
    {
        $scalar = 'a string';

        $serializer = new Serializer(new JsonStrategy());
        $serialized = $serializer->serialize($scalar);

        $this->assertEquals($scalar, $serializer->unserialize($serialized));
    }
}
