<?php

declare(strict_types=1);

namespace Qubus\Tests\Support;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Qubus\Exception\Data\TypeException;
use Qubus\Support\Collection\Arrayable;
use Qubus\Support\Collection\ArrayCollection;
use Qubus\Support\Collection\Collectionable;
use Qubus\Support\Container\ObjectStorageMap;
use Qubus\Support\DataType;

use Qubus\Tests\Support\Mock\Bar;
use Qubus\Tests\Support\Mock\BarCollection;

use function strtoupper;

class CollectionTest extends TestCase
{
    protected Collectionable $collection;

    protected function setUp(): void
    {
        $array = ['cat' => 'lion', 'dog' => 'german shepard', 'feline' => 'minx'];

        $this->collection = new ArrayCollection($array);
    }

    public function testClassInstanceOfCollectionable()
    {
        Assert::assertInstanceOf(Collectionable::class, $this->collection);
    }

    public function testClassInstanceOfArrayable()
    {
        Assert::assertInstanceOf(Arrayable::class, $this->collection);
    }

    public function testGetTypeIsArray()
    {
        Assert::assertSame($this->collection->getType(), 'array');
    }

    public function testCollectionContainsElement()
    {
        Assert::assertContains('german shepard', $this->collection->getIterator());

        Assert::assertTrue($this->collection->contains('german shepard'));
    }

    public function testCollectionMap()
    {
        $collection = $this->collection->map(fn($item) => strtoupper($item));

        Assert::assertSame($collection->items(), ['LION', 'GERMAN SHEPARD', 'MINX']);
    }

    public function testCollectionItemsAreFlipped()
    {
        $collection = $this->collection->flip();

        Assert::assertEquals(['lion' => 'cat', 'german shepard' => 'dog', 'minx' => 'feline'], $collection->all());
    }

    public function testCollectionItemsAreFiltered()
    {
        $filtered = $this->collection->filter(function ($item, $key) {
            return $key !== 'dog';
        });

        $reject = $this->collection->reject(function ($item, $key) {
            return $key === 'dog';
        });

        Assert::assertEquals(['lion', 'minx'], $filtered->all());

        Assert::assertEquals(['lion', 'minx'], $reject->all());
    }

    public function testCollectionGetItem()
    {
        $collection = $this->collection->get('cat');

        Assert::assertEquals('lion', $collection);
    }

    public function testCollectionIsSliced()
    {
        $collection = $this->collection->slice(2);

        Assert::assertEquals(['feline' => 'minx'], $collection->all());
    }

    public function testNewCollectionAtNth()
    {
        $collection = $this->collection->nth(2);

        Assert::assertEquals(['lion','minx'], $collection->all());
    }

    public function testCollectionIsPushable()
    {
        $push = $this->collection->push(value: 'strawberry');

        Assert::assertEquals(
            ['cat' => 'lion', 'dog' => 'german shepard', 'feline' => 'minx', 'strawberry'],
            $push->all()
        );
    }

    public function testCollectionCanBePut()
    {
        $put = $this->collection->put('fruit', 'strawberry');

        Assert::assertEquals(
            ['cat' => 'lion', 'dog' => 'german shepard', 'feline' => 'minx', 'fruit' => 'strawberry'],
            $put->all()
        );
    }

    public function testCollectionIndexedValues()
    {
        $indexed = $this->collection->values();

        Assert::assertEquals(
            [0 => 'lion', 1 => 'german shepard', 2 => 'minx'],
            $indexed->all()
        );
    }

    public function testCollectionArrayCanBeFlattened()
    {
        $flatten = $this->collection->merge(
            ['food' =>
                ['cereal',
                    ['fruit' => 'strawberry'
                    ]
                ]
            ]
        )->flatten();

        Assert::assertEquals(
            [
                'cat' => 'lion',
                'dog' => 'german shepard',
                'feline' => 'minx',
                'food:0' => 'cereal',
                'food:1:fruit' => 'strawberry'
            ],
            $flatten->all()
        );
    }

