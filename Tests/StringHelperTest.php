<?php

declare(strict_types=1);

namespace Qubus\Tests\Support;

use BadMethodCallException;
use PHPUnit\Framework\TestCase;
use Qubus\Tests\Support\Mock\SerializedWakeupFixture;
use Qubus\Support\StringHelper;

final class StringHelperTest extends TestCase
{
    private StringHelper $helper;

    protected function setUp(): void
    {
        $this->helper = new StringHelper();
    }

    public function testStartAndEndChecksApplyToTheWholeString(): void
    {
        self::assertFalse($this->helper->startsWith("first\ntarget", 'target'));
        self::assertFalse($this->helper->endsWith("target\nlast", 'target'));
        self::assertTrue($this->helper->startsWith('Éclair', 'é', true));
        self::assertTrue($this->helper->endsWith('CAFÉ', 'fé', true));
        self::assertTrue($this->helper->startsWith('value', ''));
        self::assertTrue($this->helper->endsWith('value', ''));
    }

    public function testIncrementTreatsSeparatorAsLiteralText(): void
    {
        self::assertSame('release.10', $this->helper->increment('release.9', separator: '.'));
        self::assertSame('release[10', $this->helper->increment('release[9', separator: '['));
    }

    public function testRandomValuesKeepTheirFormatsAndUseValidUuidBits(): void
    {
        self::assertMatchesRegularExpression('/^[A-Za-z0-9]{64}$/', $this->helper->random('alnum', 64));
        self::assertMatchesRegularExpression('/^[0-9]{32}$/', $this->helper->random('numeric', 32));
        self::assertMatchesRegularExpression('/^[0-9a-f]{32}$/', $this->helper->random('unique'));
        self::assertMatchesRegularExpression('/^[0-9a-f]{40}$/', $this->helper->random('sha1'));
        self::assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $this->helper->random('sha256'));
        self::assertMatchesRegularExpression('/^[0-9a-f]{128}$/', $this->helper->random('sha512'));
        self::assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',
            $this->helper->random('uuid')
        );
    }

    public function testSerializedDetectionCannotInvokeObjectWakeupHooks(): void
    {
        SerializedWakeupFixture::$wokeUp = false;
        $serialized = serialize(new SerializedWakeupFixture());

        self::assertTrue($this->helper->isSerialized($serialized));
        self::assertFalse(SerializedWakeupFixture::$wokeUp);
        self::assertTrue($this->helper->isSerialized('b:0;'));
        self::assertTrue($this->helper->isSerialized('N;'));
        self::assertFalse($this->helper->isSerialized('not serialized'));
    }

    public function testAlternatorRequiresValues(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->helper->alternator();
    }

    public function testAlternatorCyclesAndCanPeek(): void
    {
        $alternate = $this->helper->alternator('odd', 'even');

        self::assertSame('odd', $alternate());
        self::assertSame('even', $alternate(false));
        self::assertSame('even', $alternate());
        self::assertSame('odd', $alternate());
    }

    public function testXmlDetectionRestoresLibxmlErrorMode(): void
    {
        $previous = libxml_use_internal_errors(false);

        try {
            self::assertTrue($this->helper->isXml('<root><item /></root>'));
            self::assertFalse($this->helper->isXml('<root>'));
            self::assertFalse(libxml_use_internal_errors());
        } finally {
            libxml_use_internal_errors($previous);
        }
    }
}
