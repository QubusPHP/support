<?php

/**
 * Qubus\Support
 *
 * @link       https://github.com/QubusPHP/support
 * @copyright  2020 Joshua Parker <josh@joshuaparker.blog>
 * @copyright  2015 Stolz
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 *
 * @since      1.0.0
 */

namespace Qubus\Tests\Support;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Qubus\Support\Assets;
use ReflectionClass;

use function Qubus\Support\Helpers\camel_case;

class AssetsTest extends TestCase
{
    protected $asset;

    protected function setUp(): void
    {
        $this->asset = new Assets();
    }

    public function testRemoteLinkDetection()
    {
        $method = self::getMethod('isRemoteLink');

        Assert::assertTrue($method->invokeArgs($this->asset, ['http://foo']));
        Assert::assertTrue($method->invokeArgs($this->asset, ['https://foo']));
        Assert::assertTrue($method->invokeArgs($this->asset, ['//foo']));

        Assert::assertFalse($method->invokeArgs($this->asset, ['/']));
        Assert::assertFalse($method->invokeArgs($this->asset, ['/foo']));
        Assert::assertFalse($method->invokeArgs($this->asset, ['foo']));
    }

    public function testPackageAssetDetection()
    {
        $vendor = '_This-Is-Vendor.0';
        $name = '_This-Is-Package.9';
        $asset = 'local/asset.css';

        $method = self::getMethod('assetIsFromPackage');
        $package = $method->invokeArgs($this->asset, ["$vendor/$name:$asset"]);

        Assert::assertCount(3, $package);
        Assert::assertEquals($vendor, $package[0]);
        Assert::assertEquals($name, $package[1]);
        Assert::assertEquals($asset, $package[2]);

        Assert::assertFalse($method->invokeArgs($this->asset, ['foo']));
        Assert::assertFalse($method->invokeArgs($this->asset, ['foo/bar']));
        Assert::assertFalse($method->invokeArgs($this->asset, ['foo/bar/foo:bar']));
        Assert::assertFalse($method->invokeArgs($this->asset, ['foo:bar']));
    }

    public function testAddOneCss()
    {
        Assert::assertCount(0, $this->asset->getCss());

        $asset = uniqid('asset');
        $this->asset->addCss($asset);
        $assets = $this->asset->getCss();

        Assert::assertCount(1, $assets);
        Assert::assertStringEndsWith($asset, array_pop($assets));
        Assert::assertCount(0, $assets);
    }

    public function testPrependOneCss()
    {
        Assert::assertCount(0, $this->asset->getCss());

        $asset1 = uniqid('asset1');
        $asset2 = uniqid('asset2');
        $this->asset->addCss($asset2);
        $this->asset->prependCss($asset1);

        $assets = $this->asset->getCss();
        Assert::assertStringEndsWith($asset2, array_pop($assets));
        Assert::assertStringEndsWith($asset1, array_pop($assets));
        Assert::assertCount(0, $assets);
    }

    public function testAddOneJs()
    {
        Assert::assertCount(0, $this->asset->getJs());

        $asset = uniqid('asset');
        $this->asset->addJs($asset);
        $assets = $this->asset->getJs();

        Assert::assertCount(1, $assets);
        Assert::assertStringEndsWith($asset, array_pop($assets));
        Assert::assertCount(0, $assets);
    }

    public function testPrependOneJs()
    {
        Assert::assertCount(0, $this->asset->getJs());

        $asset1 = uniqid('asset1');
        $asset2 = uniqid('asset2');
        $this->asset->addJs($asset2);
        $this->asset->prependJs($asset1);

        $assets = $this->asset->getJs();
        Assert::assertStringEndsWith($asset2, array_pop($assets));
        Assert::assertStringEndsWith($asset1, array_pop($assets));
        Assert::assertCount(0, $assets);
    }

