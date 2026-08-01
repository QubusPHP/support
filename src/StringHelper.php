<?php

/**
 * Qubus\Support
 *
 * @link       https://github.com/QubusPHP/support
 * @copyright  2022
 * @author     Joshua Parker <joshua@joshuaparker.dev>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 */

declare(strict_types=1);

namespace Qubus\Support;

use BadMethodCallException;
use Closure;
use Qubus\Exception\Exception;
use Random\RandomException;

use function array_pop;
use function array_reverse;
use function bin2hex;
use function count;
use function defined;
use function end;
use function func_get_args;
use function implode;
use function in_array;
use function libxml_clear_errors;
use function libxml_use_internal_errors;
use function mb_convert_case;
use function mb_stripos;
use function mb_stristr;
use function mb_strlen;
use function mb_strpos;
use function mb_strrchr;
use function mb_strripos;
use function mb_strrpos;
use function mb_strstr;
use function mb_strtolower;
use function mb_strtoupper;
use function mb_substr;
use function mb_substr_count;
use function min;
use function preg_match;
use function preg_match_all;
use function preg_quote;
use function random_bytes;
use function random_int;
use function simplexml_load_string;
use function sprintf;
use function strip_tags;
use function strlen;
use function strpos;
use function strtok;
use function strtr;
use function unserialize;

use const LIBXML_NONET;
use const MB_CASE_TITLE;
use const PHP_INT_MAX;
use const PREG_OFFSET_CAPTURE;
use const PREG_SET_ORDER;

