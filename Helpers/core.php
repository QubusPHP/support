<?php

/**
 * Qubus\Support
 *
 * @link       https://github.com/QubusPHP/support
 * @copyright  2020 Joshua Parker <josh@joshuaparker.blog>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 *
 * @since      1.0.0
 */

declare(strict_types=1);

namespace Qubus\Support\Helpers;

use ArrayAccess;
use Closure;
use Qubus\Exception\Data\TypeException;
use Qubus\Support\DataType;

use function array_key_exists;
use function array_map;
use function array_merge;
use function array_slice;
use function array_unique;
use function array_values;
use function count;
use function ctype_lower;
use function debug_backtrace;
use function define;
use function defined;
use function end;
use function explode;
use function fclose;
use function file_exists;
use function fopen;
use function func_get_args;
use function func_num_args;
use function htmlentities;
use function implode;
use function in_array;
use function is_array;
use function is_bool;
use function is_dir;
use function is_object;
use function is_string;
use function lcfirst;
use function ltrim;
use function mt_rand;
use function next;
use function ord;
use function preg_match;
use function preg_quote;
use function preg_replace;
use function preg_split;
use function print_r;
use function rtrim;
use function sprintf;
use function str_contains;
use function str_replace;
use function str_split;
use function strlen;
use function strtolower;
use function strtoupper;
use function substr;
use function trigger_error;
use function trim;
use function ucwords;
use function uniqid;
use function unlink;

use const DIRECTORY_SEPARATOR;
use const E_USER_DEPRECATED;
use const E_USER_NOTICE;
use const ENT_NOQUOTES;
use const PHP_OS;
use const PREG_SPLIT_NO_EMPTY;

/**
 * Wrapper function for the core PHP function: trigger_error.
 *
 * This function makes the error a little more understandable for the
 * end user to track down the issue.
 *
 * @param string $message Custom message to print.
 * @param int $level Predefined PHP error constant.
 */
function trigger_error__(string $message, int $level = E_USER_NOTICE): void
{
    $debug = debug_backtrace();
    $caller = next($debug);
    echo '<div class="alerts alerts-error center">';
    trigger_error(
        $message . ' used <strong>' . $caller['function'] . '()</strong> called from <strong>'
            . $caller['file'] . '</strong> on line <strong>' . $caller['line'] . '</strong>' . "\n<br />error handler",
        $level
    );
    echo '</div>';
}

/**
 * Returns false.
 *
 * @return bool False.
 */
function return_false__(): bool
{
    return false;
}

/**
 * Returns true.
 *
 * @return bool True.
 */
function return_true__(): bool
{
    return true;
}

/**
 * Returns null.
 *
 * @return null NULL.
 */
function return_null__()
{
    return null;
}

/**
 * Returns zero.
 *
 * @return int Zero.
 */
function return_zero__(): int
{
    return (int) 0;
}

/**
 * Returns an empty array.
 *
 * @return array Empty array.
 */
function return_empty_array__(): array
{
    return [];
}

/**
 * Returns an empty string.
 *
 * @return string Empty string.
 */
function return_empty_string__(): string
{
    return '';
}

/**
 * Returns void.
 *
 * @return void Void.
 */
function return_void__(): void
{
}

/**
 * Special function for file includes.
 *
 * @param string $file File which should be included/required.
 * @param bool $once File should be included/required once. Default true.
 * @param bool|Closure $showErrors If true error will be processed,
 *                                 if Closure - only Closure will be called.
 *                                 Default true.
 */
function load_file(string $file, bool $once = true, bool|Closure $showErrors = true): mixed
{
    if (file_exists("'$file'")) {
        if ($once) {
            return require_once "'$file'";
        } else {
            return require "'$file'";
        }
    } elseif (is_bool($showErrors) && $showErrors) {
        trigger_error__(
            sprintf(
                'Invalid file name: <strong>%s</strong> does not exist. <br />',
                $file
            )
        );
    } elseif ($showErrors instanceof Closure) {
        return (bool) $showErrors();
    }
    return false;
}

