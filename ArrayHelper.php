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

namespace Qubus\Support;

use ArrayAccess;
use BadMethodCallException;
use Closure;
use Iterator;
use Qubus\Exception\Data\TypeException;
use Qubus\Inheritance\StaticProxyAware;

use function abs;
use function array_combine;
use function array_filter;
use function array_key_exists;
use function array_keys;
use function array_map;
use function array_merge;
use function array_pop;
use function array_search;
use function array_shift;
use function array_slice;
use function array_splice;
use function array_sum;
use function array_values;
use function arsort;
use function asort;
use function count;
use function explode;
use function func_get_arg;
use function func_get_args;
use function implode;
use function in_array;
use function is_array;
use function is_int;
use function is_numeric;
use function is_object;
use function is_string;
use function preg_match;
use function preg_replace;
use function property_exists;
use function Qubus\Support\Helpers\call_qubus_func_array;
use function stripos;

use const SORT_REGULAR;

class ArrayHelper
{
    use StaticProxyAware;

    /**
     * Gets a dot-notated key from an array, with a default value if it does
     * not exist.
     *
     * @param array|ArrayAccess $array $array The search array.
     * @param mixed|null $key The dot-notated key or array of keys.
     * @param string|null $default The default value
     * @return mixed
     * @throws TypeException
     */
    public function get(array|ArrayAccess $array, mixed $key = null, ?string $default = null): mixed
    {
        if (! is_array($array) && ! $array instanceof ArrayAccess) {
            throw new TypeException('First parameter must be an array or ArrayAccess object.');
        }

        if ($key === null) {
            return $array;
        }

        if (is_array($key)) {
            $return = [];
            foreach ($key as $k) {
                $return[$k] = $this->get($array, $k, $default);
            }
            return $return;
        }

        if (is_object($key)) {
            $key = (string) $key;
        }

        if (is_object($array)) {
            if (property_exists($array, $key)) {
                return $array->$key;
            }
        } elseif (array_key_exists($key, $array)) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $keyPart) {
            if (($array instanceof ArrayAccess && isset($array[$keyPart])) === false) {
                if (! is_array($array) || ! array_key_exists($keyPart, $array)) {
                    return $this->value($default);
                }
            }

            $array = $array[$keyPart];
        }