class StringHelper
{
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
    public function truncate(string $string, int $limit, string $continuation = '...', bool $isHtml = false): string
    {
        static $selfClosingTags = [
            'area',
            'base',
            'br',
            'col',
            'command',
            'embed',
            'hr',
            'img',
            'input',
            'keygen',
            'link',
            'meta',
            'param',
            'source',
            'track',
            'wbr',
        ];

        $offset = 0;
        $tags = [];

        if ($isHtml) {
            // Handle special characters.
            preg_match_all('/&[a-z]+;/i', strip_tags($string), $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
            // fix preg_match_all broken multibyte support
            if (strlen($string) !== mb_strlen($string)) {
                $correction = 0;
                foreach ($matches as $index => $match) {
                    $matches[$index][0][1] -= $correction;
                    $correction += strlen($match[0][0]) - mb_strlen($match[0][0]);
                }
            }

            foreach ($matches as $match) {
                if ($match[0][1] >= $limit) {
                    break;
                }
                $limit += $this->strlen($match[0][0]) - 1;
            }

            // Handle all the html tags.
            preg_match_all('/<[^>]+>([^<]*)/', $string, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
            // fix preg_match_all broken multibyte support
            if (strlen($string) !== mb_strlen($string)) {
                $correction = 0;
                foreach ($matches as $index => $match) {
                    $matches[$index][0][1] -= $correction;
                    $matches[$index][1][1] -= $correction;
                    $correction += strlen($match[0][0]) - mb_strlen($match[0][0]);
                }
            }

            foreach ($matches as $match) {
                if ($match[0][1] - $offset >= $limit) {
                    break;
                }

                $tag = $this->substr(strtok($match[0][0], " \t\n\r\0\x0B>"), 1);
                if ($tag[0] !== '/') {
                    if (! in_array($tag, $selfClosingTags)) {
                        $tags[] = $tag;
                    }
                } elseif (end($tags) === $this->substr($tag, 1)) {
                    array_pop($tags);
                }
                $offset += $match[1][1] - $match[0][1];
            }
        }

        $newString = $this->substr($string, 0, $limit = min($this->strlen($string), $limit + $offset));
        $newString .= $this->strlen($string) > $limit ? $continuation : '';
        $newString .= count($tags = array_reverse($tags)) ? '</' . implode('></', $tags) . '>' : '';
        return $newString;
    }

    /**
     * Adds _1 to a string or increment the ending number to allow _2, _3, etc
     *
     * @param string  $str       String to increment.
     * @param int     $first     Number that is used to mean first.
     * @param string  $separator Separator between the name and the number.
     */
    public function increment(string $str, int $first = 1, string $separator = '_'): string
    {
        preg_match('/(.+)' . preg_quote($separator, '/') . '([0-9]+)$/', $str, $match);

        return isset($match[2]) ? $match[1] . $separator . ((int) $match[2] + 1) : $str . $separator . $first;
    }

    /**
     * Checks whether a string has a specific beginning.
     *
     * @param string $str        String to check.
     * @param string $start      Beginning to check for.
     * @param bool   $ignoreCase Whether to ignore the case.
     * @return bool whether a string starts with a specified beginning.
     */
    public function startsWith(string $str, string $start, bool $ignoreCase = false): bool
    {
        if ($ignoreCase) {
            return mb_stripos($str, $start, 0, 'UTF-8') === 0;
        }

        return str_starts_with($str, $start);
    }

    /**
     * Checks whether a string has a specific ending.
     *
     * @param string $str        String to check.
     * @param string $end        Ending to check for.
     * @param bool   $ignoreCase Whether to ignore the case.
     * @return bool Whether a string ends with a specified ending.
     */
    public function endsWith(string $str, string $end, bool $ignoreCase = false): bool
    {
        if ($ignoreCase) {
            $endLength = mb_strlen($end, 'UTF-8');
            $stringEnd = mb_substr($str, -$endLength, encoding: 'UTF-8');

            return $endLength === 0 || mb_strtolower($stringEnd, 'UTF-8') === mb_strtolower($end, 'UTF-8');
        }

        return str_ends_with($str, $end);
    }

    /**
     * Creates a random string of characters
     *
     * @param string $type The type of string.
     * @param int $length The number of characters.
     * @return string|int|false The random string.
     * @throws RandomException
     */
    public function random(string $type = 'alnum', int $length = 16): string|int|false
    {
        switch ($type) {
            case 'basic':
                return random_int(0, PHP_INT_MAX);

            default:
            case 'alnum':
            case 'numeric':
            case 'nozero':
            case 'alpha':
            case 'distinct':
            case 'hexdec':
                switch ($type) {
                    case 'alpha':
                        $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        break;

                    default:
                    case 'alnum':
                        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        break;

                    case 'numeric':
                        $pool = '0123456789';
                        break;

                    case 'nozero':
                        $pool = '123456789';
                        break;

                    case 'distinct':
                        $pool = '2345679ACDEFHJKLMNPRSTUVWXYZ';
                        break;

                    case 'hexdec':
                        $pool = '0123456789abcdef';
                        break;
                }

                $str = '';
                for ($i = 0; $i < $length; $i++) {
                    $str .= $pool[random_int(0, strlen($pool) - 1)];
                }
                return $str;

            case 'unique':
                return bin2hex(random_bytes(16));

            case 'sha1':
                return bin2hex(random_bytes(20));

            case 'sha256':
                return bin2hex(random_bytes(32));

            case 'sha512':
                return bin2hex(random_bytes(64));

            case 'uuid':
                $pool = ['8', '9', 'a', 'b'];
                return sprintf(
                    '%s-%s-4%s-%s%s-%s',
                    $this->random('hexdec', 8),
                    $this->random('hexdec', 4),
                    $this->random('hexdec', 3),
                    $pool[random_int(0, count($pool) - 1)],
                    $this->random('hexdec', 3),
                    $this->random('hexdec', 12)
                );
        }
    }

    /**
     * Replace the first occurrence of a given value in the string.
     *
     * @param string $search
     * @param string $replace
     * @param string $subject
     * @return string
     */
    public function replaceFirst(string $search, string $replace, string $subject): string
    {
        if ($search === '') {
            return $subject;
        }

        $position = strpos($subject, $search);

        if ($position !== false) {
            return substr_replace($subject, $replace, $position, strlen($search));
        }

        return $subject;
    }

    /**
     * Returns a closure that will alternate between the args which to return.
     * If you call the closure with false as the arg it will return the value without
     * alternating the next time.
     *
     * @return Closure
     */
    public function alternator(): Closure
    {
        // the args are the values to alternate
        $args = func_get_args();
        if ($args === []) {
            throw new BadMethodCallException('StringHelper::alternator() requires at least one value.');
        }

        return function ($next = true) use ($args) {
            static $i = 0;
            return $args[($next ? $i++ : $i) % count($args)];
        };
    }

    /**
     * Parse the params from a string using strtr().
     *
     * @param string $string String to parse.
     * @param array<string>  $array  Params to str_replace.
     */
    public function tr(string $string, array $array = []): string
    {
        $trArr = [];

        foreach ($array as $from => $to) {
            !str_starts_with($from, ':') && $from = ':' . $from;
            $trArr[$from] = $to;
        }
        unset($array);

        return strtr($string, $trArr);
    }

    /**
     * Check if a string is json encoded.
     *
     * @param string $string String to check.
     */
    public function isJson(string $string): bool
    {
        return json_validate($string);
    }

    /**
     * Check if a string is a valid XML.
     *
     * @param string $string String to check.
     * @throws Exception
     */
    public function isXml(string $string): bool
    {
        if (! defined('LIBXML_COMPACT')) {
            throw new Exception('libxml is required to use StringHelper::isXml()');
        }

        $internalErrors = libxml_use_internal_errors();
        libxml_use_internal_errors(true);
        try {
            $result = simplexml_load_string($string, options: LIBXML_NONET) !== false;
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($internalErrors);
        }

        return $result;
    }

    /**
     * Check if a string is PHP serialized.
     *
     * @param string $string String to check.
     */
    public function isSerialized(string $string): bool
    {
        try {
            $value = @unserialize($string, ['allowed_classes' => false]);
        } catch (\Throwable) {
            return false;
        }

        return ! ($value === false && $string !== 'b:0;');
    }

    /**
     * Check if a string is HTML.
     *
     * @param string $string String to check.
     */
    public function isHtml(string $string): bool
    {
        return strlen(strip_tags($string)) < strlen($string);
    }

    // multibyte functions

    /**
     * Find the position of the first occurrence of a substring in a string.
     *
     * @param string $str The string being measured for length.
     * @param string|null $encoding Defaults to the setting in the config, which defaults to UTF-8.
     * @return int|false The length of the string on success, and 0 if the string is empty.
     */
    public function strlen(string $str, string|null $encoding = 'UTF-8'): int|false
    {
        return $encoding
        ? mb_strlen($str, $encoding)
        : strlen($str);
    }

    /**
     * Find position of first occurrence of string in a string.
     *
     * @param string $haystack The string being checked.
     * @param mixed $needle The string to find in haystack.
     * @param int $offset The search offset.
     * @param string|null $encoding
     * @return false|int Returns the position of where the needle exists relative to the beginning
*                        of the haystack string (independent of offset). Also note that string
*                        positions start at 0, and not 1.
*                        Returns false if the needle was not found.
     */
    public function strpos(string $haystack, mixed $needle, int $offset = 0, string|null $encoding = 'UTF-8'): false|int
    {
        return $encoding
        ? mb_strpos($haystack, $needle, $offset, $encoding)
        : strpos($haystack, $needle, $offset);
    }

    /**
     * Find position of last occurrence of a string in a string.
     *
     * @param string $haystack The string being checked.
     * @param mixed $needle The string to find in haystack.
     * @param int $offset The search offset.
     * @return false|int Returns the numeric position of the last occurrence of needle in the
     *                         haystack string. If needle is not found, it returns false.
     */
    public function strrpos(string $haystack, mixed $needle, int $offset = 0): false|int
    {
        return mb_strrpos($haystack, $needle, $offset, 'UTF-8');
    }

    /**
     * Get part of string.
     *
     * @param string $str The string to extract the substring from.
     * @param int $start If start is non-negative, the returned string will start at the start'th
     *                       position in str, counting from zero. If start is negative, the returned
     *                       string will start at the start'th character from the end of str.
     * @param int|null $length Maximum number of characters to use from str. If omitted or NULL is passed,
     *                       extract all characters to the end of the string.
     * @return mixed Returns the extracted part of string; or false on failure, or an empty string.
     */
    public function substr(string $str, int $start, int|null $length = null): mixed
    {
        // substr functions don't parse null correctly if the string is multibyte.
        if (null === $length) {
            $length = mb_strlen($str, 'UTF-8');
        }

        return mb_substr($str, $start, $length, 'UTF-8');
    }

    /**
     * Make a string lowercase.
     *
     * @param string $str The string to convert to lowercase.
     * @return string The lowercase string.
     */
    public function strtolower(string $str): string
    {
        return mb_strtolower($str, 'UTF-8');
    }

    /**
     * Make a string uppercase.
     *
     * @param string $str The string to convert to uppercase.
     * @return string The uppercase string.
     */
    public function strtoupper(string $str): string
    {
        return mb_strtoupper($str, 'UTF-8');
    }

    /**
     * Find the position of the first occurrence of a case-insensitive substring in a string.
     *
     * @param string $haystack The string from which to get the position of the last occurrence of needle.
     * @param string $needle   The string to find in haystack.
     * @param int    $offset   The search offset.
     * @return false|int Returns the position of where the needle exists relative to the beginning
     *               of the haystack string (independent of offset). Also note that string
     *               positions start at 0, and not 1.
     *               Returns false if the needle was not found.
     */
    public function stripos(string $haystack, string $needle, int $offset = 0): bool|int
    {
        return mb_stripos($haystack, $needle, $offset, 'UTF-8');
    }

    /**
     * Finds position of last occurrence of a string within another, case insensitive.
     *
     * @param string $haystack The string from which to get the position of the last occurrence of needle.
     * @param string $needle   The string to find in haystack.
     * @param int    $offset   The search offset.
     * @return int|bool Returns the numeric position of the last occurrence of needle in the
     *               haystack string. If needle is not found, it returns false.
     */
    public function strripos(string $haystack, string $needle, int $offset = 0): int|bool
    {
        return mb_strripos($haystack, $needle, $offset, 'UTF-8');
    }

    /**
     * Finds first occurrence of a string within another.
     *
     * @param string $haystack The string from which to get the position of the last occurrence of needle.
     * @param string $needle The string to find in haystack.
     * @param bool $beforeNeedle Determines which portion of haystack this function returns.
     * @return string|bool The portion of haystack, or false if needle is not found.
     */
    public function strstr(string $haystack, string $needle, bool $beforeNeedle = false): string|bool
    {
        return mb_strstr($haystack, $needle, $beforeNeedle, 'UTF-8');
    }

    /**
     * Finds first occurrence of a string within another, case-insensitive.
     *
     * @param string $haystack The string from which to get the position of the last occurrence of needle.
     * @param string $needle The string to find in haystack.
     * @param bool $beforeNeedle Determines which portion of haystack this function returns.
     * @return string|bool The portion of haystack, or false if needle is not found.
     */
    public function stristr(string $haystack, string $needle, bool $beforeNeedle = false): string|bool
    {
        return mb_stristr($haystack, $needle, $beforeNeedle, 'UTF-8');
    }

    /**
     * Finds the last occurrence of a character in a string within another.
     *
     * @param string $haystack The string from which to get the last occurrence of needle.
     * @param string $needle The string to find in haystack.
     * @param bool $beforeNeedle
     * @return false|string           The portion of haystack, or false if needle is not found.
     */
    public function strrchr(string $haystack, string $needle, bool $beforeNeedle = false): bool|string
    {
        return mb_strrchr($haystack, $needle, $beforeNeedle, 'UTF-8');
    }

    /**
     * substr_count — Count the number of substring occurrences.
     *
     * @param string      $haystack The string from which to get the position of the last occurrence of needle.
     * @param string      $needle   The string to find in haystack.
     * @param string|null $encoding
     * @return int The number of occurrences found.
     */
    public function substrCount(string $haystack, string $needle, ?string $encoding = 'UTF-8'): int
    {
        return mb_substr_count($haystack, $needle, $encoding);
    }

    /**
     * Does not strtoupper first.
     *
     * @param string $str String to lowercase first letter.
     */
    public function lcfirst(string $str): string
    {
        return mb_strtolower(mb_substr($str, 0, 1, 'UTF-8'), 'UTF-8')
        . mb_substr($str, 1, mb_strlen($str, 'UTF-8'), 'UTF-8');
    }

    /**
     * Does not strtolower first.
     *
     * @param string $str String to uppercase first letter.
     */
    public function ucfirst(string $str): string
    {
        return mb_strtoupper(mb_substr($str, 0, 1, 'UTF-8'), 'UTF-8')
        . mb_substr($str, 1, mb_strlen($str, 'UTF-8'), 'UTF-8');
    }

    /**
     * First strtolower then ucwords.
     *
     * ucwords normally doesn't strtolower first
     * but MB_CASE_TITLE does, so ucwords now too.
     *
     * @param string $str String to uppercase.
     * @return string
     */
    public function ucwords(string $str): string
    {
        return mb_convert_case($str, MB_CASE_TITLE, 'UTF-8');
    }
}
