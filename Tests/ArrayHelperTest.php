<?php

declare(strict_types=1);

namespace Qubus\Tests\Support;

use ArrayObject;
use PHPUnit\Framework\TestCase;
use Qubus\Support\ArrayHelper;

final class ArrayHelperTest extends TestCase
{
    private ArrayHelper $helper;

    protected function setUp(): void
    {
        $this->helper = new ArrayHelper();
    }

    public function testGetHonorsStringDefaults(): void
    {
        self::assertSame('fallback', $this->helper->get([], 'missing', 'fallback'));
    }

    public function testDeleteReturnsFalseWhenAPathCrossesScalarData(): void
    {
        $array = ['user' => 'scalar'];

        self::assertFalse($this->helper->delete($array, 'user.name'));
        self::assertSame(['user' => 'scalar'], $array);
    }

    public function testPrefixAndSuffixFiltersTreatInputAsLiteralText(): void
    {
        $array = ['a[one' => 1, 'a.one' => 2, 'one.+$' => 3, 'two' => 4];

        self::assertSame(['one' => 1], $this->helper->filterPrefixed($array, 'a['));
        self::assertSame(
            ['a[one' => 1, 'a.one' => 2, 'two' => 4],
            $this->helper->removeSuffixed($array, '.+$')
        );
        self::assertSame(['one' => 3], $this->helper->filterSuffixed($array, '.+$'));
        self::assertSame($array, $this->helper->filterSuffixed($array, ''));
    }

    public function testRecursiveMembershipHonorsStrictFlag(): void
    {
        $array = ['nested' => [1]];

        self::assertTrue($this->helper->inArrayRecursive('1', $array));
        self::assertFalse($this->helper->inArrayRecursive('1', $array, true));
    }

    public function testUniqueDoesNotLeakStateBetweenCalls(): void
    {
        self::assertSame([0 => 'a', 2 => 'b'], $this->helper->unique(['a', 'a', 'b']));
        self::assertSame(['a', 'b'], $this->helper->unique(['a', 'b']));
    }

    public function testNavigationCanReturnValuesOfAnyType(): void
    {
        $object = new \stdClass();
        $array = ['first' => ['nested'], 'second' => $object, 'third' => 1.5];

        self::assertSame(['nested'], $this->helper->previousByKey($array, 'second', true));
        self::assertSame($object, $this->helper->nextByKey($array, 'first', true));
        self::assertSame(1.5, $this->helper->nextByValue($array, $object));
    }

    public function testEnumerableArrayAccessWorksAcrossAdvertisedOperations(): void
    {
        $items = new ArrayObject([
            ['score' => 2],
            ['score' => 3],
        ]);

        self::assertSame(5, $this->helper->sum($items, 'score'));
        self::assertSame(1, $this->helper->search($items, ['score' => 3], recursive: false, strict: true));
        self::assertSame(['score' => 3], $this->helper->nextByKey($items, 0, true));
    }

    public function testRecursiveSearchDoesNotConfuseAFoundKeyWithTheDefault(): void
    {
        self::assertSame('nested.missing', $this->helper->search(
            ['nested' => ['missing' => 'found']],
            'found',
            'missing'
        ));
    }

    public function testAssociativeDetectionExaminesKeysRatherThanValues(): void
    {
        self::assertFalse($this->helper->isAssoc(['first', 'second']));
        self::assertFalse($this->helper->isAssoc([]));
        self::assertTrue($this->helper->isAssoc([1 => 'first', 0 => 'second']));
        self::assertTrue($this->helper->isAssoc(['name' => 'value']));
    }

    public function testBulkHelpersHandleEdgeInputsWithoutWarnings(): void
    {
        self::assertSame(['a' => 1], $this->helper->only(['a' => 1, 'b' => 2], [[], 'a']));
        self::assertSame(['b' => 2], $this->helper->except(['a' => 1, 'b' => 2], [[], 'a']));
        self::assertSame([['name' => 'b']], $this->helper->multisort([['name' => 'b']], []));

        $array = ['a' => 1];
        self::assertFalse($this->helper->insertAssoc($array, 'invalid', 0));
        self::assertSame(['a' => 1], $array);
    }
}
