<?php

declare(strict_types=1);

namespace Qubus\Tests\Support;

use ArrayIterator;
use DateTimeInterface;
use OutOfRangeException;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Qubus\Exception\Data\TypeException;
use Qubus\Support\ArrayHelper;
use Qubus\Support\Collection\ArrayList;
use Qubus\Support\Collection\BaseCollection;
use Qubus\Support\Collection\Collectionable;
use Qubus\Support\DataType;
use Qubus\Support\DateTime\QubusDate;
use Qubus\Support\DateTime\QubusDateTime;
use Qubus\Support\DateTime\QubusDateTimeImmutable;
use Qubus\Support\StringHelper;
use SplObjectStorage;

use function json_encode;

use const JSON_PRETTY_PRINT;

class ArrayListTest extends TestCase
{
    protected ?ArrayList $list = null;

    public function testArrayListIsEmpty(): void
    {
        $this->list = new ArrayList('string');

        Assert::assertTrue($this->list->isEmpty());
    }

    public function testArrayListIsStringType(): void
    {
        $this->list = new ArrayList('string');

        Assert::assertTrue($this->list->type() === 'string');
        Assert::assertTrue($this->list->getType() === 'string');
        Assert::assertTrue($this->list->getType() === $this->list->type());
        Assert::assertFalse($this->list->type() === 'object');
        Assert::assertFalse($this->list->type() === 'array');
        Assert::assertFalse($this->list->type() === DateTimeInterface::class);
    }

    public function testGetElementAtIndex(): void
    {
        $this->list = new ArrayList('string');
        $this->list
            ->add('fish')
            ->add('cat')
            ->add('pig')
            ->add('cow');

        Assert::assertEquals('pig', $this->list->get(2));
    }

    public function testSetElementAtIndex(): void
    {
        $this->list = new ArrayList('string');
        $this->list
            ->add('fish')
            ->add('cat')
            ->add('pig')
            ->add('cow');

        $this->list->set(2, 'monkey');

        Assert::assertEquals('monkey', $this->list->get(2));
        Assert::assertEquals(
            json_encode(["fish", "cat", "monkey", "cow"], JSON_PRETTY_PRINT),
            json_encode($this->list->all(), JSON_PRETTY_PRINT)
        );
        Assert::assertSame(
            json_encode(["fish", "cat", "monkey", "cow"], JSON_PRETTY_PRINT),
            json_encode($this->list->items(), JSON_PRETTY_PRINT)
        );
    }

    public function testRemoveElementAtIndex(): void
    {
        $this->list = new ArrayList('string');
        $this->list
            ->add('fish')
            ->add('cat')
            ->add('pig')
            ->add('cow');

        Assert::assertCount(4, $this->list->all());
        Assert::assertEquals(4, $this->list->count());
        Assert::assertEquals(4, $this->list->size());

        $this->list->remove(0);

        Assert::assertCount(3, $this->list->all());
        Assert::assertEquals(3, $this->list->count());
        Assert::assertEquals(3, $this->list->size());
        Assert::assertEquals(['cat', 'pig', 'cow'], $this->list->all());
    }

    public function testRemoveElementsWithinRange(): void
    {
        $this->list = new ArrayList('object');
        $this->list
            ->add(new DataType())
            ->add(new ArrayHelper())
            ->add(new StringHelper())
            ->add(QubusDate::fromString('now'))
            ->add(new SplObjectStorage())
            ->add(new ArrayIterator());

        $this->list->removeRange(3, 5);

        Assert::assertTrue($this->list->type() === 'object');
        Assert::assertEquals(
            [new DataType(), new ArrayHelper(), new StringHelper(), new ArrayIterator()],
            $this->list->all()
        );
    }

    public function testInstanceOfType(): void
    {
        $this->list = new ArrayList(DateTimeInterface::class);
        $this->list->add(new QubusDateTime())->add(new QubusDateTimeImmutable());

        Assert::assertSame(2, $this->list->size());
        Assert::assertTrue($this->list->type() === DateTimeInterface::class);
        Assert::assertInstanceOf(DateTimeInterface::class, $this->list->get(0));
        Assert::assertInstanceOf(DateTimeInterface::class, $this->list->get(1));
    }

    public function testRejectsWrongTypeThroughEveryMutationPath(): void
    {
        $mutations = [
            static fn (ArrayList $list) => $list->add(1),
            static fn (ArrayList $list) => $list->set(0, 1),
            static fn (ArrayList $list) => $list->push(1),
            static fn (ArrayList $list) => $list->put(0, 1),
            static function (ArrayList $list): void {
                $list[] = 1;
            },
        ];

        foreach ($mutations as $mutation) {
            $list = new ArrayList('string');
            $list->add('original');

            try {
                $mutation($list);
                self::fail('A mutation accepted a value of the wrong type.');
            } catch (TypeException) {
                self::assertSame(['original'], $list->all());
            }
        }
    }

    public function testArrayAccessCanAppendAndReplaceWithoutCreatingSparseIndexes(): void
    {
        $list = new ArrayList('string');
        $list[] = 'first';
        $list[] = 'second';
        $list[1] = 'changed';

        self::assertSame(['first', 'changed'], $list->all());

        $this->expectException(OutOfRangeException::class);
        $list[3] = 'sparse';
    }

    public function testArrayAccessUnsetReindexesTheList(): void
    {
        $list = new ArrayList('int');
        $list->add(10)->add(20)->add(30);

        unset($list[1]);

        self::assertSame([10, 30], $list->all());
        self::assertSame(30, $list->get(1));
    }

    public function testNullIsAReadableAndRemovableElement(): void
    {
        $list = new ArrayList('null');
        $list->add(null);

        self::assertNull($list->get(0));
        $list->remove(0);
        self::assertTrue($list->isEmpty());
    }

    public function testRemoveRangeAllowsEmptyRangesIncludingAtEnd(): void
    {
        $list = new ArrayList('int');
        $list->add(1)->add(2);

        $list->removeRange(1, 1);
        $list->removeRange(2, 2);

        self::assertSame([1, 2], $list->all());
    }

    public function testUnserializeValidatesAtomicallyAndReindexesInput(): void
    {
        $list = new ArrayList('string');
        $list->add('original');
        $list->unserialize([5 => 'first', 9 => 'second']);

        self::assertSame(['first', 'second'], $list->all());

        try {
            $list->unserialize(['valid', 1]);
            self::fail('Unserialize accepted an invalid value.');
        } catch (TypeException) {
            self::assertSame(['first', 'second'], $list->all());
        }
    }
}
