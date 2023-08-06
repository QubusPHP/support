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

namespace Qubus\Tests\Support\Serializer;

use Exception;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Qubus\Support\Serializer\Serializer;
use Qubus\Support\Serializer\Strategy\JsonStrategy;
use Qubus\Support\Serializer\SerializerException;
use DateTimeImmutable;
use DateTimeZone;
use DateInterval;
use DatePeriod;
use DateTime;
use ReflectionException;
use stdClass;

class SerializerTest extends TestCase
{
    protected Serializer $serializer;

    /**
     * Test case setup.
     */
    public function setUp(): void
    {
        $this->serializer = new Serializer(new JsonStrategy());
    }

    /**
     * Test serialization of DateTime classes.
     *
     * Some internal classes, such as DateTime, cannot be initialized with
     * ReflectionClass::newInstanceWithoutConstructor()
     * @throws Exception
     */
    public function testSerializationOfDateTime()
    {
        $date = (new DateTime('2014-06-15 12:00:00', new DateTimeZone('UTC')))->format('c');

        $obj = $this->serializer->unserialize($this->serializer->serialize($date));
        Assert::assertSame($date, $obj);
        Assert::assertEquals($date, $obj);
    }

    /**
     * Test serialization of scalar values.
     *
     * @dataProvider scalarDataToJson
     *
     * @param mixed $scalar
     * @param string $jsoned
     * @throws ReflectionException
     */
    public function testSerializeScalar(mixed $scalar, string $jsoned)
    {
        Assert::assertSame($jsoned, $this->serializer->serialize($scalar));
    }

    /**
     * Test unserialization of scalar values.
     *
     * @dataProvider scalarDataToJson
     *
     * @param mixed $scalar
     * @param string $jsoned
     * @throws ReflectionException
     */
    public function testUnserializeScalar(mixed $scalar, string $jsoned)
    {
        Assert::assertSame($scalar, $this->serializer->unserialize($jsoned));
    }

    /**
     * List of scalar data.
     *
     * @return array
     */
    public function scalarDataToJson(): array
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
     * @throws ReflectionException
     */
    public function testSerializeResource()
    {
        $this->expectException(SerializerException::class);
        $this->serializer->serialize(fopen(__FILE__, 'r'));
    }

    /**
     * Test the serialization of closures.
     * @throws ReflectionException
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
     * @param array $array
     * @param string $jsoned
     * @throws ReflectionException
     */
    public function testSerializeArrayNoObject(array $array, string $jsoned)
    {
        Assert::assertSame($jsoned, $this->serializer->serialize($array));
    }

    /**
     * Test unserialization of array without objects.
     *
     * @dataProvider arrayNoObjectData
     *
     * @param array $array
     * @param string $jsoned
     * @throws ReflectionException
     */
    public function testUnserializeArrayNoObject(array $array, string $jsoned)
    {
        Assert::assertSame($array, $this->serializer->unserialize($jsoned));
    }

