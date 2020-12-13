<?php

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

    public function testConfigSetsDirs()
    {
        $config = [
            'public_dir'   => __DIR__,
            'css_dir'      => uniqid('css'),
            'js_dir'       => uniqid('js'),
            'packages_dir' => uniqid('packages'),
            'pipeline_dir' => uniqid('pipe'),
        ];

        $this->asset->config($config);

        foreach ($config as $key => $value) {
            $newKey = camel_case($key);
            $this->assertEquals($value, Assert::readAttribute($this->asset, $newKey));
        }
    }

    public function testRemoteLinkDetection()
    {
        $method = self::getMethod('isRemoteLink');

        $this->assertTrue($method->invokeArgs($this->asset, ['http://foo']));
        $this->assertTrue($method->invokeArgs($this->asset, ['https://foo']));
        $this->assertTrue($method->invokeArgs($this->asset, ['//foo']));

        $this->assertFalse($method->invokeArgs($this->asset, ['/']));
        $this->assertFalse($method->invokeArgs($this->asset, ['/foo']));
        $this->assertFalse($method->invokeArgs($this->asset, ['foo']));
    }

    public function testPackageAssetDetection()
    {
        $vendor = '_This-Is-Vendor.0';
        $name = '_This-Is-Package.9';
        $asset = 'local/asset.css';

        $method = self::getMethod('assetIsFromPackage');
        $package = $method->invokeArgs($this->asset, ["$vendor/$name:$asset"]);

        $this->assertCount(3, $package);
        $this->assertEquals($vendor, $package[0]);
        $this->assertEquals($name, $package[1]);
        $this->assertEquals($asset, $package[2]);

        $this->assertFalse($method->invokeArgs($this->asset, ['foo']));
        $this->assertFalse($method->invokeArgs($this->asset, ['foo/bar']));
        $this->assertFalse($method->invokeArgs($this->asset, ['foo/bar/foo:bar']));
        $this->assertFalse($method->invokeArgs($this->asset, ['foo:bar']));
    }

    public function testAddOneCss()
    {
        $this->assertCount(0, $this->asset->getCss());

        $asset = uniqid('asset');
        $this->asset->addCss($asset);
        $assets = $this->asset->getCss();

        $this->assertCount(1, $assets);
        $this->assertStringEndsWith($asset, array_pop($assets));
        $this->assertCount(0, $assets);
    }

    public function testPrependOneCss()
    {
        $this->assertCount(0, $this->asset->getCss());

        $asset1 = uniqid('asset1');
        $asset2 = uniqid('asset2');
        $this->asset->addCss($asset2);
        $this->asset->prependCss($asset1);

        $assets = $this->asset->getCss();
        $this->assertStringEndsWith($asset2, array_pop($assets));
        $this->assertStringEndsWith($asset1, array_pop($assets));
        $this->assertCount(0, $assets);
    }

    public function testAddOneJs()
    {
        $this->assertCount(0, $this->asset->getJs());

        $asset = uniqid('asset');
        $this->asset->addJs($asset);
        $assets = $this->asset->getJs();

        $this->assertCount(1, $assets);
        $this->assertStringEndsWith($asset, array_pop($assets));
        $this->assertCount(0, $assets);
    }

    public function testPrependOneJs()
    {
        $this->assertCount(0, $this->asset->getJs());

        $asset1 = uniqid('asset1');
        $asset2 = uniqid('asset2');
        $this->asset->addJs($asset2);
        $this->asset->prependJs($asset1);

        $assets = $this->asset->getJs();
        $this->assertStringEndsWith($asset2, array_pop($assets));
        $this->assertStringEndsWith($asset1, array_pop($assets));
        $this->assertCount(0, $assets);
    }

    public function testAddMultipleCss()
    {
        $this->assertCount(0, $this->asset->getCss());

        $asset1 = uniqid('asset1');
        $asset2 = uniqid('asset2');
        $this->asset->addCss([$asset1, $asset2]);
        $assets = $this->asset->getCss();

        $this->assertCount(2, $assets);
        $this->assertStringEndsWith($asset2, array_pop($assets));
        $this->assertStringEndsWith($asset1, array_pop($assets));
        $this->assertCount(0, $assets);
    }

    public function testPrependMultipleCss()
    {
        $this->assertCount(0, $this->asset->getCss());

        $asset1 = uniqid('asset1');
        $asset2 = uniqid('asset2');
        $asset3 = uniqid('asset3');
        $this->asset->addCss($asset3);
        $this->asset->prependCss([$asset1, $asset2]);
        $assets = $this->asset->getCss();

        $this->assertCount(3, $assets);
        $this->assertStringEndsWith($asset3, array_pop($assets));
        $this->assertStringEndsWith($asset2, array_pop($assets));
        $this->assertStringEndsWith($asset1, array_pop($assets));
        $this->assertCount(0, $assets);
    }

    public function testAddMultipleJs()
    {
        $this->assertCount(0, $this->asset->getJs());

        $asset1 = uniqid('asset1');
        $asset2 = uniqid('asset2');
        $this->asset->addJs([$asset1, $asset2]);
        $assets = $this->asset->getJs();

        $this->assertCount(2, $assets);
        $this->assertStringEndsWith($asset2, array_pop($assets));
        $this->assertStringEndsWith($asset1, array_pop($assets));
        $this->assertCount(0, $assets);
    }

    public function testPrependMultipleJs()
    {
        $this->assertCount(0, $this->asset->getJs());

        $asset1 = uniqid('asset1');
        $asset2 = uniqid('asset2');
        $asset3 = uniqid('asset3');
        $this->asset->addJs($asset3);
        $this->asset->prependJs([$asset1, $asset2]);
        $assets = $this->asset->getJs();

        $this->assertCount(3, $assets);
        $this->assertStringEndsWith($asset3, array_pop($assets));
        $this->assertStringEndsWith($asset2, array_pop($assets));
        $this->assertStringEndsWith($asset1, array_pop($assets));
        $this->assertCount(0, $assets);
    }

    public function testDetectAndAddCss()
    {
        $this->assertCount(0, $this->asset->getCss());
        $this->assertCount(0, $this->asset->getJs());

        $asset = 'foo.css';
        $this->asset->addCss($asset);

        $this->assertCount(1, $assets = $this->asset->getCss());
        $this->assertCount(0, $this->asset->getJs());
        $this->assertStringEndsWith($asset, array_pop($assets));
    }

    public function testDetectAndAddJs()
    {
        $this->assertCount(0, $this->asset->getCss());
        $this->assertCount(0, $this->asset->getJs());

        $asset = 'foo.js';
        $this->asset->addJs($asset);

        $this->assertCount(1, $assets = $this->asset->getJs());
        $this->assertCount(0, $this->asset->getCss());
        $this->assertStringEndsWith($asset, array_pop($assets));
    }

    public function testDetectAndAddCollection()
    {
        $asset1 = 'foo.js';
        $asset2 = 'foo.css';
        $collection = [$asset1, $asset2];
        $this->asset->config(['collections' => ['collection' => $collection]]);

        $this->assertCount(0, $this->asset->getCss());
        $this->assertCount(0, $this->asset->getJs());

        $this->asset->add('collection');

        $this->assertCount(1, $assets1 = $this->asset->getJs());
        $this->assertCount(1, $assets2 = $this->asset->getCss());

        $this->assertStringEndsWith($asset1, array_pop($assets1));
        $this->assertStringEndsWith($asset2, array_pop($assets2));
    }

    public function testRegexOptions()
    {
        $files = [
            '.css',        // Not an asset
            'foo.CSS',
            'foomin.css',
            'foo.min.css', // Skip from minification
            'foo-MIN.css', // Skip from minification

            '.js',        // Not an asset
            'foo.JS',
            'foomin.js',
            'foo.min.js', // Skip from minification
            'foo-MIN.js', // Skip from minification
        ];

        // Test asset detection
        $regex = Assert::readAttribute($this->asset, 'assetRegex');
        $matching = array_filter($files, function ($file) use ($regex) {
            return 1 === preg_match($regex, $file);
        });
        $this->assertEquals(8, count($matching));

        // Test CSS asset detection
        $regex = Assert::readAttribute($this->asset, 'cssRegex');
        $matching = array_filter($files, function ($file) use ($regex) {
            return 1 === preg_match($regex, $file);
        });
        $this->assertEquals(4, count($matching));

        // Test JS asset detection
        $regex = Assert::readAttribute($this->asset, 'jsRegex');
        $matching = array_filter($files, function ($file) use ($regex) {
            return 1 === preg_match($regex, $file);
        });
        $this->assertEquals(4, count($matching));

        // Test minification skip detection
        $regex = Assert::readAttribute($this->asset, 'noMinificationRegex');
        $matching = array_filter($files, function ($file) use ($regex) {
            return 1 === preg_match($regex, $file);
        });
        $this->assertEquals(4, count($matching));
    }

    protected static function getMethod($name)
    {
        $class = new ReflectionClass('Qubus\Support\Assets');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }
}