        return $array;
    }

    /**
     * Set an array item (dot-notated) to the value.
     *
     * @param array   $array  The array to insert it into
     * @param mixed   $key    The dot-notated key to set or array of keys
     * @param mixed|null $value  The value
     * @return void
     */
    public function set(array &$array, mixed $key, mixed $value = null): void
    {
        if ($key === null) {
            $array = $value;
            return;
        }

        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->set($array, $k, $v);
            }
        } else {
            $keys = explode('.', $key);

            while (count($keys) > 1) {
                $key = array_shift($keys);

                if (! isset($array[$key]) || ! is_array($array[$key])) {
                    $array[$key] = [];
                }

                $array = &$array[$key];
            }

            $array[array_shift($keys)] = $value;
        }
    }

    /**
     * Pluck an array of values from an array.
     *
     * @param array $array Collection of arrays to pluck from.
     * @param string $key Key of the value to pluck.
     * @param bool|int|string|null $index Optional return array index key, true for original index.
     * @return array Array of plucked values.
     * @throws TypeException
     */
    public function pluck(array $array, string $key, bool|int|string $index = null): array
    {
        $return = [];
        $getDeep = str_contains($key, '.') !== false;

        if (! $index) {
            foreach ($array as $i => $a) {
                $return[] = is_object($a) && ! $a instanceof ArrayAccess ? $a->{$key} : ($getDeep ? $this->get($a, $key) : $a[$key]);
            }
        } else {
            foreach ($array as $i => $a) {
                $index !== true && $i = is_object($a) && ! $a instanceof ArrayAccess ? $a->{$index} : $a[$index];
                $return[$i] = is_object($a) && ! $a instanceof ArrayAccess ? $a->{$key} : ($getDeep ? $this->get($a, $key) : $a[$key]);
            }
        }

        return $return;
    }

    /**
     * Array_key_exists with a dot-notated key from an array.
     *
     * @param array|ArrayAccess $array $array The search array
     * @param mixed $key The dot-notated key or array of keys
     * @return bool
     * @throws TypeException
     */
    public function keyExists(array|ArrayAccess $array, mixed $key): bool
    {
        if (! is_array($array) && ! $array instanceof ArrayAccess) {
            throw new TypeException('First parameter must be an array or ArrayAccess object.');
        }

        is_object($key) && $key = (string) $key;

        if (! is_string($key)) {
            return false;
        }

        if (array_key_exists($key, $array)) {
            return true;
        }

        foreach (explode('.', $key) as $keyPart) {
            if (($array instanceof ArrayAccess && isset($array[$keyPart])) === false) {
                if (! is_array($array) || ! array_key_exists($keyPart, $array)) {
                    return false;
                }
            }

            $array = $array[$keyPart];
        }

        return true;
    }

    /**
     * Unsets dot-notated k?string ey from an array
     *
     * @param array   $array    The search array
     * @param mixed|null $key      The dot-notated key or array of keys
     * @return mixed
     */
    public function delete(array &$array, mixed $key = null): mixed
    {
        if ($key === null) {
            return false;
        }

        if (is_array($key)) {
            $return = [];
            foreach ($key as $k) {
                $return[$k] = $this->delete($array, $k);
            }
            return $return;
        }

        $keyParts = explode('.', $key);

        if (! array_key_exists($keyParts[0], $array)) {
            return false;
        }

        $thisKey = array_shift($keyParts);

        if (! empty($keyParts)) {
            $key = implode('.', $keyParts);
            return $this->delete($array[$thisKey], $key);
        } else {
            unset($array[$thisKey]);
        }

        return true;
    }

    /**
     * Converts a multidimensional associative array into an array of key => values with the provided field names.
     *
     * @param array|Iterator $assoc The array to convert.
     * @param string $keyField The field name of the key field.
     * @param string $valField The field name of the value field.
     * @return array
     * @throws TypeException
     */
    public function assocToKeyVal(array|Iterator $assoc, string $keyField, string $valField): array
    {
        if (! is_array($assoc) && ! $assoc instanceof Iterator) {
            throw new TypeException('The first parameter must be an array.');
        }

        $output = [];
        foreach ($assoc as $row) {
            if (isset($row[$keyField]) && isset($row[$valField])) {
                $output[$row[$keyField]] = $row[$valField];
            }
        }

        return $output;
    }

    /**
     * Converts an array of key => values into a multidimensional associative array with the provided field names
     *
     * @param array|Iterator $array $array      the array to convert
     * @param string $keyField the field name of the key field
     * @param string $valField the field name of the value field
     * @return  array
     * @throws TypeException
     */
    public function keyValToAssoc(array|Iterator $array, string $keyField, string $valField): array
    {
        if (! is_array($array) && ! $array instanceof Iterator) {
            throw new TypeException('The first parameter must be an array.');
        }

        $output = [];
        foreach ($array as $key => $value) {
            $output[] = [
                $keyField => $key,
                $valField => $value,
            ];
        }

        return $output;
    }

    /**
     * Converts the given 1 dimensional non-associative array to an associative
     * array.
     *
     * The array given must have an even number of elements or null will be returned.
     *
     *     $this->toAssoc(['foo','bar']);
     *
     * @param array      $arr  the array to change
     * @return  array|null  the new array or null
     * @throws BadMethodCallException
     */
    public function toAssoc(array $arr): ?array
    {
        if (($count = count($arr)) % 2 > 0) {
            throw new BadMethodCallException('Number of values in toAssoc must be even.');
        }
        $keys = $vals = [];

        for ($i = 0; $i < $count - 1; $i += 2) {
            $keys[] = array_shift($arr);
            $vals[] = array_shift($arr);
        }
        return array_combine($keys, $vals);
    }

    /**
     * Checks if the given array is an assoc array.
     *
     * @param array $arr the array to check
     * @return bool True if it's an assoc array, false if not.
     */
    public function isAssoc(array $arr): bool
    {
        $counter = 0;
        foreach ($arr as $key => $unused) {
            if (! is_int($key) || $key !== $counter++) {
                return true;
            }
        }
        return false;
    }

    /**
     * Flattens a multi-dimensional associative array down into a 1 dimensional
     * associative array.
     *
     * @param array   $array   the array to flatten
     * @param string  $glue    what to glue the keys together with
     * @param bool    $reset   whether to reset and start over on a new array
     * @param bool    $indexed whether to flatten only associative array's, or also indexed ones
     * @return array
     */
    public function flatten(array $array, string $glue = ':', bool $reset = true, bool $indexed = true): array
    {
        static $return = [];
        static $currKey = [];

        if ($reset) {
            $return = [];
            $currKey = [];
        }

        foreach ($array as $key => $val) {
            $currKey[] = $key;
            if (is_array($val) && ($indexed || array_values($val) !== $val)) {
                $this->flatten($val, $glue, false, $indexed);
            } else {
                $return[implode($glue, $currKey)] = $val;
            }
            array_pop($currKey);
        }
        return $return;
    }

    /**
     * Flattens a multi-dimensional associative array down into a 1 dimensional
     * associative array.
     *
     * @param array   $array  the array to flatten
     * @param string  $glue   what to glue the keys together with
     * @param bool    $reset  whether to reset and start over on a new array
     * @return array
     */
    public function flattenAssoc(array $array, string $glue = ':', bool $reset = true): array
    {
        return $this->flatten($array, $glue, $reset, false);
    }

    /**
     * Reverse a flattened array in its original form.
     *
     * @param array   $array  flattened array
     * @param string  $glue   glue used in flattening
     * @return array The unflattened array.
     */
    public function reverseFlatten(array $array, string $glue = ':'): array
    {
        $return = [];

        foreach ($array as $key => $value) {
            if (stripos($key, $glue) !== false) {
                $keys = explode($glue, $key);
                $temp = &$return;
                while (count($keys) > 1) {
                    $key = array_shift($keys);
                    $key = is_numeric($key) ? (int) $key : $key;
                    if (! isset($temp[$key]) || ! is_array($temp[$key])) {
                        $temp[$key] = [];
                    }
                    $temp = &$temp[$key];
                }

                $key = array_shift($keys);
                $key = is_numeric($key) ? (int) $key : $key;
                $temp[$key] = $value;
            } else {
                $key = is_numeric($key) ? (int) $key : $key;
                $return[$key] = $value;
            }
        }

        return $return;
    }

    /**
     * Filters an array on prefixed associative keys.
     *
     * @param array   $array        The array to filter.
     * @param string  $prefix       Prefix to filter on.
     * @param bool    $removePrefix Whether to remove the prefix.
     * @return array
     */
    public function filterPrefixed(array $array, string $prefix, bool $removePrefix = true): array
    {
        $return = [];
        foreach ($array as $key => $val) {
            if (preg_match('/^' . $prefix . '/', $key)) {
                if ($removePrefix === true) {
                    $key = preg_replace('/^' . $prefix . '/', '', $key);
                }
                $return[$key] = $val;
            }
        }
        return $return;
    }

    /**
     * Recursive version of PHP's array_filter().
     *
     * @param array $array The array to filter.
     * @param callable|null $callback $callback The callback that determines whether a value is filtered.
     * @return array
     */
    public function filterRecursive(array $array, ?callable $callback = null): array
    {
        foreach ($array as &$value) {
            if (is_array($value)) {
                $value = $callback === null
                ? $this->filterRecursive($value)
                : $this->filterRecursive($value, $callback);
            }
        }

        return $callback === null ? array_filter($array) : array_filter($array, $callback);
    }

    /**
     * Removes items from an array that match a key prefix.
     *
     * @param array  $array  The array to remove from.
     * @param string $prefix Prefix to filter on.
     * @return array
     */
    public function removePrefixed(array $array, string $prefix): array
    {
        foreach ($array as $key => $val) {
            if (preg_match('/^' . $prefix . '/', $key)) {
                unset($array[$key]);
            }
        }
        return $array;
    }

    /**
     * Filters an array on suffixed associative keys.
     *
     * @param array  $array        The array to filter.
     * @param string $suffix       Suffix to filter on.
     * @param bool   $removeSuffix Whether to remove the suffix.
     * @return array
     */
    public function filterSuffixed(array $array, string $suffix, bool $removeSuffix = true): array
    {
        $return = [];
        foreach ($array as $key => $val) {
            if (preg_match('/' . $suffix . '$/', $key)) {
                if ($removeSuffix === true) {
                    $key = preg_replace('/' . $suffix . '$/', '', $key);
                }
                $return[$key] = $val;
            }
        }
        return $return;
    }

    /**
     * Removes items from an array that match a key suffix.
     *
     * @param array  $array  The array to remove from.
     * @param string $suffix Suffix to filter on.
     * @return array
     */
    public function removeSuffixed(array $array, string $suffix): array
    {
        foreach ($array as $key => $val) {
            if (preg_match('/' . $suffix . '$/', $key)) {
                unset($array[$key]);
            }
        }
        return $array;
    }

    /**
     * Filters an array by an array of keys
     *
     * @param array  $array  The array to filter.
     * @param array  $keys   The keys to filter
     * @param bool   $remove If true, removes the matched elements.
     * @return array
     */
    public function filterKeys(array $array, array $keys, bool $remove = false): array
    {
        $return = [];
        foreach ($keys as $key) {
            if (array_key_exists($key, $array)) {
                $remove || $return[$key] = $array[$key];
                if ($remove) {
                    unset($array[$key]);
                }
            }
        }
        return $remove ? $array : $return;
    }

    /**
     * Insert value(s) into an array, mostly an array_splice alias.
     *
     * WARNING: original array is edited by reference, only bool success is returned.
     *
     * @param array $original The original array (by reference).
     * @param mixed $value    The value(s) to insert, if you want to insert an array
     *                        it needs to be in an array itself.
     * @param int   $pos      The numeric position at which to insert, negative to count from the end backwards.
     * @return bool False when array shorter than $pos, otherwise true
     */
    public function insert(array &$original, mixed $value, int $pos): bool
    {
        if (count($original) < abs($pos)) {
            return false;
        }

        array_splice($original, $pos, 0, $value);

        return true;
    }

    /**
     * Insert value(s) into an array, mostly an array_splice alias
     * WARNING: original array is edited by reference, only bool success is returned
     *
     * @param array $original The original array (by reference)
     * @param mixed $values   The value(s) to insert, if you want to insert an array
     *                        it needs to be in an array itself.
     * @param int   $pos      The numeric position at which to insert, negative to count from the end backwards.
     * @return bool false when array shorter than $pos, otherwise true
     */
    public function insertAssoc(array &$original, mixed $values, int $pos): bool
    {
        if (count($original) < abs($pos)) {
            return false;
        }

        $original = array_slice($original, 0, $pos, true) + $values + array_slice($original, $pos, null, true);

        return true;
    }

    /**
     * Insert value(s) into an array before a specific key.
     *
     * WARNING: original array is edited by reference, only bool success is returned.
     *
     * @param array      $original The original array (by reference).
     * @param mixed      $value    The value(s) to insert, if you want to insert an array
     *                             it needs to be in an array itself.
     * @param int|string $key      The key before which to insert.
     * @param bool       $isAssoc  Whether the input is an associative array.
     * @return bool False when key isn't found in the array, otherwise true.
     */
    public function insertBeforeKey(array &$original, mixed $value, int|string $key, bool $isAssoc = false): bool
    {
        $pos = array_search($key, array_keys($original));

        if ($pos === false) {
            return false;
        }

        return $isAssoc ? $this->insertAssoc($original, $value, $pos) : $this->insert($original, $value, $pos);
    }

    /**
     * Insert value(s) into an array after a specific key.
     *
     * WARNING: original array is edited by reference, only bool success is returned.
     *
     * @param array      $original The original array (by reference).
     * @param mixed      $value    The value(s) to insert, if you want to insert an array
     *                             it needs to be in an array itself.
     * @param int|string $key      The key after which to insert.
     * @param bool       $isAssoc  Whether the input is an associative array.
     * @return bool False when key isn't found in the array, otherwise true.
     */
    public function insertAfterKey(array &$original, mixed $value, int|string $key, bool $isAssoc = false): bool
    {
        $pos = array_search($key, array_keys($original));

        if ($pos === false) {
            return false;
        }

        return $isAssoc ? $this->insertAssoc($original, $value, $pos + 1) : $this->insert($original, $value, $pos + 1);
    }

    /**
     * Insert value(s) into an array after a specific value (first found in array).
     *
     * @param array      $original The original array (by reference).
     * @param mixed      $value    The value(s) to insert, if you want to insert an array
     *                             it needs to be in an array itself.
     * @param int|string $search   The value after which to insert.
     * @param bool       $isAssoc  Whether the input is an associative array.
     * @return bool False when value isn't found in the array, otherwise true.
     */
    public function insertAfterValue(array &$original, mixed $value, int|string $search, bool $isAssoc = false): bool
    {
        $key = array_search($search, $original);

        if ($key === false) {
            return false;
        }

        return $this->insertAfterKey($original, $value, $key, $isAssoc);
    }

    /**
     * Insert value(s) into an array before a specific value (first found in array)
     *
     * @param array      $original The original array (by reference).
     * @param mixed      $value    The value(s) to insert, if you want to insert an array.
     *                             it needs to be in an array itself.
     * @param int|string $search   The value after which to insert.
     * @param bool       $isAssoc  Whether the input is an associative array.
     * @return bool False when value isn't found in the array, otherwise true.
     */
    public function insertBeforeValue(array &$original, mixed $value, int|string $search, bool $isAssoc = false): bool
    {
        $key = array_search($search, $original);

        if ($key === false) {
            return false;
        }

        return $this->insertBeforeKey($original, $value, $key, $isAssoc);
    }

    /**
     * Sorts a multi-dimensional array by it's values.
     *
     * @param array $array     The array to fetch from.
     * @param string $key       The key to sort by.
     * @param string $order     The order (asc or desc).
     * @param int    $sortFlags The php sort type flag.
     * @return array
     * @throws TypeException
     */
    public function sort(
        array $array,
        string $key,
        string $order = 'asc',
        int $sortFlags = SORT_REGULAR
    ): array {
        if (empty($array)) {
            return $array;
        }

        $b = [];

        foreach ($array as $k => $v) {
            $b[$k] = $this->get($v, $key);
        }

        switch ($order) {
            case 'asc':
                asort($b, $sortFlags);
                break;
            case 'desc':
                arsort($b, $sortFlags);
                break;
            default:
                throw new TypeException('$this->sort() - $order must be asc or desc.');
            break;
        }

        $c = [];

        foreach ($b as $key => $val) {
            $c[] = $array[$key];
        }

        return $c;
    }

    /**
     * Sorts an array on multiple values, with deep sorting support.
     *
     * @param array $array Collection of arrays/objects to sort.
     * @param array $conditions Sorting conditions.
     * @param bool $ignoreCase Whether to sort case-insensitive.
     * @return array
     * @throws TypeException
     */
    public function multisort(array $array, array $conditions, bool $ignoreCase = false): array
    {
        $temp = [];
        $keys = array_keys($conditions);

        foreach ($keys as $key) {
            $temp[$key] = $this->pluck($array, $key, true);
            is_array($conditions[$key]) || $conditions[$key] = [$conditions[$key]];
        }

        $args = [];
        foreach ($keys as $key) {
            $args[] = $ignoreCase ? array_map('strtolower', $temp[$key]) : $temp[$key];
            foreach ($conditions[$key] as $flag) {
                $args[] = $flag;
            }
        }

        $args[] = &$array;

        call_qubus_func_array('array_multisort', $args);

        return $array;
    }

    /**
     * Find the average of an array.
     *
     * @param array $array The array containing the values.
     * @return float|int The average value.
     */
    public function average(array $array): float|int
    {
        // No arguments passed, lets not divide by 0
        if (! ($count = count($array)) > 0) {
            return 0;
        }

        return array_sum($array) / $count;
    }

    /**
     * Replaces key names in an array by names in the $replace parameter.
     *
     * @param array $source The array containing the key/value combinations
     * @param array|string $replace Key to replace or array containing the replacement keys
     * @param string|null $newKey The replacement key
     * @return array The array with the new keys.
     */
    public function replaceKey(array $source, array|string $replace, ?string $newKey = null): array
    {
        if (is_string($replace)) {
            $replace = [$replace => $newKey];
        }

        $result = [];

        foreach ($source as $key => $value) {
            if (array_key_exists($key, $replace)) {
                $result[$replace[$key]] = $value;
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Merge 2 arrays recursively, differs in 2 important ways from array_merge_recursive().
     *
     * When there's 2 different values and not both arrays, the latter value overwrites the earlier
     * instead of merging both into an array.
     *
     * Numeric keys that don't conflict aren't changed, only when a numeric key already exists is the
     * value added using array_push().
     *
     * @return array
     * @throws TypeException
     */
    public function merge(): array
    {
        $array  = func_get_arg(0);
        $arrays = array_slice(func_get_args(), 1);

        if (! is_array($array)) {
            throw new TypeException('$this->merge() - all arguments must be arrays.');
        }

        foreach ($arrays as $arr) {
            if (! is_array($arr)) {
                throw new TypeException('$this->merge() - all arguments must be arrays.');
            }

            foreach ($arr as $k => $v) {
                // numeric keys are appended
                if (is_int($k)) {
                    array_key_exists($k, $array) ? $array[] = $v : $array[$k] = $v;
                } elseif (is_array($v) && array_key_exists($k, $array) && is_array($array[$k])) {
                    $array[$k] = $this->merge($array[$k], $v);
                } else {
                    $array[$k] = $v;
                }
            }
        }

        return $array;
    }

    /**
     * Merge 2 arrays recursively, differs in 2 important ways from array_merge_recursive().
     *
     * When there's 2 different values and not both arrays, the latter value overwrites the earlier
     * instead of merging both into an array. Numeric keys are never changed.
     *
     * @return array
     * @throws TypeException
     */
    public function mergeAssoc(): array
    {
        $array  = func_get_arg(0);
        $arrays = array_slice(func_get_args(), 1);

        if (! is_array($array)) {
            throw new TypeException('$this->mergeAssoc() - all arguments must be arrays.');
        }

        foreach ($arrays as $arr) {
            if (! is_array($arr)) {
                throw new TypeException('$this->mergeAssoc() - all arguments must be arrays.');
            }

            foreach ($arr as $k => $v) {
                if (is_array($v) && array_key_exists($k, $array) && is_array($array[$k])) {
                    $array[$k] = $this->mergeAssoc($array[$k], $v);
                } else {
                    $array[$k] = $v;
                }
            }
        }

        return $array;
    }

    /**
     * Prepends a value with an associative key to an array.
     *
     * Will overwrite if the value exists.
     *
     * @param array        $arr   The array to prepend to
     * @param array|string $key   The key or array of keys and values
     * @param mixed|null $value The value to prepend
     */
    public function prepend(array &$arr, array|string $key, mixed $value = null): string|array
    {
        $arr = (is_array($key) ? $key : [$key => $value]) + $arr;

        return $arr;
    }

    /**
     * Recursive in_array
     *
     * @param mixed $needle   What to search for.
     * @param array $haystack Array to search in.
     * @return bool Whether the needle is found in the haystack.
     */
    public function inArrayRecursive(mixed $needle, array $haystack, bool $strict = false): bool
    {
        foreach ($haystack as $value) {
            if (! $strict && $needle === $value) {
                return true;
            } elseif ($needle === $value) {
                return true;
            } elseif (is_array($value) && $this->inArrayRecursive($needle, $value, $strict)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if the given array is a multidimensional array.
     *
     * @param array $arr     The array to check
     * @param bool  $allKeys If true, check that all elements are arrays.
     * @return bool True if its a multidimensional array, false if not.
     */
    public function isMulti(array $arr, bool $allKeys = false): bool
    {
        $values = array_filter($arr, 'is_array');
        return $allKeys ? count($arr) === count($values) : count($values) > 0;
    }

    /**
     * Searches the array for a given value and returns the
     * corresponding key or default value.
     *
     * If $recursive is set to true, then the $this->search()
     * method will return a delimiter-notated key using the
     * $delimiter parameter.
     *
     * @param array|ArrayAccess $array $array     The search array.
     * @param mixed $value The searched value.
     * @param string|null $default The default value.
     * @param bool $recursive Whether to get keys recursive.
     * @param string $delimiter The delimiter, when $recursive is true.
     * @param bool $strict If true, do a strict key comparison.
     * @return string|bool|null
     * @throws TypeException
     */
    public function search(
        array|ArrayAccess $array,
        mixed $value,
        ?string $default = null,
        bool $recursive = true,
        string $delimiter = '.',
        bool $strict = false
    ): string|bool|null {
        if (! is_array($array) && ! $array instanceof ArrayAccess) {
            throw new TypeException('First parameter must be an array or ArrayAccess object.');
        }

        if (null !== $default && ! is_int($default) && ! is_string($default)) {
            throw new TypeException('Expects parameter 3 ($default) to be a string or integer or null.');
        }

        if (! is_string($delimiter)) {
            throw new TypeException('Expects parameter 5 ($delimiter) to be a string.');
        }

        $key = array_search($value, $array, $strict);

        if ($recursive && $key === false) {
            $keys = [];
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    $rk = $this->search($v, $value, $default, true, $delimiter, $strict);
                    if ($rk !== $default) {
                        $keys = [$k, $rk];
                        break;
                    }
                }
            }
            $key = count($keys) ? implode($delimiter, $keys) : false;
        }

        return $key === false ? $default : $key;
    }

    /**
     * Returns only unique values in an array. It does not sort. First value is used.
     *
     * @param array $arr The array to dedupe.
     * @return array array With only de-duped values.
     */
    public function unique(array $arr): array
    {
        // filter out all duplicate values
        return array_filter($arr, function ($item) {
            // contrary to popular belief, this is not as static as you think...
            static $vars = [];

            if (in_array($item, $vars, true)) {
                // duplicate
                return false;
            } else {
                // record we've had this value
                $vars[] = $item;

                // unique
                return true;
            }
        });
    }

    /**
     * Calculate the sum of an array.
     *
     * @param array|ArrayAccess $array $array The array containing the values.
     * @param string $key Key of the value to pluck.
     * @return float|int The sum value
     * @throws TypeException
     */
    public function sum(array|ArrayAccess $array, string $key): float|int
    {
        if (! is_array($array) && ! $array instanceof ArrayAccess) {
            throw new TypeException('First parameter must be an array or ArrayAccess object.');
        }

        return array_sum($this->pluck($array, $key));
    }

    /**
     * Returns the array with all numeric keys re-indexed, and string keys untouched.
     *
     * @param array $arr The array to reindex.
     * @return array Re-indexed array.
     */
    public function reindex(array $arr): array
    {
        // reindex this level
        $arr = array_merge($arr);

        foreach ($arr as $k => &$v) {
            is_array($v) && $v = $this->reindex($v);
        }

        return $arr;
    }

    /**
     * Get the previous value or key from an array using the current array key.
     *
     * @param array|ArrayAccess $array $array    The array containing the values.
     * @param string $key Key of the current entry to use as reference.
     * @param bool $getValue If true, return the previous value instead of the previous key.
     * @param bool $strict If true, do a strict key comparison.
     * @return string|bool|null The value in the array, null if there is no previous value,
     *                          or false if the key doesn't exist.
     * @throws TypeException
     */
    public function previousByKey(
        array|ArrayAccess $array,
        string $key,
        bool $getValue = false,
        bool $strict = false
    ): string|bool|null {
        if (! is_array($array) && ! $array instanceof ArrayAccess) {
            throw new TypeException('First parameter must be an array or ArrayAccess object.');
        }

        // get the keys of the array
        $keys = array_keys($array);

        // and do a lookup of the key passed
        if (($index = array_search($key, $keys, $strict)) === false) {
            // key does not exist
            return false;
        } elseif (! isset($keys[$index - 1])) { // check if we have a previous key
            // there is none
            return null;
        }

        // return the value or the key of the array entry the previous key points to
        return $getValue ? $array[$keys[$index - 1]] : $keys[$index - 1];
    }

    /**
     * Get the next value or key from an array using the current array key.
     *
     * @param array|ArrayAccess $array $array The array containing the values.
     * @param string $key Key of the current entry to use as reference.
     * @param bool $getValue If true, return the next value instead of the next key.
     * @param bool $strict If true, do a strict key comparison.
     * @return string|bool|null The value in the array, null if there is no next value,
     *                          or false if the key doesn't exist.
     * @throws TypeException
     */
    public function nextByKey(
        array|ArrayAccess $array,
        string $key,
        bool $getValue = false,
        bool $strict = false
    ): string|bool|null {
        if (! is_array($array) && ! $array instanceof ArrayAccess) {
            throw new TypeException('First parameter must be an array or ArrayAccess object.');
        }

        // get the keys of the array
        $keys = array_keys($array);

        // and do a lookup of the key passed
        if (($index = array_search($key, $keys, $strict)) === false) {
            // key does not exist
            return false;
        }

        // check if we have a previous key
        if (! isset($keys[$index + 1])) {
            // there is none
            return null;
        }

        // return the value or the key of the array entry the previous key points to
        return $getValue ? $array[$keys[$index + 1]] : $keys[$index + 1];
    }

    /**
     * Get the previous value or key from an array using the current array value
     *
     * @param array|ArrayAccess $array $array    The array containing the values.
     * @param string $value Value of the current entry to use as reference.
     * @param bool $getValue If true, return the previous value instead of the previous key.
     * @param bool $strict If true, do a strict key comparison.
     * @return string|bool|null The value in the array, null if there is no previous value,
     *                          or false if the key doesn't exist.
     * @throws TypeException
     */
    public function previousByValue(
        array|ArrayAccess $array,
        string $value,
        bool $getValue = true,
        bool $strict = false
    ): string|bool|null {
        if (! is_array($array) && ! $array instanceof ArrayAccess) {
            throw new TypeException('First parameter must be an array or ArrayAccess object.');
        }

        // find the current value in the array
        if (($key = array_search($value, $array, $strict)) === false) {
            // bail out if not found
            return false;
        }

        // get the list of keys, and find our found key
        $keys = array_keys($array);
        $index = array_search($key, $keys);

        // if there is no previous one, bail out
        if (! isset($keys[$index - 1])) {
            return null;
        }

        // return the value or the key of the array entry the previous key points to
        return $getValue ? $array[$keys[$index - 1]] : $keys[$index - 1];
    }

    /**
     * Get the next value or key from an array using the current array value.
     *
     * @param array|ArrayAccess $array $array    The array containing the values.
     * @param string $value Value of the current entry to use as reference.
     * @param bool $getValue If true, return the next value instead of the next key.
     * @param bool $strict If true, do a strict key comparison.
     * @return string|bool|null The value in the array, null if there is no next value,
     *                          or false if the key doesn't exist
     * @throws TypeException
     */
    public function nextByValue(
        array|ArrayAccess $array,
        string $value,
        bool $getValue = true,
        bool $strict = false
    ): string|bool|null {
        if (! is_array($array) && ! $array instanceof ArrayAccess) {
            throw new TypeException('First parameter must be an array or ArrayAccess object.');
        }

        // find the current value in the array
        if (($key = array_search($value, $array, $strict)) === false) {
            // bail out if not found
            return false;
        }

        // get the list of keys, and find our found key
        $keys = array_keys($array);
        $index = array_search($key, $keys);

        // if there is no next one, bail out
        if (! isset($keys[$index + 1])) {
            return null;
        }

        // return the value or the key of the array entry the next key points to
        return $getValue ? $array[$keys[$index + 1]] : $keys[$index + 1];
    }

    /**
     * Return the subset of the array defined by the supplied keys.
     *
     * Returns $default for missing keys, as with $this->get().
     *
     * @param array $array The array containing the values.
     * @param array $keys List of keys (or indices) to return.
     * @param mixed|null $default Value of missing keys; default null.
     * @return array An array containing the same set of keys provided.
     * @throws TypeException
     */
    public function subset(array $array, array $keys, mixed $default = null): array
    {
        $result = [];

        foreach ($keys as $key) {
            $this->set($result, $key, $this->get($array, $key, $default));
        }

        return $result;
    }

    /**
     * Takes a value and checks if it is a Closure or not, if it is it
     * will return the result of the closure, if not, it will simply return the
     * value.
     *
     * @param mixed $var The value to get.
     * @return mixed
     */
    public function value(mixed $var): mixed
    {
        return $var instanceof Closure ? $var() : $var;
    }
}