    /**
     * List of array data.
     *
     * @return array
     */
    public function arrayNoObjectData(): array
    {
        return [
            [
                [1, 2, 3],
                '{"@map":"array","@value":[{"@scalar":"integer","@value":1},{"@scalar":"integer","@value":2},
                {"@scalar":"integer","@value":3}]}'
            ],
            [
                [1, 'abc', false],
                '{"@map":"array","@value":[{"@scalar":"integer","@value":1},{"@scalar":"string","@value":"abc"},
                {"@scalar":"boolean","@value":false}]}'
            ],
            [
                ['a' => 1, 'b' => 2, 'c' => 3],
                '{"@map":"array","@value":{"a":{"@scalar":"integer","@value":1},
                "b":{"@scalar":"integer","@value":2},"c":{"@scalar":"integer","@value":3}}}'
            ],
            [
                ['integer' => 1, 'string' => 'abc', 'bool' => false],
                '{"@map":"array","@value":{"integer":{"@scalar":"integer","@value":1},
                "string":{"@scalar":"string","@value":"abc"},"bool":{"@scalar":"boolean","@value":false}}}'
            ],
            [
                [1, ['nested']],
                '{"@map":"array","@value":[{"@scalar":"integer","@value":1},{"@map":"array",
                "@value":[{"@scalar":"string","@value":"nested"}]}]}'
            ],
            [
                ['integer' => 1, 'array' => ['nested']],
                '{"@map":"array","@value":{"integer":{"@scalar":"integer","@value":1},
                "array":{"@map":"array","@value":[{"@scalar":"string","@value":"nested"}]}}}'
            ],
            [
                ['integer' => 1, 'array' => ['nested' => 'object']],
                '{"@map":"array","@value":{"integer":{"@scalar":"integer","@value":1},
                "array":{"@map":"array","@value":{"nested":{"@scalar":"string","@value":"object"}}}}}'
            ],
            [
                [1.0, 2, 3e1],
                '{"@map":"array","@value":[{"@scalar":"float","@value":1},{"@scalar":"integer","@value":2},
                {"@scalar":"float","@value":30}]}'
            ],
        ];
    }

    /**
     * Test serialization of DateTimeImmutable classes.
     *
     * Some internal classes, such as DateTimeImmutable, cannot be initialized with
     * ReflectionClass::newInstanceWithoutConstructor()
     * @throws Exception
     */
    public function testSerializationOfDateTimeImmutable()
    {
        if (version_compare(PHP_VERSION, '5.5.0', '<')) {
            $this->markTestSkipped('Supported for PHP 5.5.0 and above');
        }

        $date = (new DateTimeImmutable('2014-06-15 12:00:00', new DateTimeZone('UTC')))->format('c');
        $obj = $this->serializer->unserialize($this->serializer->serialize($date));

        Assert::assertSame($date, $obj);
        Assert::assertEquals($date, $obj);
    }

    /**
     * Test serialization of DateInterval classes.
     *
     * Some internal classes, such as DateInterval, cannot be initialized with
     * ReflectionClass::newInstanceWithoutConstructor()
     * @throws ReflectionException
     */
    public function testSerializationOfDateInterval()
    {
        $date = new DateInterval('P2Y4DT6H8M');
        $obj = $this->serializer->unserialize($this->serializer->serialize($date));
        Assert::assertEquals($date, $obj);
        Assert::assertSame($date->d, $obj->d);
    }

    /**
     * Test serialization of DatePeriod classes.
     *
     * Some internal classes, such as DatePeriod, cannot be initialized with
     * ReflectionClass::newInstanceWithoutConstructor()
     * @throws ReflectionException
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
     * @throws ReflectionException
     */
    public function testUnserializeUnknownClass()
    {
        $this->expectException(SerializerException::class);
        $serialized = '{"@type":"UnknownClass"}';
        $this->serializer->unserialize($serialized);
    }

    /**
     * Test serialization of undeclared properties.
     * @throws ReflectionException
     */
    public function testSerializationUndeclaredProperties()
    {
        $obj = new stdClass();
        $obj->param1 = true;
        $obj->param2 = 'store me, please';
        $serialized = '{"@type":"stdClass","param1":{"@scalar":"boolean","@value":true},"param2":{"@scalar":"string",
        "@value":"store me, please"}}';
        Assert::assertSame($serialized, $this->serializer->serialize($obj));

        $obj2 = $this->serializer->unserialize($serialized);
        Assert::assertInstanceOf('stdClass', $obj2);
        Assert::assertTrue($obj2->param1);
        Assert::assertSame('store me, please', $obj2->param2);

        $serialized = '{"@type":"stdClass","sub":{"@type":"stdClass","key":"value"}}';
        $obj = $this->serializer->unserialize($serialized);
        Assert::assertInstanceOf('stdClass', $obj->sub);
        Assert::assertSame('value', $obj->sub->key);
    }

    /**
     * Test serialize with recursion.
     * @throws ReflectionException
     */
    public function testSerializeRecursion()
    {
        $c1 = new stdClass();
        $c1->c2 = new stdClass();
        $c1->c2->c3 = new stdClass();
        $c1->c2->c3->c1 = $c1;
        $c1->something = 'ok';
        $c1->c2->c3->ok = true;

        $expected = '{"@type":"stdClass","c2":{"@type":"stdClass","c3":{"@type":"stdClass","c1":{"@type":"@0"},
        "ok":{"@scalar":"boolean","@value":true}}},"something":{"@scalar":"string","@value":"ok"}}';
        Assert::assertSame($expected, $this->serializer->serialize($c1));

        $c1 = new stdClass();
        $c1->mirror = $c1;
        $expected = '{"@type":"stdClass","mirror":{"@type":"@0"}}';
        Assert::assertSame($expected, $this->serializer->serialize($c1));
    }

    /**
     * Test unserialize with recursion.
     * @throws ReflectionException
     */
    public function testUnserializeRecursion()
    {
        $serialized = '{"@type":"stdClass","c2":{"@type":"stdClass","c3":{"@type":"stdClass","c1":{"@type":"@0"},
        "ok":{"@scalar":"boolean","@value":true}}},"something":{"@scalar":"string","@value":"ok"}}';
        $obj = $this->serializer->unserialize($serialized);
        Assert::assertTrue($obj->c2->c3->ok);
        Assert::assertSame($obj, $obj->c2->c3->c1);
        Assert::assertNotSame($obj, $obj->c2);

        $serialized = '{"@type":"stdClass","c2":{"@type":"stdClass","c3":{"@type":"stdClass","c1":{"@type":"@0"},
        "c2":{"@type":"@1"},"c3":{"@type":"@2"}},"c3_copy":{"@type":"@2"}}}';
        $obj = $this->serializer->unserialize($serialized);
        Assert::assertSame($obj, $obj->c2->c3->c1);
        Assert::assertSame($obj->c2, $obj->c2->c3->c2);
        Assert::assertSame($obj->c2->c3, $obj->c2->c3->c3);
        Assert::assertSame($obj->c2->c3_copy, $obj->c2->c3);
    }

    public function testItCanGetTransformer()
    {
        $strategy = new JsonStrategy();
        $serializer = new Serializer($strategy);

        Assert::assertSame($strategy, $serializer->getTransformer());
    }

    /**
     * @throws ReflectionException
     */
    public function testSerializationOfAnArrayOfScalars()
    {
        $scalar = 'a string';

        $serializer = new Serializer(new JsonStrategy());
        $serialized = $serializer->serialize($scalar);

        Assert::assertEquals($scalar, $serializer->unserialize($serialized));
    }
}