/**
 * Appends a trailing slash.
 *
 * Will remove trailing forward and backslashes if it exists already before adding
 * a trailing forward slash. This prevents double slashing a string or path.
 *
 * The primary use of this is for paths and thus should be used for paths. It is
 * not restricted to paths and offers no specific path support.
 *
 * @param string $string What to add the trailing slash to.
 * @return string String with trailing slash added.
 */
function add_trailing_slash(string $string): string
{
    return remove_trailing_slash($string) . '/';
}

/**
 * Removes trailing forward slashes and backslashes if they exist.
 *
 * The primary use of this is for paths and thus should be used for paths. It is
 * not restricted to paths and offers no specific path support.
 *
 * @param string $string What to remove the trailing slashes from.
 * @return string String without the trailing slashes.
 */
function remove_trailing_slash(string $string): string
{
    return rtrim($string, '/\\');
}

/**
 * Split a delimited string into an array.
 *
 * @param array|string $string     String or array to be split.
 * @param array|string $delimiters Delimiter(s) to search for.
 * @return array Return array.
 */
function explode_array(string|array $string, array|string $delimiters = [',']): array
{
    if (! is_array($delimiters) && ! is_array($string)) {
        //if neither the delimiter nor the string are arrays
        return explode($delimiters, $string);
    } elseif (! is_array($delimiters) && is_array($string)) {
        //if the delimiter is not an array but the string is
        foreach ($string as $item) {
            foreach (explode($delimiters, $item) as $subItem) {
                $items[] = $subItem;
            }
        }
        return $items;
    } elseif (is_array($delimiters) && ! is_array($string)) {
        //if the delimiter is an array but the string is not
        $stringArray[] = $string;
        foreach ($delimiters as $delimiter) {
            $stringArray = explode_array($delimiter, $stringArray);
        }
        return $stringArray;
    }
}

/**
 * Concatenation with separator.
 *
 * @param string $string1    Left string.
 * @param string $string2    Right string.
 * @param string $separator  Delimiter to use between strings. Default: comma.
 * @param string ...$strings List of strings.
 * @return string Concatenated string.
 */
function concat_ws(string $string1, string $string2, string $separator = ',', ...$strings): string
{
    $string = $string1 . $separator . $string2;

    if (func_num_args() > 3) {
        $stringList = '';
        $argList = array_slice(func_get_args(), 3);
        $argCount = count($argList);
        for ($i = 0; $i < $argCount; $i++) {
            if (null === $argList[$i]) {
                continue;
            }
            $stringList .= $separator . $argList[$i];
        }
        return $string . $stringList;
    }

    return $string;
}

/**
 * Checks if a variable is null.
 *
 * Works the same as PHP's native `is_null()` function.
 * If $var is not set, an `Undefined variable` notice
 * will be thrown.
 *
 * @param mixed $var Variable to check.
 * @return bool Returns `true` if null, `false` otherwise.
 */
function is_null__(mixed $var): bool
{
    if (null === $var) {
        return true;
    }

    return false;
}

/**
 * Checks if a variable is true.
 *
 * @param mixed $var Variable to check.
 * @return bool Returns `true` if true, `false` otherwise.
 */
function is_true__(mixed $var): bool
{
    if (true === $var) {
        return true;
    }

    return false;
}

/**
 * Checks if a variable is false.
 *
 * @param mixed $var Variable to check.
 * @return bool Returns `true` if false, `false` otherwise.
 */
function is_false__(mixed $var): bool
{
    if (false === $var) {
        return true;
    }

    return false;
}

/**
 * Truncates a string to the given length. It will optionally preserve
 * HTML tags if $isHtml is set to true.
 *
 * @param string  $string        The string to truncate.
 * @param int     $limit         The number of characters to truncate too.
 * @param string  $continuation  The string to use to denote it was truncated.
 * @param bool    $isHtml        Whether the string has HTML.
 * @return string The truncated string.
 */
function truncate_string(string $string, int $limit, string $continuation = '...', bool $isHtml = false): string
{
    return (new DataType())->string->truncate($string, $limit, $continuation, $isHtml);
}

