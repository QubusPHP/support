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

use Carbon\Carbon;
use DateTimeInterface;

/**
 * @see https://carbon.nesbot.com/docs/
 * @see https://github.com/briannesbitt/Carbon/blob/master/src/Carbon/Carbon.php
 *
 * @property      int                 $year
 * @property      int                 $yearIso
 * @property      int                 $month
 * @property      int                 $day
 * @property      int                 $hour
 * @property      int                 $minute
 * @property      int                 $second
 * @property      int                 $micro
 * @property      int                 $microsecond
 * @property      int|float|string    $timestamp                                                                           seconds since the Unix Epoch
 * @property      string              $englishDayOfWeek                                                                    the day of week in English
 * @property      string              $shortEnglishDayOfWeek                                                               the abbreviated day of week in English
 * @property      string              $englishMonth                                                                        the month in English
 * @property      string              $shortEnglishMonth                                                                   the abbreviated month in English
 * @property      string              $localeDayOfWeek                                                                     the day of week in current locale LC_TIME
 * @property      string              $shortLocaleDayOfWeek                                                                the abbreviated day of week in current locale LC_TIME
 * @property      string              $localeMonth                                                                         the month in current locale LC_TIME
 * @property      string              $shortLocaleMonth                                                                    the abbreviated month in current locale LC_TIME
 * @property      int                 $milliseconds
 * @property      int                 $millisecond
 * @property      int                 $milli
 * @property      int                 $week                                                                                1 through 53
 * @property      int                 $isoWeek                                                                             1 through 53
 * @property      int                 $weekYear                                                                            year according to week format
 * @property      int                 $isoWeekYear                                                                         year according to ISO week format
 * @property      int                 $dayOfYear                                                                           1 through 366
 * @property      int                 $age                                                                                 does a diffInYears() with default parameters
 * @property      int                 $offset                                                                              the timezone offset in seconds from UTC
 * @property      int                 $offsetMinutes                                                                       the timezone offset in minutes from UTC
 * @property      int                 $offsetHours                                                                         the timezone offset in hours from UTC
 * @property      CarbonTimeZone      $timezone                                                                            the current timezone
 * @property      CarbonTimeZone      $tz                                                                                  alias of $timezone
 * @property-read int                 $dayOfWeek                                                                           0 (for Sunday) through 6 (for Saturday)
 * @property-read int                 $dayOfWeekIso                                                                        1 (for Monday) through 7 (for Sunday)
 * @property-read int                 $weekOfYear                                                                          ISO-8601 week number of year, weeks starting on Monday
 * @property-read int                 $daysInMonth                                                                         number of days in the given month
 * @property-read string              $latinMeridiem                                                                       "am"/"pm" (Ante meridiem or Post meridiem latin lowercase mark)
 * @property-read string              $latinUpperMeridiem                                                                  "AM"/"PM" (Ante meridiem or Post meridiem latin uppercase mark)
 * @property-read string              $timezoneAbbreviatedName                                                             the current timezone abbreviated name
 * @property-read string              $tzAbbrName                                                                          alias of $timezoneAbbreviatedName
 * @property-read string              $dayName                                                                             long name of weekday translated according to Carbon locale, in english if no translation available for current language
 * @property-read string              $shortDayName                                                                        short name of weekday translated according to Carbon locale, in english if no translation available for current language
 * @property-read string              $minDayName                                                                          very short name of weekday translated according to Carbon locale, in english if no translation available for current language
 * @property-read string              $monthName                                                                           long name of month translated according to Carbon locale, in english if no translation available for current language
 * @property-read string              $shortMonthName                                                                      short name of month translated according to Carbon locale, in english if no translation available for current language
 * @property-read string              $meridiem                                                                            lowercase meridiem mark translated according to Carbon locale, in latin if no translation available for current language
 * @property-read string              $upperMeridiem                                                                       uppercase meridiem mark translated according to Carbon locale, in latin if no translation available for current language
 * @property-read int                 $noZeroHour                                                                          current hour from 1 to 24
 * @property-read int                 $weeksInYear                                                                         51 through 53
 * @property-read int                 $isoWeeksInYear                                                                      51 through 53
 * @property-read int                 $weekOfMonth                                                                         1 through 5
 * @property-read int                 $weekNumberInMonth                                                                   1 through 5
 * @property-read int                 $firstWeekDay                                                                        0 through 6
 * @property-read int                 $lastWeekDay                                                                         0 through 6
 * @property-read int                 $daysInYear                                                                          365 or 366
 * @property-read int                 $quarter                                                                             the quarter of this instance, 1 - 4
 * @property-read int                 $decade                                                                              the decade of this instance
 * @property-read int                 $century                                                                             the century of this instance
 * @property-read int                 $millennium                                                                          the millennium of this instance
 * @property-read bool                $dst                                                                                 daylight savings time indicator, true if DST, false otherwise
 * @property-read bool                $local                                                                               checks if the timezone is local, true if local, false otherwise
 * @property-read bool                $utc                                                                                 checks if the timezone is UTC, true if UTC, false otherwise
 * @property-read string              $timezoneName                                                                        the current timezone name
 * @property-read string              $tzName                                                                              alias of $timezoneName
 * @property-read string              $locale                                                                              locale of the current instance
 *
 * @method        bool                isUtc()                                                                              Check if the current instance has UTC timezone. (Both isUtc and isUTC cases are valid.)
 * @method        bool                isLocal()                                                                            Check if the current instance has non-UTC timezone.
 * @method        bool                isValid()                                                                            Check if the current instance is a valid date.
 * @method        bool                isDST()                                                                              Check if the current instance is in a daylight saving time.
 * @method        bool                isSunday()                                                                           Checks if the instance day is sunday.
 * @method        bool                isMonday()                                                                           Checks if the instance day is monday.
 * @method        bool                isTuesday()                                                                          Checks if the instance day is tuesday.
 * @method        bool                isWednesday()                                                                        Checks if the instance day is wednesday.
 * @method        bool                isThursday()                                                                         Checks if the instance day is thursday.
 * @method        bool                isFriday()                                                                           Checks if the instance day is friday.
 * @method        bool                isSaturday()                                                                         Checks if the instance day is saturday.
 * @method        bool                isSameYear(Carbon|DateTimeInterface|string|null $date = null)                        Checks if the given date is in the same year as the instance. If null passed, compare to now (with the same timezone).
 * @method        bool                isCurrentYear()                                                                      Checks if the instance is in the same year as the current moment.
 * @method        bool                isNextYear()                                                                         Checks if the instance is in the same year as the current moment next year.
 * @method        bool                isLastYear()                                                                         Checks if the instance is in the same year as the current moment last year.
 * @method        bool                isSameWeek(Carbon|DateTimeInterface|string|null $date = null)                        Checks if the given date is in the same week as the instance. If null passed, compare to now (with the same timezone).
 * @method        bool                isCurrentWeek()                                                                      Checks if the instance is in the same week as the current moment.
 * @method        bool                isNextWeek()                                                                         Checks if the instance is in the same week as the current moment next week.
 * @method        bool                isLastWeek()                                                                         Checks if the instance is in the same week as the current moment last week.
 * @method        bool                isSameDay(Carbon|DateTimeInterface|string|null $date = null)                         Checks if the given date is in the same day as the instance. If null passed, compare to now (with the same timezone).
 * @method        bool                isCurrentDay()                                                                       Checks if the instance is in the same day as the current moment.
 * @method        bool                isNextDay()                                                                          Checks if the instance is in the same day as the current moment next day.
 * @method        bool                isLastDay()                                                                          Checks if the instance is in the same day as the current moment last day.
 * @method        bool                isSameHour(Carbon|DateTimeInterface|string|null $date = null)                        Checks if the given date is in the same hour as the instance. If null passed, compare to now (with the same timezone).
 * @method        bool                isCurrentHour()                                                                      Checks if the instance is in the same hour as the current moment.
 * @method        bool                isNextHour()                                                                         Checks if the instance is in the same hour as the current moment next hour.
 * @method        bool                isLastHour()                                                                         Checks if the instance is in the same hour as the current moment last hour.
 * @method        bool                isSameMinute(Carbon|DateTimeInterface|string|null $date = null)                      Checks if the given date is in the same minute as the instance. If null passed, compare to now (with the same timezone).
 * @method        bool                isCurrentMinute()                                                                    Checks if the instance is in the same minute as the current moment.
 * @method        bool                isNextMinute()                                                                       Checks if the instance is in the same minute as the current moment next minute.
 * @method        bool                isLastMinute()                                                                       Checks if the instance is in the same minute as the current moment last minute.
 * @method        bool                isSameSecond(Carbon|DateTimeInterface|string|null $date = null)                      Checks if the given date is in the same second as the instance. If null passed, compare to now (with the same timezone).
 * @method        bool                isCurrentSecond()                                                                    Checks if the instance is in the same second as the current moment.
 * @method        bool                isNextSecond()                                                                       Checks if the instance is in the same second as the current moment next second.
 * @method        bool                isLastSecond()                                                                       Checks if the instance is in the same second as the current moment last second.
 * @method        bool                isSameMicro(Carbon|DateTimeInterface|string|null $date = null)                       Checks if the given date is in the same microsecond as the instance. If null passed, compare to now (with the same timezone).
 * @method        bool                isCurrentMicro()                                                                     Checks if the instance is in the same microsecond as the current moment.
 * @method        bool                isNextMicro()                                                                        Checks if the instance is in the same microsecond as the current moment next microsecond.
 * @method        bool                isLastMicro()                                                                        Checks if the instance is in the same microsecond as the current moment last microsecond.
 * @method        bool                isSameMicrosecond(Carbon|DateTimeInterface|string|null $date = null)                 Checks if the given date is in the same microsecond as the instance. If null passed, compare to now (with the same timezone).
 * @method        bool                isCurrentMicrosecond()                                                               Checks if the instance is in the same microsecond as the current moment.
 * @method        bool                isNextMicrosecond()                                                                  Checks if the instance is in the same microsecond as the current moment next microsecond.
 * @method        bool                isLastMicrosecond()                                                                  Checks if the instance is in the same microsecond as the current moment last microsecond.
 * @method        bool                isCurrentMonth()                                                                     Checks if the instance is in the same month as the current moment.
 * @method        bool                isNextMonth()                                                                        Checks if the instance is in the same month as the current moment next month.
 * @method        bool                isLastMonth()                                                                        Checks if the instance is in the same month as the current moment last month.
 * @method        bool                isCurrentQuarter()                                                                   Checks if the instance is in the same quarter as the current moment.
 * @method        bool                isNextQuarter()                                                                      Checks if the instance is in the same quarter as the current moment next quarter.
 * @method        bool                isLastQuarter()                                                                      Checks if the instance is in the same quarter as the current moment last quarter.
 * @method        bool                isSameDecade(Carbon|DateTimeInterface|string|null $date = null)                      Checks if the given date is in the same decade as the instance. If null passed, compare to now (with the same timezone).
 * @method        bool                isCurrentDecade()                                                                    Checks if the instance is in the same decade as the current moment.
 * @method        bool                isNextDecade()                                                                       Checks if the instance is in the same decade as the current moment next decade.
 * @method        bool                isLastDecade()                                                                       Checks if the instance is in the same decade as the current moment last decade.
 * @method        bool                isSameCentury(Carbon|DateTimeInterface|string|null $date = null)                     Checks if the given date is in the same century as the instance. If null passed, compare to now (with the same timezone).
 * @method        bool                isCurrentCentury()                                                                   Checks if the instance is in the same century as the current moment.
 * @method        bool                isNextCentury()                                                                      Checks if the instance is in the same century as the current moment next century.
 * @method        bool                isLastCentury()                                                                      Checks if the instance is in the same century as the current moment last century.
 * @method        bool                isSameMillennium(Carbon|DateTimeInterface|string|null $date = null)                  Checks if the given date is in the same millennium as the instance. If null passed, compare to now (with the same timezone).
 * @method        bool                isCurrentMillennium()                                                                Checks if the instance is in the same millennium as the current moment.
 * @method        bool                isNextMillennium()                                                                   Checks if the instance is in the same millennium as the current moment next millennium.
 * @method        bool                isLastMillennium()                                                                   Checks if the instance is in the same millennium as the current moment last millennium.
 * @method        $this               years(int $value)                                                                    Set current instance year to the given value.
 * @method        $this               year(int $value)                                                                     Set current instance year to the given value.
 * @method        $this               setYears(int $value)                                                                 Set current instance year to the given value.
 * @method        $this               setYear(int $value)                                                                  Set current instance year to the given value.
 * @method        $this               months(int $value)                                                                   Set current instance month to the given value.
 * @method        $this               month(int $value)                                                                    Set current instance month to the given value.
 * @method        $this               setMonths(int $value)                                                                Set current instance month to the given value.
 * @method        $this               setMonth(int $value)                                                                 Set current instance month to the given value.
 * @method        $this               days(int $value)                                                                     Set current instance day to the given value.
 * @method        $this               day(int $value)                                                                      Set current instance day to the given value.
 * @method        $this               setDays(int $value)                                                                  Set current instance day to the given value.
 * @method        $this               setDay(int $value)                                                                   Set current instance day to the given value.
 * @method        $this               hours(int $value)                                                                    Set current instance hour to the given value.
 * @method        $this               hour(int $value)                                                                     Set current instance hour to the given value.
 * @method        $this               setHours(int $value)                                                                 Set current instance hour to the given value.
 * @method        $this               setHour(int $value)                                                                  Set current instance hour to the given value.
 * @method        $this               minutes(int $value)                                                                  Set current instance minute to the given value.
 * @method        $this               minute(int $value)                                                                   Set current instance minute to the given value.
 * @method        $this               setMinutes(int $value)                                                               Set current instance minute to the given value.
 * @method        $this               setMinute(int $value)                                                                Set current instance minute to the given value.
 * @method        $this               seconds(int $value)                                                                  Set current instance second to the given value.
 * @method        $this               second(int $value)                                                                   Set current instance second to the given value.
 * @method        $this               setSeconds(int $value)                                                               Set current instance second to the given value.
 * @method        $this               setSecond(int $value)                                                                Set current instance second to the given value.
 * @method        $this               millis(int $value)                                                                   Set current instance millisecond to the given value.
 * @method        $this               milli(int $value)                                                                    Set current instance millisecond to the given value.
 * @method        $this               setMillis(int $value)                                                                Set current instance millisecond to the given value.
 * @method        $this               setMilli(int $value)                                                                 Set current instance millisecond to the given value.
 * @method        $this               milliseconds(int $value)                                                             Set current instance millisecond to the given value.
 * @method        $this               millisecond(int $value)                                                              Set current instance millisecond to the given value.
 * @method        $this               setMilliseconds(int $value)                                                          Set current instance millisecond to the given value.
 * @method        $this               setMillisecond(int $value)                                                           Set current instance millisecond to the given value.
 * @method        $this               micros(int $value)                                                                   Set current instance microsecond to the given value.
 * @method        $this               micro(int $value)                                                                    Set current instance microsecond to the given value.
 * @method        $this               setMicros(int $value)                                                                Set current instance microsecond to the given value.
 * @method        $this               setMicro(int $value)                                                                 Set current instance microsecond to the given value.
 * @method        $this               microseconds(int $value)                                                             Set current instance microsecond to the given value.
 * @method        $this               microsecond(int $value)                                                              Set current instance microsecond to the given value.
 * @method        $this               setMicroseconds(int $value)                                                          Set current instance microsecond to the given value.
 * @method        $this               setMicrosecond(int $value)                                                           Set current instance microsecond to the given value.
 * @method        $this               addYears(int $value = 1)                                                             Add years (the $value count passed in) to the instance (using date interval).
 * @method        $this               addYear()                                                                            Add one year to the instance (using date interval).
 * @method        $this               subYears(int $value = 1)                                                             Sub years (the $value count passed in) to the instance (using date interval).
 * @method        $this               subYear()                                                                            Sub one year to the instance (using date interval).
 * @method        $this               addYearsWithOverflow(int $value = 1)                                                 Add years (the $value count passed in) to the instance (using date interval) with overflow explicitly allowed.
 * @method        $this               addYearWithOverflow()                                                                Add one year to the instance (using date interval) with overflow explicitly allowed.
 * @method        $this               subYearsWithOverflow(int $value = 1)                                                 Sub years (the $value count passed in) to the instance (using date interval) with overflow explicitly allowed.
 * @method        $this               subYearWithOverflow()                                                                Sub one year to the instance (using date interval) with overflow explicitly allowed.
 * @method        $this               addYearsWithoutOverflow(int $value = 1)                                              Add years (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               addYearWithoutOverflow()                                                             Add one year to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               subYearsWithoutOverflow(int $value = 1)                                              Sub years (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               subYearWithoutOverflow()                                                             Sub one year to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               addYearsWithNoOverflow(int $value = 1)                                               Add years (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               addYearWithNoOverflow()                                                              Add one year to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               subYearsWithNoOverflow(int $value = 1)                                               Sub years (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               subYearWithNoOverflow()                                                              Sub one year to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               addYearsNoOverflow(int $value = 1)                                                   Add years (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               addYearNoOverflow()                                                                  Add one year to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               subYearsNoOverflow(int $value = 1)                                                   Sub years (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               subYearNoOverflow()                                                                  Sub one year to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               addMonths(int $value = 1)                                                            Add months (the $value count passed in) to the instance (using date interval).
 * @method        $this               addMonth()                                                                           Add one month to the instance (using date interval).
 * @method        $this               subMonths(int $value = 1)                                                            Sub months (the $value count passed in) to the instance (using date interval).
 * @method        $this               subMonth()                                                                           Sub one month to the instance (using date interval).
 * @method        $this               addMonthsWithOverflow(int $value = 1)                                                Add months (the $value count passed in) to the instance (using date interval) with overflow explicitly allowed.
 * @method        $this               addMonthWithOverflow()                                                               Add one month to the instance (using date interval) with overflow explicitly allowed.
 * @method        $this               subMonthsWithOverflow(int $value = 1)                                                Sub months (the $value count passed in) to the instance (using date interval) with overflow explicitly allowed.
 * @method        $this               subMonthWithOverflow()                                                               Sub one month to the instance (using date interval) with overflow explicitly allowed.
 * @method        $this               addMonthsWithoutOverflow(int $value = 1)                                             Add months (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               addMonthWithoutOverflow()                                                            Add one month to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               subMonthsWithoutOverflow(int $value = 1)                                             Sub months (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               subMonthWithoutOverflow()                                                            Sub one month to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               addMonthsWithNoOverflow(int $value = 1)                                              Add months (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               addMonthWithNoOverflow()                                                             Add one month to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               subMonthsWithNoOverflow(int $value = 1)                                              Sub months (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               subMonthWithNoOverflow()                                                             Sub one month to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               addMonthsNoOverflow(int $value = 1)                                                  Add months (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               addMonthNoOverflow()                                                                 Add one month to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               subMonthsNoOverflow(int $value = 1)                                                  Sub months (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               subMonthNoOverflow()                                                                 Sub one month to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               addDays(int $value = 1)                                                              Add days (the $value count passed in) to the instance (using date interval).
 * @method        $this               addDay()                                                                             Add one day to the instance (using date interval).
 * @method        $this               subDays(int $value = 1)                                                              Sub days (the $value count passed in) to the instance (using date interval).
 * @method        $this               subDay()                                                                             Sub one day to the instance (using date interval).
 * @method        $this               addHours(int $value = 1)                                                             Add hours (the $value count passed in) to the instance (using date interval).
 * @method        $this               addHour()                                                                            Add one hour to the instance (using date interval).
 * @method        $this               subHours(int $value = 1)                                                             Sub hours (the $value count passed in) to the instance (using date interval).
 * @method        $this               subHour()                                                                            Sub one hour to the instance (using date interval).
 * @method        $this               addMinutes(int $value = 1)                                                           Add minutes (the $value count passed in) to the instance (using date interval).
 * @method        $this               addMinute()                                                                          Add one minute to the instance (using date interval).
 * @method        $this               subMinutes(int $value = 1)                                                           Sub minutes (the $value count passed in) to the instance (using date interval).
 * @method        $this               subMinute()                                                                          Sub one minute to the instance (using date interval).
 * @method        $this               addSeconds(int $value = 1)                                                           Add seconds (the $value count passed in) to the instance (using date interval).
 * @method        $this               addSecond()                                                                          Add one second to the instance (using date interval).
 * @method        $this               subSeconds(int $value = 1)                                                           Sub seconds (the $value count passed in) to the instance (using date interval).
 * @method        $this               subSecond()                                                                          Sub one second to the instance (using date interval).
 * @method        $this               addMillis(int $value = 1)                                                            Add milliseconds (the $value count passed in) to the instance (using date interval).
 * @method        $this               addMilli()                                                                           Add one millisecond to the instance (using date interval).
 * @method        $this               subMillis(int $value = 1)                                                            Sub milliseconds (the $value count passed in) to the instance (using date interval).
 * @method        $this               subMilli()                                                                           Sub one millisecond to the instance (using date interval).
 * @method        $this               addMilliseconds(int $value = 1)                                                      Add milliseconds (the $value count passed in) to the instance (using date interval).
 * @method        $this               addMillisecond()                                                                     Add one millisecond to the instance (using date interval).
 * @method        $this               subMilliseconds(int $value = 1)                                                      Sub milliseconds (the $value count passed in) to the instance (using date interval).
 * @method        $this               subMillisecond()                                                                     Sub one millisecond to the instance (using date interval).
 * @method        $this               addMicros(int $value = 1)                                                            Add microseconds (the $value count passed in) to the instance (using date interval).
 * @method        $this               addMicro()                                                                           Add one microsecond to the instance (using date interval).
 * @method        $this               subMicros(int $value = 1)                                                            Sub microseconds (the $value count passed in) to the instance (using date interval).
 * @method        $this               subMicro()                                                                           Sub one microsecond to the instance (using date interval).
 * @method        $this               addMicroseconds(int $value = 1)                                                      Add microseconds (the $value count passed in) to the instance (using date interval).
 * @method        $this               addMicrosecond()                                                                     Add one microsecond to the instance (using date interval).
 * @method        $this               subMicroseconds(int $value = 1)                                                      Sub microseconds (the $value count passed in) to the instance (using date interval).
 * @method        $this               subMicrosecond()                                                                     Sub one microsecond to the instance (using date interval).
 * @method        $this               addMillennia(int $value = 1)                                                         Add millennia (the $value count passed in) to the instance (using date interval).
 * @method        $this               addMillennium()                                                                      Add one millennium to the instance (using date interval).
 * @method        $this               subMillennia(int $value = 1)                                                         Sub millennia (the $value count passed in) to the instance (using date interval).
 * @method        $this               subMillennium()                                                                      Sub one millennium to the instance (using date interval).
 * @method        $this               addMillenniaWithOverflow(int $value = 1)                                             Add millennia (the $value count passed in) to the instance (using date interval) with overflow explicitly allowed.
 * @method        $this               addMillenniumWithOverflow()                                                          Add one millennium to the instance (using date interval) with overflow explicitly allowed.
 * @method        $this               subMillenniaWithOverflow(int $value = 1)                                             Sub millennia (the $value count passed in) to the instance (using date interval) with overflow explicitly allowed.
 * @method        $this               subMillenniumWithOverflow()                                                          Sub one millennium to the instance (using date interval) with overflow explicitly allowed.
 * @method        $this               addMillenniaWithoutOverflow(int $value = 1)                                          Add millennia (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               addMillenniumWithoutOverflow()                                                       Add one millennium to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               subMillenniaWithoutOverflow(int $value = 1)                                          Sub millennia (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               subMillenniumWithoutOverflow()                                                       Sub one millennium to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               addMillenniaWithNoOverflow(int $value = 1)                                           Add millennia (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               addMillenniumWithNoOverflow()                                                        Add one millennium to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               subMillenniaWithNoOverflow(int $value = 1)                                           Sub millennia (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               subMillenniumWithNoOverflow()                                                        Sub one millennium to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               addMillenniaNoOverflow(int $value = 1)                                               Add millennia (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               addMillenniumNoOverflow()                                                            Add one millennium to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               subMillenniaNoOverflow(int $value = 1)                                               Sub millennia (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               subMillenniumNoOverflow()                                                            Sub one millennium to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               addCenturies(int $value = 1)                                                         Add centuries (the $value count passed in) to the instance (using date interval).
 * @method        $this               addCentury()                                                                         Add one century to the instance (using date interval).
 * @method        $this               subCenturies(int $value = 1)                                                         Sub centuries (the $value count passed in) to the instance (using date interval).
 * @method        $this               subCentury()                                                                         Sub one century to the instance (using date interval).
 * @method        $this               addCenturiesWithOverflow(int $value = 1)                                             Add centuries (the $value count passed in) to the instance (using date interval) with overflow explicitly allowed.
 * @method        $this               addCenturyWithOverflow()                                                             Add one century to the instance (using date interval) with overflow explicitly allowed.
 * @method        $this               subCenturiesWithOverflow(int $value = 1)                                             Sub centuries (the $value count passed in) to the instance (using date interval) with overflow explicitly allowed.
 * @method        $this               subCenturyWithOverflow()                                                             Sub one century to the instance (using date interval) with overflow explicitly allowed.
 * @method        $this               addCenturiesWithoutOverflow(int $value = 1)                                          Add centuries (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               addCenturyWithoutOverflow()                                                          Add one century to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               subCenturiesWithoutOverflow(int $value = 1)                                          Sub centuries (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               subCenturyWithoutOverflow()                                                          Sub one century to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               addCenturiesWithNoOverflow(int $value = 1)                                           Add centuries (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               addCenturyWithNoOverflow()                                                           Add one century to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               subCenturiesWithNoOverflow(int $value = 1)                                           Sub centuries (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               subCenturyWithNoOverflow()                                                           Sub one century to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               addCenturiesNoOverflow(int $value = 1)                                               Add centuries (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               addCenturyNoOverflow()                                                               Add one century to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               subCenturiesNoOverflow(int $value = 1)                                               Sub centuries (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               subCenturyNoOverflow()                                                               Sub one century to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               addDecades(int $value = 1)                                                           Add decades (the $value count passed in) to the instance (using date interval).
 * @method        $this               addDecade()                                                                          Add one decade to the instance (using date interval).
 * @method        $this               subDecades(int $value = 1)                                                           Sub decades (the $value count passed in) to the instance (using date interval).
 * @method        $this               subDecade()                                                                          Sub one decade to the instance (using date interval).
 * @method        $this               addDecadesWithOverflow(int $value = 1)                                               Add decades (the $value count passed in) to the instance (using date interval) with overflow explicitly allowed.
 * @method        $this               addDecadeWithOverflow()                                                              Add one decade to the instance (using date interval) with overflow explicitly allowed.
 * @method        $this               subDecadesWithOverflow(int $value = 1)                                               Sub decades (the $value count passed in) to the instance (using date interval) with overflow explicitly allowed.
 * @method        $this               subDecadeWithOverflow()                                                              Sub one decade to the instance (using date interval) with overflow explicitly allowed.
 * @method        $this               addDecadesWithoutOverflow(int $value = 1)                                            Add decades (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               addDecadeWithoutOverflow()                                                           Add one decade to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               subDecadesWithoutOverflow(int $value = 1)                                            Sub decades (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               subDecadeWithoutOverflow()                                                           Sub one decade to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               addDecadesWithNoOverflow(int $value = 1)                                             Add decades (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               addDecadeWithNoOverflow()                                                            Add one decade to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               subDecadesWithNoOverflow(int $value = 1)                                             Sub decades (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               subDecadeWithNoOverflow()                                                            Sub one decade to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               addDecadesNoOverflow(int $value = 1)                                                 Add decades (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               addDecadeNoOverflow()                                                                Add one decade to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               subDecadesNoOverflow(int $value = 1)                                                 Sub decades (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               subDecadeNoOverflow()                                                                Sub one decade to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               addQuarters(int $value = 1)                                                          Add quarters (the $value count passed in) to the instance (using date interval).
 * @method        $this               addQuarter()                                                                         Add one quarter to the instance (using date interval).
 * @method        $this               subQuarters(int $value = 1)                                                          Sub quarters (the $value count passed in) to the instance (using date interval).
 * @method        $this               subQuarter()                                                                         Sub one quarter to the instance (using date interval).
 * @method        $this               addQuartersWithOverflow(int $value = 1)                                              Add quarters (the $value count passed in) to the instance (using date interval) with overflow explicitly allowed.
 * @method        $this               addQuarterWithOverflow()                                                             Add one quarter to the instance (using date interval) with overflow explicitly allowed.
 * @method        $this               subQuartersWithOverflow(int $value = 1)                                              Sub quarters (the $value count passed in) to the instance (using date interval) with overflow explicitly allowed.
 * @method        $this               subQuarterWithOverflow()                                                             Sub one quarter to the instance (using date interval) with overflow explicitly allowed.
 * @method        $this               addQuartersWithoutOverflow(int $value = 1)                                           Add quarters (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               addQuarterWithoutOverflow()                                                          Add one quarter to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               subQuartersWithoutOverflow(int $value = 1)                                           Sub quarters (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               subQuarterWithoutOverflow()                                                          Sub one quarter to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               addQuartersWithNoOverflow(int $value = 1)                                            Add quarters (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               addQuarterWithNoOverflow()                                                           Add one quarter to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               subQuartersWithNoOverflow(int $value = 1)                                            Sub quarters (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               subQuarterWithNoOverflow()                                                           Sub one quarter to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               addQuartersNoOverflow(int $value = 1)                                                Add quarters (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               addQuarterNoOverflow()                                                               Add one quarter to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               subQuartersNoOverflow(int $value = 1)                                                Sub quarters (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               subQuarterNoOverflow()                                                               Sub one quarter to the instance (using date interval) with overflow explicitly forbidden.
 * @method        $this               addWeeks(int $value = 1)                                                             Add weeks (the $value count passed in) to the instance (using date interval).
 * @method        $this               addWeek()                                                                            Add one week to the instance (using date interval).
 * @method        $this               subWeeks(int $value = 1)                                                             Sub weeks (the $value count passed in) to the instance (using date interval).
 * @method        $this               subWeek()                                                                            Sub one week to the instance (using date interval).
 * @method        $this               addWeekdays(int $value = 1)                                                          Add weekdays (the $value count passed in) to the instance (using date interval).
 * @method        $this               addWeekday()                                                                         Add one weekday to the instance (using date interval).
 * @method        $this               subWeekdays(int $value = 1)                                                          Sub weekdays (the $value count passed in) to the instance (using date interval).
 * @method        $this               subWeekday()                                                                         Sub one weekday to the instance (using date interval).
 * @method        $this               addRealMicros(int $value = 1)                                                        Add microseconds (the $value count passed in) to the instance (using timestamp).
 * @method        $this               addRealMicro()                                                                       Add one microsecond to the instance (using timestamp).
 * @method        $this               subRealMicros(int $value = 1)                                                        Sub microseconds (the $value count passed in) to the instance (using timestamp).
 * @method        $this               subRealMicro()                                                                       Sub one microsecond to the instance (using timestamp).
 * @method        CarbonPeriod        microsUntil($endDate = null, int $factor = 1)                                        Return an iterable period from current date to given end (string, DateTime or Carbon instance) for each microsecond or every X microseconds if a factor is given.
 * @method        $this               addRealMicroseconds(int $value = 1)                                                  Add microseconds (the $value count passed in) to the instance (using timestamp).
 * @method        $this               addRealMicrosecond()                                                                 Add one microsecond to the instance (using timestamp).
 * @method        $this               subRealMicroseconds(int $value = 1)                                                  Sub microseconds (the $value count passed in) to the instance (using timestamp).
 * @method        $this               subRealMicrosecond()                                                                 Sub one microsecond to the instance (using timestamp).
 * @method        CarbonPeriod        microsecondsUntil($endDate = null, int $factor = 1)                                  Return an iterable period from current date to given end (string, DateTime or Carbon instance) for each microsecond or every X microseconds if a factor is given.
 * @method        $this               addRealMillis(int $value = 1)                                                        Add milliseconds (the $value count passed in) to the instance (using timestamp).
 * @method        $this               addRealMilli()                                                                       Add one millisecond to the instance (using timestamp).
 * @method        $this               subRealMillis(int $value = 1)                                                        Sub milliseconds (the $value count passed in) to the instance (using timestamp).
 * @method        $this               subRealMilli()                                                                       Sub one millisecond to the instance (using timestamp).
 * @method        CarbonPeriod        millisUntil($endDate = null, int $factor = 1)                                        Return an iterable period from current date to given end (string, DateTime or Carbon instance) for each millisecond or every X milliseconds if a factor is given.
 * @method        $this               addRealMilliseconds(int $value = 1)                                                  Add milliseconds (the $value count passed in) to the instance (using timestamp).
 * @method        $this               addRealMillisecond()                                                                 Add one millisecond to the instance (using timestamp).
 * @method        $this               subRealMilliseconds(int $value = 1)                                                  Sub milliseconds (the $value count passed in) to the instance (using timestamp).
 * @method        $this               subRealMillisecond()                                                                 Sub one millisecond to the instance (using timestamp).
 * @method        CarbonPeriod        millisecondsUntil($endDate = null, int $factor = 1)                                  Return an iterable period from current date to given end (string, DateTime or Carbon instance) for each millisecond or every X milliseconds if a factor is given.
 * @method        $this               addRealSeconds(int $value = 1)                                                       Add seconds (the $value count passed in) to the instance (using timestamp).
 * @method        $this               addRealSecond()                                                                      Add one second to the instance (using timestamp).
 * @method        $this               subRealSeconds(int $value = 1)                                                       Sub seconds (the $value count passed in) to the instance (using timestamp).
 * @method        $this               subRealSecond()                                                                      Sub one second to the instance (using timestamp).
 * @method        CarbonPeriod        secondsUntil($endDate = null, int $factor = 1)                                       Return an iterable period from current date to given end (string, DateTime or Carbon instance) for each second or every X seconds if a factor is given.
 * @method        $this               addRealMinutes(int $value = 1)                                                       Add minutes (the $value count passed in) to the instance (using timestamp).
 * @method        $this               addRealMinute()                                                                      Add one minute to the instance (using timestamp).
 * @method        $this               subRealMinutes(int $value = 1)                                                       Sub minutes (the $value count passed in) to the instance (using timestamp).
 * @method        $this               subRealMinute()                                                                      Sub one minute to the instance (using timestamp).
 * @method        CarbonPeriod        minutesUntil($endDate = null, int $factor = 1)                                       Return an iterable period from current date to given end (string, DateTime or Carbon instance) for each minute or every X minutes if a factor is given.
 * @method        $this               addRealHours(int $value = 1)                                                         Add hours (the $value count passed in) to the instance (using timestamp).
 * @method        $this               addRealHour()                                                                        Add one hour to the instance (using timestamp).
 * @method        $this               subRealHours(int $value = 1)                                                         Sub hours (the $value count passed in) to the instance (using timestamp).
 * @method        $this               subRealHour()                                                                        Sub one hour to the instance (using timestamp).
 * @method        CarbonPeriod        hoursUntil($endDate = null, int $factor = 1)                                         Return an iterable period from current date to given end (string, DateTime or Carbon instance) for each hour or every X hours if a factor is given.
 * @method        $this               addRealDays(int $value = 1)                                                          Add days (the $value count passed in) to the instance (using timestamp).
 * @method        $this               addRealDay()                                                                         Add one day to the instance (using timestamp).
 * @method        $this               subRealDays(int $value = 1)                                                          Sub days (the $value count passed in) to the instance (using timestamp).
 * @method        $this               subRealDay()                                                                         Sub one day to the instance (using timestamp).
 * @method        CarbonPeriod        daysUntil($endDate = null, int $factor = 1)                                          Return an iterable period from current date to given end (string, DateTime or Carbon instance) for each day or every X days if a factor is given.
 * @method        $this               addRealWeeks(int $value = 1)                                                         Add weeks (the $value count passed in) to the instance (using timestamp).
 * @method        $this               addRealWeek()                                                                        Add one week to the instance (using timestamp).
 * @method        $this               subRealWeeks(int $value = 1)                                                         Sub weeks (the $value count passed in) to the instance (using timestamp).
 * @method        $this               subRealWeek()                                                                        Sub one week to the instance (using timestamp).
 * @method        CarbonPeriod        weeksUntil($endDate = null, int $factor = 1)                                         Return an iterable period from current date to given end (string, DateTime or Carbon instance) for each week or every X weeks if a factor is given.
 * @method        $this               addRealMonths(int $value = 1)                                                        Add months (the $value count passed in) to the instance (using timestamp).
 * @method        $this               addRealMonth()                                                                       Add one month to the instance (using timestamp).
 * @method        $this               subRealMonths(int $value = 1)                                                        Sub months (the $value count passed in) to the instance (using timestamp).
 * @method        $this               subRealMonth()                                                                       Sub one month to the instance (using timestamp).
 * @method        CarbonPeriod        monthsUntil($endDate = null, int $factor = 1)                                        Return an iterable period from current date to given end (string, DateTime or Carbon instance) for each month or every X months if a factor is given.
 * @method        $this               addRealQuarters(int $value = 1)                                                      Add quarters (the $value count passed in) to the instance (using timestamp).
 * @method        $this               addRealQuarter()                                                                     Add one quarter to the instance (using timestamp).
 * @method        $this               subRealQuarters(int $value = 1)                                                      Sub quarters (the $value count passed in) to the instance (using timestamp).
 * @method        $this               subRealQuarter()                                                                     Sub one quarter to the instance (using timestamp).
 * @method        CarbonPeriod        quartersUntil($endDate = null, int $factor = 1)                                      Return an iterable period from current date to given end (string, DateTime or Carbon instance) for each quarter or every X quarters if a factor is given.
 * @method        $this               addRealYears(int $value = 1)                                                         Add years (the $value count passed in) to the instance (using timestamp).
 * @method        $this               addRealYear()                                                                        Add one year to the instance (using timestamp).
 * @method        $this               subRealYears(int $value = 1)                                                         Sub years (the $value count passed in) to the instance (using timestamp).
 * @method        $this               subRealYear()                                                                        Sub one year to the instance (using timestamp).
 * @method        CarbonPeriod        yearsUntil($endDate = null, int $factor = 1)                                         Return an iterable period from current date to given end (string, DateTime or Carbon instance) for each year or every X years if a factor is given.
 * @method        $this               addRealDecades(int $value = 1)                                                       Add decades (the $value count passed in) to the instance (using timestamp).
 * @method        $this               addRealDecade()                                                                      Add one decade to the instance (using timestamp).
 * @method        $this               subRealDecades(int $value = 1)                                                       Sub decades (the $value count passed in) to the instance (using timestamp).
 * @method        $this               subRealDecade()                                                                      Sub one decade to the instance (using timestamp).
 * @method        CarbonPeriod        decadesUntil($endDate = null, int $factor = 1)                                       Return an iterable period from current date to given end (string, DateTime or Carbon instance) for each decade or every X decades if a factor is given.
 * @method        $this               addRealCenturies(int $value = 1)                                                     Add centuries (the $value count passed in) to the instance (using timestamp).
 * @method        $this               addRealCentury()                                                                     Add one century to the instance (using timestamp).
 * @method        $this               subRealCenturies(int $value = 1)                                                     Sub centuries (the $value count passed in) to the instance (using timestamp).
 * @method        $this               subRealCentury()                                                                     Sub one century to the instance (using timestamp).
 * @method        CarbonPeriod        centuriesUntil($endDate = null, int $factor = 1)                                     Return an iterable period from current date to given end (string, DateTime or Carbon instance) for each century or every X centuries if a factor is given.
 * @method        $this               addRealMillennia(int $value = 1)                                                     Add millennia (the $value count passed in) to the instance (using timestamp).
 * @method        $this               addRealMillennium()                                                                  Add one millennium to the instance (using timestamp).
 * @method        $this               subRealMillennia(int $value = 1)                                                     Sub millennia (the $value count passed in) to the instance (using timestamp).
 * @method        $this               subRealMillennium()                                                                  Sub one millennium to the instance (using timestamp).
 * @method        CarbonPeriod        millenniaUntil($endDate = null, int $factor = 1)                                     Return an iterable period from current date to given end (string, DateTime or Carbon instance) for each millennium or every X millennia if a factor is given.
 * @method        $this               roundYear(float $precision = 1, string $function = "round")                          Round the current instance year with given precision using the given function.
 * @method        $this               roundYears(float $precision = 1, string $function = "round")                         Round the current instance year with given precision using the given function.
 * @method        $this               floorYear(float $precision = 1)                                                      Truncate the current instance year with given precision.
 * @method        $this               floorYears(float $precision = 1)                                                     Truncate the current instance year with given precision.
 * @method        $this               ceilYear(float $precision = 1)                                                       Ceil the current instance year with given precision.
 * @method        $this               ceilYears(float $precision = 1)                                                      Ceil the current instance year with given precision.
 * @method        $this               roundMonth(float $precision = 1, string $function = "round")                         Round the current instance month with given precision using the given function.
 * @method        $this               roundMonths(float $precision = 1, string $function = "round")                        Round the current instance month with given precision using the given function.
 * @method        $this               floorMonth(float $precision = 1)                                                     Truncate the current instance month with given precision.
 * @method        $this               floorMonths(float $precision = 1)                                                    Truncate the current instance month with given precision.
 * @method        $this               ceilMonth(float $precision = 1)                                                      Ceil the current instance month with given precision.
 * @method        $this               ceilMonths(float $precision = 1)                                                     Ceil the current instance month with given precision.
 * @method        $this               roundDay(float $precision = 1, string $function = "round")                           Round the current instance day with given precision using the given function.
 * @method        $this               roundDays(float $precision = 1, string $function = "round")                          Round the current instance day with given precision using the given function.
 * @method        $this               floorDay(float $precision = 1)                                                       Truncate the current instance day with given precision.
 * @method        $this               floorDays(float $precision = 1)                                                      Truncate the current instance day with given precision.
 * @method        $this               ceilDay(float $precision = 1)                                                        Ceil the current instance day with given precision.
 * @method        $this               ceilDays(float $precision = 1)                                                       Ceil the current instance day with given precision.
 * @method        $this               roundHour(float $precision = 1, string $function = "round")                          Round the current instance hour with given precision using the given function.
 * @method        $this               roundHours(float $precision = 1, string $function = "round")                         Round the current instance hour with given precision using the given function.
 * @method        $this               floorHour(float $precision = 1)                                                      Truncate the current instance hour with given precision.
 * @method        $this               floorHours(float $precision = 1)                                                     Truncate the current instance hour with given precision.
 * @method        $this               ceilHour(float $precision = 1)                                                       Ceil the current instance hour with given precision.
 * @method        $this               ceilHours(float $precision = 1)                                                      Ceil the current instance hour with given precision.
 * @method        $this               roundMinute(float $precision = 1, string $function = "round")                        Round the current instance minute with given precision using the given function.
 * @method        $this               roundMinutes(float $precision = 1, string $function = "round")                       Round the current instance minute with given precision using the given function.
 * @method        $this               floorMinute(float $precision = 1)                                                    Truncate the current instance minute with given precision.
 * @method        $this               floorMinutes(float $precision = 1)                                                   Truncate the current instance minute with given precision.
 * @method        $this               ceilMinute(float $precision = 1)                                                     Ceil the current instance minute with given precision.
 * @method        $this               ceilMinutes(float $precision = 1)                                                    Ceil the current instance minute with given precision.
 * @method        $this               roundSecond(float $precision = 1, string $function = "round")                        Round the current instance second with given precision using the given function.
 * @method        $this               roundSeconds(float $precision = 1, string $function = "round")                       Round the current instance second with given precision using the given function.
 * @method        $this               floorSecond(float $precision = 1)                                                    Truncate the current instance second with given precision.
 * @method        $this               floorSeconds(float $precision = 1)                                                   Truncate the current instance second with given precision.
 * @method        $this               ceilSecond(float $precision = 1)                                                     Ceil the current instance second with given precision.
 * @method        $this               ceilSeconds(float $precision = 1)                                                    Ceil the current instance second with given precision.
 * @method        $this               roundMillennium(float $precision = 1, string $function = "round")                    Round the current instance millennium with given precision using the given function.
 * @method        $this               roundMillennia(float $precision = 1, string $function = "round")                     Round the current instance millennium with given precision using the given function.
 * @method        $this               floorMillennium(float $precision = 1)                                                Truncate the current instance millennium with given precision.
 * @method        $this               floorMillennia(float $precision = 1)                                                 Truncate the current instance millennium with given precision.
 * @method        $this               ceilMillennium(float $precision = 1)                                                 Ceil the current instance millennium with given precision.
 * @method        $this               ceilMillennia(float $precision = 1)                                                  Ceil the current instance millennium with given precision.
 * @method        $this               roundCentury(float $precision = 1, string $function = "round")                       Round the current instance century with given precision using the given function.
 * @method        $this               roundCenturies(float $precision = 1, string $function = "round")                     Round the current instance century with given precision using the given function.
 * @method        $this               floorCentury(float $precision = 1)                                                   Truncate the current instance century with given precision.
 * @method        $this               floorCenturies(float $precision = 1)                                                 Truncate the current instance century with given precision.
 * @method        $this               ceilCentury(float $precision = 1)                                                    Ceil the current instance century with given precision.
 * @method        $this               ceilCenturies(float $precision = 1)                                                  Ceil the current instance century with given precision.
 * @method        $this               roundDecade(float $precision = 1, string $function = "round")                        Round the current instance decade with given precision using the given function.
 * @method        $this               roundDecades(float $precision = 1, string $function = "round")                       Round the current instance decade with given precision using the given function.
 * @method        $this               floorDecade(float $precision = 1)                                                    Truncate the current instance decade with given precision.
 * @method        $this               floorDecades(float $precision = 1)                                                   Truncate the current instance decade with given precision.
 * @method        $this               ceilDecade(float $precision = 1)                                                     Ceil the current instance decade with given precision.
 * @method        $this               ceilDecades(float $precision = 1)                                                    Ceil the current instance decade with given precision.
 * @method        $this               roundQuarter(float $precision = 1, string $function = "round")                       Round the current instance quarter with given precision using the given function.
 * @method        $this               roundQuarters(float $precision = 1, string $function = "round")                      Round the current instance quarter with given precision using the given function.
 * @method        $this               floorQuarter(float $precision = 1)                                                   Truncate the current instance quarter with given precision.
 * @method        $this               floorQuarters(float $precision = 1)                                                  Truncate the current instance quarter with given precision.
 * @method        $this               ceilQuarter(float $precision = 1)                                                    Ceil the current instance quarter with given precision.
 * @method        $this               ceilQuarters(float $precision = 1)                                                   Ceil the current instance quarter with given precision.
 * @method        $this               roundMillisecond(float $precision = 1, string $function = "round")                   Round the current instance millisecond with given precision using the given function.
 * @method        $this               roundMilliseconds(float $precision = 1, string $function = "round")                  Round the current instance millisecond with given precision using the given function.
 * @method        $this               floorMillisecond(float $precision = 1)                                               Truncate the current instance millisecond with given precision.
 * @method        $this               floorMilliseconds(float $precision = 1)                                              Truncate the current instance millisecond with given precision.
 * @method        $this               ceilMillisecond(float $precision = 1)                                                Ceil the current instance millisecond with given precision.
 * @method        $this               ceilMilliseconds(float $precision = 1)                                               Ceil the current instance millisecond with given precision.
 * @method        $this               roundMicrosecond(float $precision = 1, string $function = "round")                   Round the current instance microsecond with given precision using the given function.
 * @method        $this               roundMicroseconds(float $precision = 1, string $function = "round")                  Round the current instance microsecond with given precision using the given function.
 * @method        $this               floorMicrosecond(float $precision = 1)                                               Truncate the current instance microsecond with given precision.
 * @method        $this               floorMicroseconds(float $precision = 1)                                              Truncate the current instance microsecond with given precision.
 * @method        $this               ceilMicrosecond(float $precision = 1)                                                Ceil the current instance microsecond with given precision.
 * @method        $this               ceilMicroseconds(float $precision = 1)                                               Ceil the current instance microsecond with given precision.
 * @method        string              shortAbsoluteDiffForHumans(DateTimeInterface $other = null, int $parts = 1)          Get the difference (short format, 'Absolute' mode) in a human readable format in the current locale. ($other and $parts parameters can be swapped.)
 * @method        string              longAbsoluteDiffForHumans(DateTimeInterface $other = null, int $parts = 1)           Get the difference (long format, 'Absolute' mode) in a human readable format in the current locale. ($other and $parts parameters can be swapped.)
 * @method        string              shortRelativeDiffForHumans(DateTimeInterface $other = null, int $parts = 1)          Get the difference (short format, 'Relative' mode) in a human readable format in the current locale. ($other and $parts parameters can be swapped.)
 * @method        string              longRelativeDiffForHumans(DateTimeInterface $other = null, int $parts = 1)           Get the difference (long format, 'Relative' mode) in a human readable format in the current locale. ($other and $parts parameters can be swapped.)
 * @method        string              shortRelativeToNowDiffForHumans(DateTimeInterface $other = null, int $parts = 1)     Get the difference (short format, 'RelativeToNow' mode) in a human readable format in the current locale. ($other and $parts parameters can be swapped.)
 * @method        string              longRelativeToNowDiffForHumans(DateTimeInterface $other = null, int $parts = 1)      Get the difference (long format, 'RelativeToNow' mode) in a human readable format in the current locale. ($other and $parts parameters can be swapped.)
 * @method        string              shortRelativeToOtherDiffForHumans(DateTimeInterface $other = null, int $parts = 1)   Get the difference (short format, 'RelativeToOther' mode) in a human readable format in the current locale. ($other and $parts parameters can be swapped.)
 * @method        string              longRelativeToOtherDiffForHumans(DateTimeInterface $other = null, int $parts = 1)    Get the difference (long format, 'RelativeToOther' mode) in a human readable format in the current locale. ($other and $parts parameters can be swapped.)
 * @method        static Carbon|false createFromFormat(string $format, string $time, string|DateTimeZone $timezone = null) Parse a string into a new Carbon object according to the specified format.
 * @method        static Carbon       __set_state(array $array)                                                            https://php.net/manual/en/datetime.set-state.php
 */

final class QubusDateTime extends Carbon implements DateTimeInterface
{
}