    public function testCollectionIsSortedAndThenSortedByCallback()
    {
        $uasort = $this->collection->sort(function ($item, $key) {
            return strnatcmp($item, $key);
        })->merge(['cookie' => 'oatmeal raisin']);

        Assert::assertEquals(
            [
            'cat' => 'lion',
            'cookie' => 'oatmeal raisin',
            'dog' => 'german shepard',
            'feline' => 'minx'
            ],
            $uasort->all()
        );

        $asort = $this->collection->sort()->merge(['cookie' => 'oatmeal raisin']);

        Assert::assertEquals(
            [
                'cat' => 'lion',
                'dog' => 'german shepard',
                'feline' => 'minx',
                'cookie' => 'oatmeal raisin',
            ],
            $asort->all()
        );
    }

    public function testCollectionInReverseOrder()
    {
        $reverse = $this->collection->reverse();

        Assert::assertEquals(['feline' => 'minx', 'dog' => 'german shepard', 'cat' => 'lion'], $reverse->all());
    }

    public function testCollectionBySearch()
    {
        $search = $this->collection->search('german shepard');

        Assert::assertEquals('dog', $search);
    }

    public function testCollectionNewArrayOfKeys()
    {
        $keys = $this->collection->keys();

        Assert::assertEquals([0 => 'cat', 1 => 'dog', 2 => 'feline'], $keys->all());
    }

    public function testCollectionCount()
    {
        Assert::assertCount(3, $this->collection->getIterator());
    }

    public function testCollectionOfAllItems()
    {
        Assert::assertEquals(
            [
                'cat' => 'lion',
                'dog' => 'german shepard',
                'feline' => 'minx'
            ],
            $this->collection->all()
        );
    }

    public function testCollectionGetLastItem()
    {
        $pop = $this->collection->pop();

        Assert::assertEquals('minx', $pop);

        $last = $this->collection->last();

        Assert::assertEquals('german shepard', $last);
    }

    public function testCollectionGetFirstItem()
    {
        $shift = $this->collection->shift();

        Assert::assertEquals('lion', $shift);

        $first = $this->collection->first();

        Assert::assertEquals('german shepard', $first);
    }

    public function testColumnByProperty(): void
    {
        $bar1 = new Bar(1, 'a');
        $bar2 = new Bar(2, 'b');
        $bar3 = new Bar(3, 'c');
        $barCollection = new BarCollection([$bar1, $bar2, $bar3]);

        Assert::assertEquals(['a', 'b', 'c'], $barCollection->column('name'));
    }

    public function testColumnByMethod(): void
    {
        $bar1 = new Bar(1, 'a');
        $bar2 = new Bar(2, 'b');
        $bar3 = new Bar(3, 'c');
        $barCollection = new BarCollection([$bar1, $bar2, $bar3]);

        Assert::assertEquals([1, 2, 3], $barCollection->column('getId'));
    }

    public function testFirst(): void
    {
        $bar1 = new Bar(1, 'a');
        $bar2 = new Bar(2, 'b');
        $bar3 = new Bar(3, 'c');
        $barCollection = new BarCollection([$bar1, $bar2, $bar3]);

        Assert::assertSame($bar1, $barCollection->first());
        // Make sure the collection stays unchanged
        Assert::assertEquals([$bar1, $bar2, $bar3], $barCollection->toArray());
    }

    public function testLast(): void
    {
        $bar1 = new Bar(1, 'a');
        $bar2 = new Bar(2, 'b');
        $bar3 = new Bar(3, 'c');
        $barCollection = new BarCollection([$bar1, $bar2, $bar3]);

        Assert::assertSame($bar3, $barCollection->last());
        // Make sure the collection stays unchanged
        Assert::assertEquals([$bar1, $bar2, $bar3], $barCollection->toArray());
    }
}