/**
 * Converts a string into unicode values.
 */
function unicoder(string $string): string
{
    $p = str_split(trim($string));
    $newString = '';
    foreach ($p as $val) {
        $newString .= '&#' . ord($val) . ';';
    }
    return $newString;
}

/**
 * Strips out all duplicate values and compact the array.
 *
 * @param array $a An array that be compacted.
 * @return array
 */
function compact_unique_array(array $a): array
{
    $tmparr = array_unique($a);
    $i = 0;
    foreach ($tmparr as $v) {
        $newarr[$i] = $v;
        $i++;
    }
    return $newarr;
}

/**
 * Takes an array and turns it into an object.
 *
 * @param array $array Array of data.
 */
function convert_array_to_object(array $array): object
{
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $array[$key] = convert_array_to_object($value);
        }
    }
    return (object) $array;
}

/**
 * SQL Like operator in PHP.
 *
 * Returns `true` if match else `false`.
 *
 * Example Usage:
 *
 *      php_like('%uc%','Lucy'); //true
 *      php_like('%cy', 'Lucy'); //true
 *      php_like('lu%', 'Lucy'); //true
 *      php_like('%lu', 'Lucy'); //false
 *      php_like('cy%', 'Lucy'); //false
 */
function php_like(string $pattern, string $subject): bool
{
    $match = str_replace('%', '.*', preg_quote($pattern, '/'));
    return (bool) preg_match("/^{$match}$/i", $subject);
}

/**
 * SQL Where operator in PHP.
 *
 * @param string|array $pattern
 * @throws TypeException
 */
function php_where(string $key, string $operator, $pattern): bool
{
    switch ($operator) {
        case '=':
            $filter = $key === $pattern;
            break;
        case '>':
            $filter = $key > $pattern;
            break;
        case '>=':
            $filter = $key >= $pattern;
            break;
        case '<':
            $filter = $key < $pattern;
            break;
        case '<=':
            $filter = $key <= $pattern;
            break;
        case 'in':
            $filter = in_array($key, (array) $pattern);
            break;
        case 'not in':
            $filter = ! in_array($key, (array) $pattern);
            break;
        case 'match':
            $filter = (bool) preg_match($pattern, $key);
            break;
        case 'between':
            if (! is_array($pattern) || count($pattern) < 2) {
                throw new TypeException("Query 'between' needs exactly 2 items in array.");
            }
            $filter = $key >= $pattern[0] && $key <= $pattern[1];
            break;
    }
    return $filter;
}

/**
 * Sorts a structured array by 'Name' property.
 *
 * @param array $a First item for comparison. The compared items should be
 *                 associative arrays that optionally include a 'Name' key.
 * @param array $b Second item for comparison.
 * @return int Return 0, -1, or 1 based on two string comparison.
 */
function sort_element_callback(array $a, array $b): int
{
    $aname = $a['Name'] ?? 0;
    $bname = $b['Name'] ?? 0;
    if ($aname === $bname) {
        return 0;
    }
    return $aname < $bname ? -1 : 1;
}

/**
 * Return array specific item.
 *
 * @param array $array
 * @param string|null $key
 * @param mixed|null $default
 * @return array|null
 */
function return_array(array $array, ?string $key, mixed $default = null): ?array
{
    if (! array_accessible($array)) {
        return value($default);
    }

    if (null === $key) {
        return $array;
    }

    if (array_key_exists__($key, $array)) {
        return $array[$key];
    }

    foreach (explode('.', $key) as $segment) {
        if (array_accessible($array) && array_key_exists__($segment, $array)) {
            $array = $array[$segment];
        } else {
            return value($default);
        }
    }

    return $array;
}

/**
 * Flatten a multi-dimensional associative array with dots.
 *
 * @param array $array
 * @param string $prepend
 * @return array
 */
function array_dot(array $array, string $prepend = ''): array
{
    $results = [];

    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $results = array_merge($results, array_dot($value, $prepend . $key . '.'));
        } else {
            $results[$prepend . $key] = $value;
        }
    }

    return $results;
}

