<?php

declare(strict_types=1);

namespace Qubus\Tests\Support;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Qubus\Support\Container\ObjectStorageMap;
use Qubus\Support\DataType;

class ContainerTest extends TestCase
{
    protected ContainerInterface $container;

    protected function setUp(): void
    {
        $this->container = new ObjectStorageMap();
    }

    public function testWithString()
    {
        $this->container['param'] = 'value';

        Assert::assertEquals(expected: 'value', actual: $this->container['param']);
    }

    public function testWithClosure()
    {
        $this->container['datatype'] = function () {
            return new DataType();
        };

        Assert::assertInstanceOf(expected: DataType::class, actual: $this->container['datatype']);
    }

    public function testDataTypesShouldBeDifferent()
    {
        $this->container['datatype'] = $this->container->factory(callable: function () {
            return new DataType();
        });

        $dataTypeOne = $this->container['datatype'];
        Assert::assertInstanceOf(expected: DataType::class, actual: $dataTypeOne);

        $dataTypeTwo = $this->container['datatype'];
        Assert::assertInstanceOf(expected: DataType::class, actual: $dataTypeTwo);

        Assert::assertNotSame(expected: $dataTypeOne, actual: $dataTypeTwo);
    }

    public function testDataTypesShouldBeTheSame()
    {
        $this->container->singleton(key: 'datatype', value: function () {
            return new DataType();
        });

        $dataTypeOne = $this->container['datatype'];
        Assert::assertInstanceOf(expected: DataType::class, actual: $dataTypeOne);

        $dataTypeTwo = $this->container['datatype'];
        Assert::assertInstanceOf(expected: DataType::class, actual: $dataTypeTwo);

        Assert::assertSame(expected: $dataTypeOne, actual: $dataTypeTwo);
    }

    public function testShouldPassContainerAsParameter()
    {
        $this->container['datatype'] = function () {
            return new DataType();
        };

        $this->container['container'] = function ($c) {
            return $c;
        };

        Assert::assertNotSame($this->container, $this->container['datatype']);
        Assert::assertSame($this->container, $this->container['container']);
    }

    public function testIsset()
    {
        $this->container['param'] = 'value';

        $this->container['datatype'] = function () {
            return new DataType();
        };

        $this->container['null'] = null;

        Assert::assertTrue(isset($this->container['param']));
        Assert::assertTrue(isset($this->container['datatype']));
        Assert::assertTrue(isset($this->container['null']));
        Assert::assertFalse(isset($this->container['non_existent']));
    }

    public function testConstructorInjection()
    {
        $params = ['param' => 'value'];
        $container = new ObjectStorageMap($params);

        Assert::assertSame(expected: $params['param'], actual: $container['param']);
    }

    public function testOffsetGetHonorsNullValues()
    {
        $this->container['foo'] = null;

        Assert::assertNull(actual: $this->container['foo']);
    }

    public function testUnset()
    {
        $this->container['param'] = 'value';

        $this->container['datatype'] = function () {
            return new DataType();
        };

        $this->container->remove(key:'param');

        Assert::assertFalse(condition: isset($this->container['param']));
        Assert::assertTrue(condition: isset($this->container['datatype']));

        $this->container->remove(key: 'datatype');

        Assert::assertFalse(condition: isset($this->container['datatype']));
    }
}
