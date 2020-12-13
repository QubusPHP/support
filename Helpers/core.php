<?php

/**
 * Qubus\Support
 *
 * @link       https://github.com/QubusPHP/support
 * @copyright  2020 Joshua Parker
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 *
 * @since      1.0.0
 */

declare(strict_types=1);

namespace Qubus\Support\Helpers;

use ArrayAccess;
use Closure;
use Qubus\Exception\Data\TypeException;

/**
 * Wrapper function for the core PHP function: trigger_error.
 *
 * This function makes the error a little more understandable for the
 * end user to track down the issue.
 *
 * @param string $message Custom message to print.
 * @param string $level Predefined PHP error constant.
 */
function trigger_error__(string $message, string $level = E_USER_NOTICE)
{
    $debug = debug_backtrace();
    $caller = next($debug);
    echo '<div class="alerts alerts-error center">';
    trigger_error(
        $message . ' used <strong>' . $caller['function'] . '()</strong> called from <strong>' .
    $caller['file'] . '</strong> on line <strong>' . $caller['line'] . '</strong>' . "\n<br />error handler",
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
function return_empty_array__()
{
    return [];
}

/**
 * Returns an empty string.
 *
 * @return string Empty string.
 */
function return_empty_string__()
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
    //
}

/**
 * Special function for file includes.
 *
 * @param string $file File which should be included/required.
 * @param bool $once File should be included/required once. Default true.
 * @param bool|Closure $showErrors If true error will be processed,
 *                                 if Closure - only Closure will be called.
 *                                 Default true.
 * @return mixed
 */
function load_file(string $file, bool $once = true, $showErrors = true)
{
    if (file_exists("'$file'")) {
        if ($once) {
            return require_once("'$file'");
        } else {
            return require("'$file'");
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
 * @param array|string $delimiters Delimeter(s) to search for.
 * @param array|string $string     String or array to be split.
 * @return array Return array.
 */
function explode_array($delimiters, $string): array
{
    if (!is_array(($delimiters)) && !is_array($string)) {
        //if neither the delimiter nor the string are arrays
        return explode($delimiters, $string);
    } elseif (!is_array($delimiters) && is_array($string)) {
        //if the delimiter is not an array but the string is
        foreach ($string as $item) {
            foreach (explode($delimiters, $item) as $subItem) {
                $items[] = $subItem;
            }
        }
        return $items;
    } elseif (is_array($delimiters) && !is_array($string)) {
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
 * @param string $separator Delimeter to use between strings. Default: comma.
 * @param string $string1 Left string.
 * @param string $string2 Right string.
 * @return string Concatenated string.
 */
function concat_ws(?string $separator = null, string $string1, string $string2): string
{
    if (null === $separator) {
        $separator = ',';
    }
    return $string1 . $separator . $string2;
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
function is_null__($var): bool
{
    if (null === $var) {
        return true;
    }

    return false;
}

/**
 * Truncate a string to a specified length without cutting a word off
 *
 * @param   string  $string  The string to truncate
 * @param   int     $length  The length to truncate the string to
 * @param   string  $append  Text to append to the string IF it gets
 *                           truncated, defaults to '...'
 * @return  string Truncated string.
 */
function truncate_string(string $string, int $length, string $append = '...'): string
{
    $ret = substr($string, 0, $length);
    $lastSpace = strrpos($ret, ' ');

    if ($lastSpace !== false && $string != $ret) {
        $ret = substr($ret, 0, $lastSpace);
    }

    if ($ret != $string) {
        $ret .= $append;
    }

    return $ret;
}

/**
 * Converts a string into unicode values.
 *
 * @param string $string
 * @return mixed
 */
function unicoder($string)
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
 * @param mixed $a An array that be compacted.
 * @return mixed
 */
function compact_unique_array($a)
{
    $tmparr = array_unique($a);
    $i = 0;
    foreach ($tmparr as $v) {
        $newarr[$i] = $v;
        $i ++;
    }
    return $newarr;
}

/**
 * Takes an array and turns it into an object.
 *
 * @param array $array Array of data.
 */
function convert_array_to_object(array $array)
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
 *
 * @param string $pattern
 * @param string $subject
 * @return bool
 */
function php_like($pattern, $subject): bool
{
    $match = str_replace('%', '.*', preg_quote($pattern, '/'));
    return (bool) preg_match("/^{$match}$/i", $subject);
}

/**
 * SQL Where operator in PHP.
 *
 * @param string $key
 * @param string $operator
 * @param type $pattern
 * @return bool
 * @throws \Qubus\Exception\Data\TypeException
 */
function php_where(string $key, string $operator, $pattern): bool
{
    switch ($operator) {
    case '=':
        $filter = $key == $pattern;
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
        $filter = !in_array($key, (array) $pattern);
        break;
    case 'match':
        $filter = (bool) preg_match($pattern, $key);
        break;
    case 'between':
        if (!is_array($pattern) || count($pattern) < 2) {
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
function sort_element_callback(array $a, array $b)
{
    $a_name = (is_array($a) && isset($a['Name'])) ? $a['Name'] : 0;
    $b_name = (is_array($b) && isset($b['Name'])) ? $b['Name'] : 0;
    if ($a_name == $b_name) {
        return 0;
    }
    return ($a_name < $b_name) ? -1 : 1;
}

/**
 * Return array specific item.
 *
 * @param array  $array
 * @param string $key
 * @param mixed  $default
 *
 * @return mixed
 */
function return_array(array $array, ?string $key, $default = null)
{
    if (!array_accessible($array)) {
        return value($default);
    }

    if (null === $key) {
        return $array;
    }

    if (array_exists($array, $key)) {
        return $array[$key];
    }

    foreach (explode('.', $key) as $segment) {
        if (array_accessible($array) && array_exists($array, $segment)) {
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
 * @param  array   $array
 * @param  string  $prepend
 * @return array
 */
function array_dot(array $array, string $prepend = ''): array
{
    $results = [];

    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $results = array_merge($results, array_dot($value, $prepend.$key.'.'));
        } else {
            $results[$prepend.$key] = $value;
        }
    }

    return $results;
}

/**
 * Check input is array accessable.
 *
 * @param mixed $value
 * @return bool
 */
function array_accessible($value): bool
{
    return is_array($value) || $value instanceof ArrayAccess;
}

/**
 * Check array key exists.
 *
 * @param array  $array
 * @param string $key
 * @return bool
 */
function array_exists(array $array, string $key): bool
{
    if ($array instanceof ArrayAccess) {
        return $array->offsetExists($key);
    }

    return array_key_exists($key, $array);
}

/**
 * Convert a string to snake case.
 *
 * @param string $string
 * @param string $delimiter
 * @return string
 */
function snake_case(string $string, string $delimiter = '_'): string
{
    $replace = '$1'.$delimiter.'$2';

    return ctype_lower($string) ? $string : strtolower(preg_replace('/(.)([A-Z])/', $replace, $string));
}

/**
 * Convert a value to studly caps case.
 *
 * @param string $string
 * @return string
 */
function studly_case(string $string): string
{
    $string = ucwords(str_replace(['-', '_'], ' ', $string));

    return str_replace(' ', '', $string);
}

/**
 * Convert a value to camel caps case.
 *
 * @param string $str
 * @param array $noStrip
 * @return string
 */
function camel_case(string $str, array $noStrip = [])
{
    // non-alpha and non-numeric characters become spaces
    $str = preg_replace('/[^a-z0-9' . implode("", $noStrip) . ']+/i', ' ', $str);
    $str = trim($str);
    // uppercase the first character of each word
    $str = ucwords($str);
    $str = str_replace(" ", "", $str);
    $str = lcfirst($str);

    return $str;
}

/**
 * Return the default value of the given value.
 *
 * @param  mixed  $value
 * @return mixed
 */
function value($value)
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
function remove_accents(string $string, string $encoding = 'utf-8')
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
    // Exemple "œ" => "oe", "Æ" => "AE"
    $string = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $string);

    // removing the remaining bits
    $string = preg_replace('#&[^;]+;#', '', $string);

    return $string;
}
