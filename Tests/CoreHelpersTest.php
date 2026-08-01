<?php

declare(strict_types=1);

namespace Qubus\Tests\Support;

use ArrayObject;
use PHPUnit\Framework\TestCase;
use Qubus\Exception\IO\FileSystem\FileNotFoundException;
use stdClass;

use function Qubus\Support\Helpers\array_key_exists__;
use function Qubus\Support\Helpers\camel_case;
use function Qubus\Support\Helpers\classname_to_delimited_string;
use function Qubus\Support\Helpers\load_file;
use function Qubus\Support\Helpers\php_where;
use function Qubus\Support\Helpers\remove_accents;
use function Qubus\Support\Helpers\unicoder;
use function Qubus\Support\Helpers\win_is_writable;
use function file_put_contents;
use function mkdir;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

final class CoreHelpersTest extends TestCase
{
    public function testLoadFileReportsSuccessAndHonorsOnceFlag(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'qubus-core-');
        self::assertIsString($file);
        file_put_contents(
            $file,
            '<?php $GLOBALS[\'qubus_core_load_count\'] = ($GLOBALS[\'qubus_core_load_count\'] ?? 0) + 1;'
        );
        $GLOBALS['qubus_core_load_count'] = 0;

        try {
            self::assertTrue(load_file($file));
            self::assertTrue(load_file($file));
            self::assertSame(1, $GLOBALS['qubus_core_load_count']);

            self::assertTrue(load_file($file, false));
            self::assertSame(2, $GLOBALS['qubus_core_load_count']);
        } finally {
            unset($GLOBALS['qubus_core_load_count']);
            unlink($file);
        }
    }

    public function testLoadFileRejectsDirectoriesAndEscapesErrorPath(): void
    {
        $base = tempnam(sys_get_temp_dir(), 'qubus-core-');
        self::assertIsString($base);
        unlink($base);
        $directory = $base . '-<unsafe>';
        mkdir($directory);

        try {
            load_file($directory);
            self::fail('A directory was accepted as an includable file.');
        } catch (FileNotFoundException $exception) {
            self::assertStringNotContainsString('<unsafe>', $exception->getMessage());
            self::assertStringContainsString('&lt;unsafe&gt;', $exception->getMessage());
        } finally {
            @rmdir($directory);
        }
    }

    public function testUnicoderUsesUnicodeCodePointsRatherThanBytes(): void
    {
        self::assertSame('&#65;&#233;&#128512;', unicoder(' Aé😀 '));
    }

    public function testCamelCaseQuotesAllowedRegexCharacters(): void
    {
        self::assertSame('foo[bar', camel_case('foo[bar', ['[']));
        self::assertSame('fooBar', camel_case('foo[bar', []));
    }

    public function testArrayKeyExistsSupportsIntegerKeysAndNullValues(): void
    {
        self::assertTrue(array_key_exists__(0, [null]));
        self::assertTrue(array_key_exists__(0, new ArrayObject([null])));
    }

    public function testClassnameHelperAcceptsObjectsAsDeclared(): void
    {
        self::assertSame('std-class', classname_to_delimited_string(new stdClass()));
    }

    public function testInvalidMatchPatternFailsWithoutLeakingARegexWarning(): void
    {
        self::assertFalse(php_where('value', 'match', '/[/'));
    }

    public function testRemoveAccentsHandlesAccentsAndLigatures(): void
    {
        self::assertSame('Creme brulee oeuvre AEther', remove_accents('Crème brûlée œuvre Æther'));
    }

    public function testWindowsWritableProbeHandlesAnEmptyPath(): void
    {
        self::assertFalse(win_is_writable(''));
    }
}