    public function testAddMultipleCss()
    {
        Assert::assertCount(0, $this->asset->getCss());

        $asset1 = uniqid('asset1');
        $asset2 = uniqid('asset2');
        $this->asset->addCss([$asset1, $asset2]);
        $assets = $this->asset->getCss();

        Assert::assertCount(2, $assets);
        Assert::assertStringEndsWith($asset2, array_pop($assets));
        Assert::assertStringEndsWith($asset1, array_pop($assets));
        Assert::assertCount(0, $assets);
    }

    public function testPrependMultipleCss()
    {
        Assert::assertCount(0, $this->asset->getCss());

        $asset1 = uniqid('asset1');
        $asset2 = uniqid('asset2');
        $asset3 = uniqid('asset3');
        $this->asset->addCss($asset3);
        $this->asset->prependCss([$asset1, $asset2]);
        $assets = $this->asset->getCss();

        Assert::assertCount(3, $assets);
        Assert::assertStringEndsWith($asset3, array_pop($assets));
        Assert::assertStringEndsWith($asset2, array_pop($assets));
        Assert::assertStringEndsWith($asset1, array_pop($assets));
        Assert::assertCount(0, $assets);
    }

    public function testAddMultipleJs()
    {
        Assert::assertCount(0, $this->asset->getJs());

        $asset1 = uniqid('asset1');
        $asset2 = uniqid('asset2');
        $this->asset->addJs([$asset1, $asset2]);
        $assets = $this->asset->getJs();

        Assert::assertCount(2, $assets);
        Assert::assertStringEndsWith($asset2, array_pop($assets));
        Assert::assertStringEndsWith($asset1, array_pop($assets));
        Assert::assertCount(0, $assets);
    }

    public function testPrependMultipleJs()
    {
        Assert::assertCount(0, $this->asset->getJs());

        $asset1 = uniqid('asset1');
        $asset2 = uniqid('asset2');
        $asset3 = uniqid('asset3');
        $this->asset->addJs($asset3);
        $this->asset->prependJs([$asset1, $asset2]);
        $assets = $this->asset->getJs();

        Assert::assertCount(3, $assets);
        Assert::assertStringEndsWith($asset3, array_pop($assets));
        Assert::assertStringEndsWith($asset2, array_pop($assets));
        Assert::assertStringEndsWith($asset1, array_pop($assets));
        Assert::assertCount(0, $assets);
    }

    public function testDetectAndAddCss()
    {
        Assert::assertCount(0, $this->asset->getCss());
        Assert::assertCount(0, $this->asset->getJs());

        $asset = 'foo.css';
        $this->asset->addCss($asset);

        Assert::assertCount(1, $assets = $this->asset->getCss());
        Assert::assertCount(0, $this->asset->getJs());
        Assert::assertStringEndsWith($asset, array_pop($assets));
    }

    public function testDetectAndAddJs()
    {
        Assert::assertCount(0, $this->asset->getCss());
        Assert::assertCount(0, $this->asset->getJs());

        $asset = 'foo.js';
        $this->asset->addJs($asset);

        Assert::assertCount(1, $assets = $this->asset->getJs());
        Assert::assertCount(0, $this->asset->getCss());
        Assert::assertStringEndsWith($asset, array_pop($assets));
    }

    public function testDetectAndAddCollection()
    {
        $asset1 = 'foo.js';
        $asset2 = 'foo.css';
        $collection = [$asset1, $asset2];
        $this->asset->config(['collections' => ['collection' => $collection]]);

        Assert::assertCount(0, $this->asset->getCss());
        Assert::assertCount(0, $this->asset->getJs());

        $this->asset->add('collection');

        Assert::assertCount(1, $assets1 = $this->asset->getJs());
        Assert::assertCount(1, $assets2 = $this->asset->getCss());

        Assert::assertStringEndsWith($asset1, array_pop($assets1));
        Assert::assertStringEndsWith($asset2, array_pop($assets2));
    }

    protected static function getMethod($name)
    {
        $class = new ReflectionClass('Qubus\Support\Assets');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }
}