/**
 * Check input is array accessible.
 */
function array_accessible(mixed $value): bool
{
    return is_array($value) || $value instanceof ArrayAccess;
}

/**
 * Checks if the given key or index exists in the array.
 *
 * @param array $array An array with keys to check.
 * @param string $key Value to check.
 */
function array_exists(array $array, string $key): bool
{
    trigger_deprecation(
        functionName: __FUNCTION__,
        deprecatedVersion: '1.0',
        removedVersion: '2.0',
        replacement: __NAMESPACE__ . '\\' . 'array_key_exists__'
    );

    return array_key_exists__($key, $array);
}

/**
 * Checks if the given key or index exists in the array.
 *
 * @param string $key Value to check.
 * @param array|ArrayAccess $array $array An array with keys to check.
 */
function array_key_exists__(string $key, array|ArrayAccess $array): bool
{
    if ($array instanceof ArrayAccess) {
        return $array->offsetExists($key);
    }

    return array_key_exists($key, $array);
}

/**
 * Convert a string to snake case.
 */
function snake_case(string $string, string $delimiter = '_'): string
{
    $replace = '$1' . $delimiter . '$2';

    return ctype_lower($string) ? $string : strtolower(preg_replace('/(.)([A-Z])/', $replace, $string));
}

/**
 * Convert a value to studly caps case.
 */
function studly_case(string $string): string
{
    $string = ucwords(str_replace(['-', '_'], ' ', $string));

    return str_replace(' ', '', $string);
}

/**
 * Convert a value to camel caps case.
 *
 * @param array $noStrip
 */
function camel_case(string $str, array $noStrip = []): string
{
    // non-alpha and non-numeric characters become spaces
    $str = preg_replace('/[^a-z0-9' . implode("", $noStrip) . ']+/i', ' ', $str);
    $str = trim($str);
    // uppercase the first character of each word
    $str = ucwords($str);
    $str = str_replace(" ", "", $str);
    return lcfirst($str);
}

/**
 * Return the default value of the given value.
 */
function value(mixed $value): mixed
{
    return $value instanceof Closure ? $value() : $value;
}

/**
 * Converts all accented characters to ASCII characters.
 *
 * If there are no accent characters, then the string given is just returned.
 *
 * @param string $string   Text that might have accented characters.
 * @param string $encoding Encoding used. Default: utf-8.
 * @return string Filtered string with replaced "nice" characters.
 */
function remove_accents(string $string, string $encoding = 'utf-8'): string
{
    // converting accents in HTML entities
    $string = htmlentities($string, ENT_NOQUOTES, $encoding);

    // replacing the HTML entities to extract the first letter
    // examples: "&ecute;" => "e", "&Ecute;" => "E", "à" => "a" ...
    $string = preg_replace(
        '#&([A-za-z])(?:acute|grave|cedil|circ|orn|ring|slash|th|tilde|uml);#',
        '\1',
        $string
    );

    // replacing ligatures
    // Example "œ" => "oe", "Æ" => "AE"
    $string = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $string);

    // removing the remaining bits
    return preg_replace('#&[^;]+;#', '', $string);
}

/**
 * Alternative to call_user_func_array().
 *
 * @param string|array $callback
 */
function call_qubus_func_array($callback, array $args)
{
    // deal with "class::method" syntax
    if (is_string($callback) && str_contains($callback, '::') !== false) {
        $callback = explode('::', $callback);
    }

    // dynamic call on an object?
    if (is_array($callback) && isset($callback[1]) && is_object($callback[0])) {
        // make sure our arguments array is indexed
        if ($count = count($args)) {
            $args = array_values($args);
        }

        [$instance, $method] = $callback;

        return $instance->{$method}(...$args);
    } elseif (is_array($callback) && isset($callback[1]) && is_string($callback[0])) { // static call?
        [$class, $method] = $callback;
        $class = '\\' . ltrim($class, '\\');

        return $class::{$method}(...$args);
    } elseif (is_string($callback) || $callback instanceof Closure) {
        is_string($callback) && $callback = ltrim($callback, '\\');
    }

    return $callback(...$args);
}

