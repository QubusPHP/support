<?php

/**
 * Qubus\Support
 *
 * @link       https://github.com/QubusPHP/support
 * @copyright  2022 Joshua Parker <josh@joshuaparker.blog>
 * @copyright  2014 Mathias Verraes
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 *
 * @since      2.0.0
 */

declare(strict_types=1);

namespace Qubus\Support\Helpers;

use Closure;
use Exception;
use Mockery;

use function array_reduce;
use function debug_backtrace;
use function fwrite;
use function is_callable;
use function register_shutdown_function;

use const STDERR;
use const STDOUT;

/**
 * Example usage:
 *
 *      it("should sum two numbers", 1+1==2);
 *      it("should display an red X for a failing test", 1+1==3);
 *
 * @param string $m Message.
 * @param mixed $p Callable.
 */
function it(string $m, mixed $p): void
{
    global $e;

    $d = debug_backtrace(0)[0];
    is_callable($p) && $p = $p();
    $e = $e || ! $p;
    $o = "\e[3" . ($p ? "2m✔" : "1m✘") . "\e[36m It $m";
    fwrite($p ? STDOUT : STDERR, $p ? "$o\n" : "$o \e[1;37;41mFAIL: {$d['file']} #" . $d['line'] . "\e[0m\n");
}

register_shutdown_function(function () {
    global $e;

    $e && die(1);
});

/**
 * Example usage:
 *
 *      it("should do a bunch of calculations", all([
 *          1+1 == 2,
 *          1+2 == 1249
 *      ]);
 *
 * @param array $ps Array of callables.
 */
function all(array $ps): bool
{
    return array_reduce($ps, function ($a, $p) {
        return $a && $p;
    }, true);
}

/**
 * Example usage:
 *
 *      it("should pass when the expected exception is thrown",
 *          throws("InvalidArgumentException", function () {
 *              throw new InvalidArgumentException;
 *      }));
 *
 * @param string $exp Exception to check for.
 * @param Closure $cb
 * @return bool
 */
function throws($exp, Closure $cb): bool
{
    try {
        $cb();
    } catch (Exception $e) {
        return $e instanceof $exp;
    }
    return false;
}

/**
 * Example usage:
 *
 *      it('should use SomeInterface to do Something', withMock(function () {
 *          $mock = Mockery::mock('SomeInterface');
 *          $mock->shouldReceive('someMethod')
 *              ->with('someValue')
 *              ->once()
 *              ->andReturn(true);
 *
 *          $sut = new SystemToTest($mock);
 *          $sut->test();
 *      }));
 *
 * @param Closure $cb
 * @return bool
 */
function withMock(Closure $cb): bool
{
    $cb();
    try {
        Mockery::close();
    } catch (Exception $e) {
        echo $e->getMessage() . "\n";
        return false;
    }
    return true;
}
