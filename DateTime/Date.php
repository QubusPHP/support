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

namespace Qubus\Support\DateTime;

interface Date
{
    /**
     * Formats date.
     *
     * This function uses the set timezone from date config.
     *
     * Example Usage:
     *
     *      $datetime = 'May 15, 2018 2:15 PM';
     *      $this->format('Y-m-d H:i:s', $datetime);
     *
     * @param string $format Format of the date. Default is `Y-m-d H:i:s`.
     * @return string
     */
    public function format(string $format = 'Y-m-d H:i:s'): string;

    /**
     * Returns the current time based on specified type.
     *
     * The 'db' type will return the time in the format for database date field(s).
     * The 'timestamp' type will return the current timestamp.
     * Other strings will be interpreted as PHP date formats (e.g. 'Y-m-d H:i:s').
     *
     * If $gmt is set to either '1' or 'true', then both types will use GMT time.
     * If $gmt is false, the output is adjusted with the GMT offset based on date config.
     *
     * @param string $type Type of time to return. Accepts 'db', 'timestamp', or PHP date
     *                     format string (e.g. 'Y-m-d').
     * @param bool $gmt    Optional. Whether to use GMT timezone. Default false.
     * @return int|string Integer if $type is 'timestamp', string otherwise.
     */
    public function current(string $type, bool $gmt = false): string|int;
}