/**
 * Determine whether the current environment is Windows based.
 */
function windows_os(): bool
{
    return 'WIN' === strtoupper(substr(PHP_OS, 0, 3));
}

/**
 * Print and die.
 *
 * @param bool $pre Default true.
 * @param bool $return Default false.
 */
function pd(mixed $x, bool $pre = true, bool $return = false): never
{
    if ($pre) {
        echo '<pre>';
        print_r($x, $return);
        echo '</pre>';
    }

    if (! $pre) {
        print_r($x, $return);
    }

    die(1);
}

/**
 * Single file writable attribute check.
 * Thanks to legolas558.users.sf.net
 */
function win_is_writable(string $path): bool
{
    // will work in despite of Windows ACLs bug
    // NOTE: use a trailing slash for folders!!!
    // see http://bugs.php.net/bug.php?id=27609
    // see http://bugs.php.net/bug.php?id=30931

    $randString = (string) mt_rand();

    if ($path[strlen($path) - 1] === '/') { // recursively return a temporary file path
        return win_is_writable($path . uniqid($randString) . '.tmp');
    } elseif (is_dir($path)) {
        return win_is_writable($path . DIRECTORY_SEPARATOR . uniqid($randString) . '.tmp');
    }
    // check tmp file for read/write capabilities
    $rm = file_exists($path);
    $f = fopen($path, 'a');
    if ($f === false) {
        return false;
    }
    fclose($f);
    if (! $rm) {
        unlink($path);
    }
    return true;
}

/**
 * Alternative to PHP's native is_writable function due to a Window's bug.
 *
 * @param string $path Path to check.
 */
function is_writable(string $path): bool
{
    if (windows_os()) {
        return win_is_writable($path);
    } else {
        return is_writable($path);
    }
}

/**
 * Used to trigger code deprecation warnings.
 *
 * @param string $functionName Name of function that is deprecated.
 * @param string $deprecatedVersion Version for which code becomes deprecated.
 * @param string $removedVersion Version for when deprecated code will be removed.
 * @param string|null $replacement Replacement of deprecated code if any.
 */
function trigger_deprecation(
    string $functionName,
    string $deprecatedVersion,
    string $removedVersion,
    ?string $replacement = null
): bool {
    if (! defined('QUBUS_ENVIRONMENT')) {
        define('QUBUS_ENVIRONMENT', 'production');
    }

    if (QUBUS_ENVIRONMENT === 'development') {
        if (! is_null__($replacement)) {
            trigger_error__(
                sprintf(
                    '%1$s() is <strong>deprecated</strong> since version %2$s and will be removed in version %3$s. 
                    Use %4$s() instead. <br />',
                    $functionName,
                    $deprecatedVersion,
                    $removedVersion,
                    $replacement
                ),
                E_USER_DEPRECATED
            );
        } else {
            trigger_error__(
                sprintf(
                    '%1$s() is <strong>deprecated</strong> since version %2$s and will be removed in version %3$s. 
                    No alternative is available.<br />',
                    $functionName,
                    $deprecatedVersion,
                    $removedVersion
                ),
                E_USER_DEPRECATED
            );
        }
        return true;
    }

    return false;
}

/**
 * Convert class name to a delimited string.
 *
 * @since 2.1.7
 */
function classname_to_delimited_string(
    object|string $className,
    callable|string|null $callback = 'strtolower',
    string $delimiter = '-'
): string {
    // Remove namespace from class if present.
    $explode = explode('\\', $className);
    $classNameWithoutNamespace = end($explode);
    // Split the class name into parts and remove any empty array values.
    $parts = preg_split('/(?=[A-Z])/', $classNameWithoutNamespace, -1, PREG_SPLIT_NO_EMPTY);
    // Convert each array value to lowercase.
    $array = array_map($callback, $parts);
    // Convert the array into a string with spaces in between them.
    $implode = implode(' ', $array);

    // Replace the spaces with the delimiter and give back the delimited string.
    return str_replace(' ', $delimiter, $implode);
}
