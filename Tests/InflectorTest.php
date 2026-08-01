<?php

declare(strict_types=1);

namespace Qubus\Tests\Support;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Qubus\Exception\Data\TypeException;
use Qubus\Support\Inflector;

final class InflectorTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function pluralProvider(): iterable
    {
        yield 'regular' => ['book', 'books'];
        yield 'sibilant' => ['box', 'boxes'];
        yield 'consonant y' => ['category', 'categories'];
        yield 'matrix' => ['matrix', 'matrices'];
        yield 'index' => ['index', 'indices'];
        yield 'sex does not match index rule' => ['sex', 'sexes'];
        yield 'mouse' => ['mouse', 'mice'];
        yield 'goose' => ['goose', 'geese'];
        yield 'foot' => ['foot', 'feet'];
        yield 'tooth' => ['tooth', 'teeth'];
        yield 'person' => ['person', 'people'];
        yield 'child' => ['child', 'children'];
        yield 'uncountable' => ['fish', 'fish'];
    }

    #[DataProvider('pluralProvider')]
    public function testPluralize(string $singular, string $plural): void
    {
        self::assertSame($plural, Inflector::pluralize($singular));
        self::assertSame($singular, Inflector::singularize($plural));
    }

    public function testPluralizeHonorsCountWithoutChangingTheInput(): void
    {
        self::assertSame('person', Inflector::pluralize('person', 1));
        self::assertSame('people', Inflector::pluralize('person', 2));
    }

    public function testPluralizeIsIdempotentForIrregularPlurals(): void
    {
        foreach (['people', 'men', 'children', 'mice', 'geese', 'feet', 'teeth', 'oxen'] as $plural) {
            self::assertSame($plural, Inflector::pluralize($plural));
        }
    }

    public function testOrdinalizeHandlesTeensAndNegativeNumbers(): void
    {
        self::assertSame('1st', Inflector::ordinalize(1));
        self::assertSame('12th', Inflector::ordinalize(12));
        self::assertSame('23rd', Inflector::ordinalize(23));
        self::assertSame('-1st', Inflector::ordinalize(-1));
        self::assertSame('-12th', Inflector::ordinalize(-12));
    }

    public function testCaseAndNamingConversions(): void
    {
        self::assertSame('XmlHttpRequest', Inflector::camelize('xml_http_request'));
        self::assertSame('xml_http_request', Inflector::underscore('XMLHttpRequest'));
        self::assertSame('UserProfile', Inflector::classify('user_profiles'));
        self::assertSame('UserProfiles', Inflector::classify('user_profiles', false));
        self::assertSame('admin_user_profiles', Inflector::tableize('App\\AdminUserProfile'));
    }

    public function testHumanizeHonorsLowercaseOption(): void
    {
        self::assertSame('Hello world', Inflector::humanize('HELLO_WORLD'));
        self::assertSame('HELLO WORLD', Inflector::humanize('HELLO_WORLD', lowercase: false));
        self::assertSame('Hello world', Inflector::humanize('HELLO-WORLD', '-'));
    }

    public function testNamespaceConversionsHandleRootAndQualifiedNames(): void
    {
        self::assertSame('ClassName', Inflector::denamespace('\\App\\Domain\\ClassName'));
        self::assertSame('App\\Domain\\', Inflector::getNamespace('\\App\\Domain\\ClassName'));
        self::assertSame('', Inflector::getNamespace('ClassName'));
        self::assertSame('ClassName', Inflector::demodulize('App::Domain::ClassName'));
    }

    public function testAsciiAndSlugifyNormalizeUnicode(): void
    {
        self::assertSame('Creme brulee', Inflector::ascii('Crème brûlée'));
        self::assertSame('creme-brulee', Inflector::slugify('Crème brûlée'));
    }

    public function testSlugifySupportsFlatAndNestedArrayInputs(): void
    {
        self::assertSame('hello-world-42', Inflector::slugify(['Hello', ['World', 42]]));
    }

    public function testSlugifyStripsEncodedMarkupEvenWithPermissiveOptions(): void
    {
        $slug = Inflector::slugify(
            '&lt;script&gt;alert&lt;/script&gt; Safe',
            ['regexp' => '/[^A-Za-z0-9<>]+/']
        );

        self::assertSame('alert-safe', $slug);
        self::assertStringNotContainsString('<', $slug);
    }

    public function testSlugifyRejectsUnsafeArrayValueTypesClearly(): void
    {
        $this->expectException(TypeException::class);
        Inflector::slugify([new \stdClass()]);
    }

    public function testCountabilityIsCaseInsensitive(): void
    {
        self::assertFalse(Inflector::isCountable('FISH'));
        self::assertTrue(Inflector::isCountable('person'));
    }
}
