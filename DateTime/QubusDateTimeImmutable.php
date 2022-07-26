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

use Carbon\CarbonImmutable;
use DateTimeInterface;

/**
 * @see https://carbon.nesbot.com/docs/
 * @see https://github.com/briannesbitt/Carbon/blob/master/src/Carbon/CarbonImmutable.php
 *
 * @property      int                          $year
 * @property      int                          $yearIso
 * @property      int                          $month
 * @property      int                          $day
 * @property      int                          $hour
 * @property      int                          $minute
 * @property      int                          $second
 * @property      int                          $micro
 * @property      int                          $microsecond
 * @property      int|float|string             $timestamp                                                                           seconds since the Unix Epoch
 * @property      string                       $englishDayOfWeek                                                                    the day of week in English
 * @property      string                       $shortEnglishDayOfWeek                                                               the abbreviated day of week in English
 * @property      string                       $englishMonth                                                                        the month in English
 * @property      string                       $shortEnglishMonth                                                                   the abbreviated month in English
 * @property      string                       $localeDayOfWeek                                                                     the day of week in current locale LC_TIME
 * @property      string                       $shortLocaleDayOfWeek                                                                the abbreviated day of week in current locale LC_TIME
 * @property      string                       $localeMonth                                                                         the month in current locale LC_TIME
 * @property      string                       $shortLocaleMonth                                                                    the abbreviated month in current locale LC_TIME
 * @property      int                          $milliseconds
 * @property      int                          $millisecond
 * @property      int                          $milli
 * @property      int                          $week                                                                                1 through 53
 * @property      int                          $isoWeek                                                                             1 through 53
 * @property      int                          $weekYear                                                                            year according to week format
 * @property      int                          $isoWeekYear                                                                         year according to ISO week format
 * @property      int                          $dayOfYear                                                                           1 through 366
 * @property      int                          $age                                                                                 does a diffInYears() with default parameters
 * @property      int                          $offset                                                                              the timezone offset in seconds from UTC
 * @property      int                          $offsetMinutes                                                                       the timezone offset in minutes from UTC
 * @property      int                          $offsetHours                                                                         the timezone offset in hours from UTC
 * @property      CarbonTimeZone               $timezone                                                                            the current timezone
 * @property      CarbonTimeZone               $tz                                                                                  alias of $timezone
 * @property-read int                          $dayOfWeek                                                                           0 (for Sunday) through 6 (for Saturday)
 * @property-read int                          $dayOfWeekIso                                                                        1 (for Monday) through 7 (for Sunday)
 * @property-read int                          $weekOfYear                                                                          ISO-8601 week number of year, weeks starting on Monday
 * @property-read int                          $daysInMonth                                                                         number of days in the given month
 * @property-read string                       $latinMeridiem                                                                       "am"/"pm" (Ante meridiem or Post meridiem latin lowercase mark)
 * @property-read string                       $latinUpperMeridiem                                                                  "AM"/"PM" (Ante meridiem or Post meridiem latin uppercase mark)
 * @property-read string                       $timezoneAbbreviatedName                                                             the current timezone abbreviated name
 * @property-read string                       $tzAbbrName                                                                          alias of $timezoneAbbreviatedName
 * @property-read string                       $dayName                                                                             long name of weekday translated according to Carbon locale, in english if no translation available for current language
 * @property-read string                       $shortDayName                                                                        short name of weekday translated according to Carbon locale, in english if no translation available for current language
 * @property-read string                       $minDayName                                                                          very short name of weekday translated according to Carbon locale, in english if no translation available for current language
 * @property-read string                       $monthName                                                                           long name of month translated according to Carbon locale, in english if no translation available for current language
 * @property-read string                       $shortMonthName                                                                      short name of month translated according to Carbon locale, in english if no translation available for current language
 * @property-read string                       $meridiem                                                                            lowercase meridiem mark translated according to Carbon locale, in latin if no translation available for current language
 * @property-read string                       $upperMeridiem                                                                       uppercase meridiem mark translated according to Carbon locale, in latin if no translation available for current language
 * @property-read int                          $noZeroHour                                                                          current hour from 1 to 24
 * @property-read int                          $weeksInYear                                                                         51 through 53
 * @property-read int                          $isoWeeksInYear                                                                      51 through 53
 * @property-read int                          $weekOfMonth                                                                         1 through 5
 * @property-read int                          $weekNumberInMonth                                                                   1 through 5
 * @property-read int                          $firstWeekDay                                                                        0 through 6
 * @property-read int                          $lastWeekDay                                                                         0 through 6
 * @property-read int                          $daysInYear                                                                          365 or 366
 * @property-read int                          $quarter                                                                             the quarter of this instance, 1 - 4
 * @property-read int                          $decade                                                                              the decade of this instance
 * @property-read int                          $century                                                                             the century of this instance
 * @property-read int                          $millennium                                                                          the millennium of this instance
 * @property-read bool                         $dst                                                                                 daylight savings time indicator, true if DST, false otherwise
 * @property-read bool                         $local                                                                               checks if the timezone is local, true if local, false otherwise
 * @property-read bool                         $utc                                                                                 checks if the timezone is UTC, true if UTC, false otherwise
 * @property-read string                       $timezoneName                                                                        the current timezone name
 * @property-read string                       $tzName                                                                              alias of $timezoneName
 * @property-read string                       $locale                                                                              locale of the current instance
 *
 * @method        bool                         isUtc()                                                                              Check if the current instance has UTC timezone. (Both isUtc and isUTC cases are valid.)
 * @method        bool                         isLocal()                                                                            Check if the current instance has non-UTC timezone.
 * @method        bool                         isValid()                                                                            Check if the current instance is a valid date.
 * @method        bool                         isDST()                                                                              Check if the current instance is in a daylight saving time.
 * @method        bool                         isSunday()                                                                           Checks if the instance day is sunday.
 * @method        bool                         isMonday()                                                                           Checks if the instance day is monday.
 * @method        bool                         isTuesday()                                                                          Checks if the instance day is tuesday.
 * @method        bool                         isWednesday()                                                                        Checks if the instance day is wednesday.
 * @method        bool                         isThursday()                                                                         Checks if the instance day is thursday.
 * @method        bool                         isFriday()                                                                           Checks if the instance day is friday.
 * @method        bool                         isSaturday()                                                                         Checks if the instance day is saturday.
 * @method        bool                         isSameYear(Carbon|DateTimeInterface|string|null $date = null)                        Checks if the given date is in the same year as the instance. If null passed, compare to now (with the same timezone).
 * @method        bool                         isCurrentYear()                                                                      Checks if the instance is in the same year as the current moment.
 * @method        bool                         isNextYear()                                                                         Checks if the instance is in the same year as the current moment next year.
 * @method        bool                         isLastYear()                                                                         Checks if the instance is in the same year as the current moment last year.
 * @method        bool                         isSameWeek(Carbon|DateTimeInterface|string|null $date = null)                        Checks if the given date is in the same week as the instance. If null passed, compare to now (with the same timezone).
 * @method        bool                         isCurrentWeek()                                                                      Checks if the instance is in the same week as the current moment.
 * @method        bool                         isNextWeek()                                                                         Checks if the instance is in the same week as the current moment next week.
 * @method        bool                         isLastWeek()                                                                         Checks if the instance is in the same week as the current moment last week.
 * @method        bool                         isSameDay(Carbon|DateTimeInterface|string|null $date = null)                         Checks if the given date is in the same day as the instance. If null passed, compare to now (with the same timezone).
 * @method        bool                         isCurrentDay()                                                                       Checks if the instance is in the same day as the current moment.
 * @method        bool                         isNextDay()                                                                          Checks if the instance is in the same day as the current moment next day.
 * @method        bool                         isLastDay()                                                                          Checks if the instance is in the same day as the current moment last day.
 * @method        bool                         isSameHour(Carbon|DateTimeInterface|string|null $date = null)                        Checks if the given date is in the same hour as the instance. If null passed, compare to now (with the same timezone).
 * @method        bool                         isCurrentHour()                                                                      Checks if the instance is in the same hour as the current moment.
 * @method        bool                         isNextHour()                                                                         Checks if the instance is in the same hour as the current moment next hour.
 * @method        bool                         isLastHour()                                                                         Checks if the instance is in the same hour as the current moment last hour.
 * @method        bool                         isSameMinute(Carbon|DateTimeInterface|string|null $date = null)                      Checks if the given date is in the same minute as the instance. If null passed, compare to now (with the same timezone).
 * @method        bool                         isCurrentMinute()                                                                    Checks if the instance is in the same minute as the current moment.
 * @method        bool                         isNextMinute()                                                                       Checks if the instance is in the same minute as the current moment next minute.
 * @method        bool                         isLastMinute()                                                                       Checks if the instance is in the same minute as the current moment last minute.
 * @method        bool                         isSameSecond(Carbon|DateTimeInterface|string|null $date = null)                      Checks if the given date is in the same second as the instance. If null passed, compare to now (with the same timezone).
 * @method        bool                         isCurrentSecond()                                                                    Checks if the instance is in the same second as the current moment.
 * @method        bool                         isNextSecond()                                                                       Checks if the instance is in the same second as the current moment next second.
 * @method        bool                         isLastSecond()                                                                       Checks if the instance is in the same second as the current moment last second.
 * @method        bool                         isSameMicro(Carbon|DateTimeInterface|string|null $date = null)                       Checks if the given date is in the same microsecond as the instance. If null passed, compare to now (with the same timezone).
 * @method        bool                         isCurrentMicro()                                                                     Checks if the instance is in the same microsecond as the current moment.
 * @method        bool                         isNextMicro()                                                                        Checks if the instance is in the same microsecond as the current moment next microsecond.
 * @method        bool                         isLastMicro()                                                                        Checks if the instance is in the same microsecond as the current moment last microsecond.
 * @method        bool                         isSameMicrosecond(Carbon|DateTimeInterface|string|null $date = null)                 Checks if the given date is in the same microsecond as the instance. If null passed, compare to now (with the same timezone).
 * @method        bool                         isCurrentMicrosecond()                                                               Checks if the instance is in the same microsecond as the current moment.
 * @method        bool                         isNextMicrosecond()                                                                  Checks if the instance is in the same microsecond as the current moment next microsecond.
 * @method        bool                         isLastMicrosecond()                                                                  Checks if the instance is in the same microsecond as the current moment last microsecond.
 * @method        bool                         isCurrentMonth()                                                                     Checks if the instance is in the same month as the current moment.
 * @method        bool                         isNextMonth()                                                                        Checks if the instance is in the same month as the current moment next month.
 * @method        bool                         isLastMonth()                                                                        Checks if the instance is in the same month as the current moment last month.
 * @method        bool                         isCurrentQuarter()                                                                   Checks if the instance is in the same quarter as the current moment.
 * @method        bool                         isNextQuarter()                                                                      Checks if the instance is in the same quarter as the current moment next quarter.
 * @method        bool                         isLastQuarter()                                                                      Checks if the instance is in the same quarter as the current moment last quarter.
 * @method        bool                         isSameDecade(Carbon|DateTimeInterface|string|null $date = null)                      Checks if the given date is in the same decade as the instance. If null passed, compare to now (with the same timezone).
 * @method        bool                         isCurrentDecade()                                                                    Checks if the instance is in the same decade as the current moment.
 * @method        bool                         isNextDecade()                                                                       Checks if the instance is in the same decade as the current moment next decade.
 * @method        bool                         isLastDecade()                                                                       Checks if the instance is in the same decade as the current moment last decade.
 * @method        bool                         isSameCentury(Carbon|DateTimeInterface|string|null $date = null)                     Checks if the given date is in the same century as the instance. If null passed, compare to now (with the same timezone).
 * @method        bool                         isCurrentCentury()                                                                   Checks if the instance is in the same century as the current moment.
 * @method        bool                         isNextCentury()                                                                      Checks if the instance is in the same century as the current moment next century.
 * @method        bool                         isLastCentury()                                                                      Checks if the instance is in the same century as the current moment last century.
 * @method        bool                         isSameMillennium(Carbon|DateTimeInterface|string|null $date = null)                  Checks if the given date is in the same millennium as the instance. If null passed, compare to now (with the same timezone).
 * @method        bool                         isCurrentMillennium()                                                                Checks if the instance is in the same millennium as the current moment.
 * @method        bool                         isNextMillennium()                                                                   Checks if the instance is in the same millennium as the current moment next millennium.
 * @method        bool                         isLastMillennium()                                                                   Checks if the instance is in the same millennium as the current moment last millennium.
 * @method        CarbonImmutable              years(int $value)                                                                    Set current instance year to the given value.
 * @method        CarbonImmutable              year(int $value)                                                                     Set current instance year to the given value.
 * @method        CarbonImmutable              setYears(int $value)                                                                 Set current instance year to the given value.
 * @method        CarbonImmutable              setYear(int $value)                                                                  Set current instance year to the given value.
 * @method        CarbonImmutable              months(int $value)                                                                   Set current instance month to the given value.
 * @method        CarbonImmutable              month(int $value)                                                                    Set current instance month to the given value.
 * @method        CarbonImmutable              setMonths(int $value)                                                                Set current instance month to the given value.
 * @method        CarbonImmutable              setMonth(int $value)                                                                 Set current instance month to the given value.
 * @method        CarbonImmutable              days(int $value)                                                                     Set current instance day to the given value.
 * @method        CarbonImmutable              day(int $value)                                                                      Set current instance day to the given value.
 * @method        CarbonImmutable              setDays(int $value)                                                                  Set current instance day to the given value.
 * @method        CarbonImmutable              setDay(int $value)                                                                   Set current instance day to the given value.
 * @method        CarbonImmutable              hours(int $value)                                                                    Set current instance hour to the given value.
 * @method        CarbonImmutable              hour(int $value)                                                                     Set current instance hour to the given value.
 * @method        CarbonImmutable              setHours(int $value)                                                                 Set current instance hour to the given value.
 * @method        CarbonImmutable              setHour(int $value)                                                                  Set current instance hour to the given value.
 * @method        CarbonImmutable              minutes(int $value)                                                                  Set current instance minute to the given value.
 * @method        CarbonImmutable              minute(int $value)                                                                   Set current instance minute to the given value.
 * @method        CarbonImmutable              setMinutes(int $value)                                                               Set current instance minute to the given value.
 * @method        CarbonImmutable              setMinute(int $value)                                                                Set current instance minute to the given value.
 * @method        CarbonImmutable              seconds(int $value)                                                                  Set current instance second to the given value.
 * @method        CarbonImmutable              second(int $value)                                                                   Set current instance second to the given value.
 * @method        CarbonImmutable              setSeconds(int $value)                                                               Set current instance second to the given value.
 * @method        CarbonImmutable              setSecond(int $value)                                                                Set current instance second to the given value.
 * @method        CarbonImmutable              millis(int $value)                                                                   Set current instance millisecond to the given value.
 * @method        CarbonImmutable              milli(int $value)                                                                    Set current instance millisecond to the given value.
 * @method        CarbonImmutable              setMillis(int $value)                                                                Set current instance millisecond to the given value.
 * @method        CarbonImmutable              setMilli(int $value)                                                                 Set current instance millisecond to the given value.
 * @method        CarbonImmutable              milliseconds(int $value)                                                             Set current instance millisecond to the given value.
 * @method        CarbonImmutable              millisecond(int $value)                                                              Set current instance millisecond to the given value.
 * @method        CarbonImmutable              setMilliseconds(int $value)                                                          Set current instance millisecond to the given value.
 * @method        CarbonImmutable              setMillisecond(int $value)                                                           Set current instance millisecond to the given value.
 * @method        CarbonImmutable              micros(int $value)                                                                   Set current instance microsecond to the given value.
 * @method        CarbonImmutable              micro(int $value)                                                                    Set current instance microsecond to the given value.
 * @method        CarbonImmutable              setMicros(int $value)                                                                Set current instance microsecond to the given value.
 * @method        CarbonImmutable              setMicro(int $value)                                                                 Set current instance microsecond to the given value.
 * @method        CarbonImmutable              microseconds(int $value)                                                             Set current instance microsecond to the given value.
 * @method        CarbonImmutable              microsecond(int $value)                                                              Set current instance microsecond to the given value.
 * @method        CarbonImmutable              setMicroseconds(int $value)                                                          Set current instance microsecond to the given value.
 * @method        CarbonImmutable              setMicrosecond(int $value)                                                           Set current instance microsecond to the given value.
 * @method        CarbonImmutable              addYears(int $value = 1)                                                             Add years (the $value count passed in) to the instance (using date interval).
 * @method        CarbonImmutable              addYear()                                                                            Add one year to the instance (using date interval).
 * @method        CarbonImmutable              subYears(int $value = 1)                                                             Sub years (the $value count passed in) to the instance (using date interval).
 * @method        CarbonImmutable              subYear()                                                                            Sub one year to the instance (using date interval).
 * @method        CarbonImmutable              addYearsWithOverflow(int $value = 1)                                                 Add years (the $value count passed in) to the instance (using date interval) with overflow explicitly allowed.
 * @method        CarbonImmutable              addYearWithOverflow()                                                                Add one year to the instance (using date interval) with overflow explicitly allowed.
 * @method        CarbonImmutable              subYearsWithOverflow(int $value = 1)                                                 Sub years (the $value count passed in) to the instance (using date interval) with overflow explicitly allowed.
 * @method        CarbonImmutable              subYearWithOverflow()                                                                Sub one year to the instance (using date interval) with overflow explicitly allowed.
 * @method        CarbonImmutable              addYearsWithoutOverflow(int $value = 1)                                              Add years (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              addYearWithoutOverflow()                                                             Add one year to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              subYearsWithoutOverflow(int $value = 1)                                              Sub years (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              subYearWithoutOverflow()                                                             Sub one year to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              addYearsWithNoOverflow(int $value = 1)                                               Add years (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              addYearWithNoOverflow()                                                              Add one year to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              subYearsWithNoOverflow(int $value = 1)                                               Sub years (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              subYearWithNoOverflow()                                                              Sub one year to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              addYearsNoOverflow(int $value = 1)                                                   Add years (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              addYearNoOverflow()                                                                  Add one year to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              subYearsNoOverflow(int $value = 1)                                                   Sub years (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              subYearNoOverflow()                                                                  Sub one year to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              addMonths(int $value = 1)                                                            Add months (the $value count passed in) to the instance (using date interval).
 * @method        CarbonImmutable              addMonth()                                                                           Add one month to the instance (using date interval).
 * @method        CarbonImmutable              subMonths(int $value = 1)                                                            Sub months (the $value count passed in) to the instance (using date interval).
 * @method        CarbonImmutable              subMonth()                                                                           Sub one month to the instance (using date interval).
 * @method        CarbonImmutable              addMonthsWithOverflow(int $value = 1)                                                Add months (the $value count passed in) to the instance (using date interval) with overflow explicitly allowed.
 * @method        CarbonImmutable              addMonthWithOverflow()                                                               Add one month to the instance (using date interval) with overflow explicitly allowed.
 * @method        CarbonImmutable              subMonthsWithOverflow(int $value = 1)                                                Sub months (the $value count passed in) to the instance (using date interval) with overflow explicitly allowed.
 * @method        CarbonImmutable              subMonthWithOverflow()                                                               Sub one month to the instance (using date interval) with overflow explicitly allowed.
 * @method        CarbonImmutable              addMonthsWithoutOverflow(int $value = 1)                                             Add months (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              addMonthWithoutOverflow()                                                            Add one month to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              subMonthsWithoutOverflow(int $value = 1)                                             Sub months (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              subMonthWithoutOverflow()                                                            Sub one month to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              addMonthsWithNoOverflow(int $value = 1)                                              Add months (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              addMonthWithNoOverflow()                                                             Add one month to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              subMonthsWithNoOverflow(int $value = 1)                                              Sub months (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              subMonthWithNoOverflow()                                                             Sub one month to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              addMonthsNoOverflow(int $value = 1)                                                  Add months (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              addMonthNoOverflow()                                                                 Add one month to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              subMonthsNoOverflow(int $value = 1)                                                  Sub months (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              subMonthNoOverflow()                                                                 Sub one month to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              addDays(int $value = 1)                                                              Add days (the $value count passed in) to the instance (using date interval).
 * @method        CarbonImmutable              addDay()                                                                             Add one day to the instance (using date interval).
 * @method        CarbonImmutable              subDays(int $value = 1)                                                              Sub days (the $value count passed in) to the instance (using date interval).
 * @method        CarbonImmutable              subDay()                                                                             Sub one day to the instance (using date interval).
 * @method        CarbonImmutable              addHours(int $value = 1)                                                             Add hours (the $value count passed in) to the instance (using date interval).
 * @method        CarbonImmutable              addHour()                                                                            Add one hour to the instance (using date interval).
 * @method        CarbonImmutable              subHours(int $value = 1)                                                             Sub hours (the $value count passed in) to the instance (using date interval).
 * @method        CarbonImmutable              subHour()                                                                            Sub one hour to the instance (using date interval).
 * @method        CarbonImmutable              addMinutes(int $value = 1)                                                           Add minutes (the $value count passed in) to the instance (using date interval).
 * @method        CarbonImmutable              addMinute()                                                                          Add one minute to the instance (using date interval).
 * @method        CarbonImmutable              subMinutes(int $value = 1)                                                           Sub minutes (the $value count passed in) to the instance (using date interval).
 * @method        CarbonImmutable              subMinute()                                                                          Sub one minute to the instance (using date interval).
 * @method        CarbonImmutable              addSeconds(int $value = 1)                                                           Add seconds (the $value count passed in) to the instance (using date interval).
 * @method        CarbonImmutable              addSecond()                                                                          Add one second to the instance (using date interval).
 * @method        CarbonImmutable              subSeconds(int $value = 1)                                                           Sub seconds (the $value count passed in) to the instance (using date interval).
 * @method        CarbonImmutable              subSecond()                                                                          Sub one second to the instance (using date interval).
 * @method        CarbonImmutable              addMillis(int $value = 1)                                                            Add milliseconds (the $value count passed in) to the instance (using date interval).
 * @method        CarbonImmutable              addMilli()                                                                           Add one millisecond to the instance (using date interval).
 * @method        CarbonImmutable              subMillis(int $value = 1)                                                            Sub milliseconds (the $value count passed in) to the instance (using date interval).
 * @method        CarbonImmutable              subMilli()                                                                           Sub one millisecond to the instance (using date interval).
 * @method        CarbonImmutable              addMilliseconds(int $value = 1)                                                      Add milliseconds (the $value count passed in) to the instance (using date interval).
 * @method        CarbonImmutable              addMillisecond()                                                                     Add one millisecond to the instance (using date interval).
 * @method        CarbonImmutable              subMilliseconds(int $value = 1)                                                      Sub milliseconds (the $value count passed in) to the instance (using date interval).
 * @method        CarbonImmutable              subMillisecond()                                                                     Sub one millisecond to the instance (using date interval).
 * @method        CarbonImmutable              addMicros(int $value = 1)                                                            Add microseconds (the $value count passed in) to the instance (using date interval).
 * @method        CarbonImmutable              addMicro()                                                                           Add one microsecond to the instance (using date interval).
 * @method        CarbonImmutable              subMicros(int $value = 1)                                                            Sub microseconds (the $value count passed in) to the instance (using date interval).
 * @method        CarbonImmutable              subMicro()                                                                           Sub one microsecond to the instance (using date interval).
 * @method        CarbonImmutable              addMicroseconds(int $value = 1)                                                      Add microseconds (the $value count passed in) to the instance (using date interval).
 * @method        CarbonImmutable              addMicrosecond()                                                                     Add one microsecond to the instance (using date interval).
 * @method        CarbonImmutable              subMicroseconds(int $value = 1)                                                      Sub microseconds (the $value count passed in) to the instance (using date interval).
 * @method        CarbonImmutable              subMicrosecond()                                                                     Sub one microsecond to the instance (using date interval).
 * @method        CarbonImmutable              addMillennia(int $value = 1)                                                         Add millennia (the $value count passed in) to the instance (using date interval).
 * @method        CarbonImmutable              addMillennium()                                                                      Add one millennium to the instance (using date interval).
 * @method        CarbonImmutable              subMillennia(int $value = 1)                                                         Sub millennia (the $value count passed in) to the instance (using date interval).
 * @method        CarbonImmutable              subMillennium()                                                                      Sub one millennium to the instance (using date interval).
 * @method        CarbonImmutable              addMillenniaWithOverflow(int $value = 1)                                             Add millennia (the $value count passed in) to the instance (using date interval) with overflow explicitly allowed.
 * @method        CarbonImmutable              addMillenniumWithOverflow()                                                          Add one millennium to the instance (using date interval) with overflow explicitly allowed.
 * @method        CarbonImmutable              subMillenniaWithOverflow(int $value = 1)                                             Sub millennia (the $value count passed in) to the instance (using date interval) with overflow explicitly allowed.
 * @method        CarbonImmutable              subMillenniumWithOverflow()                                                          Sub one millennium to the instance (using date interval) with overflow explicitly allowed.
 * @method        CarbonImmutable              addMillenniaWithoutOverflow(int $value = 1)                                          Add millennia (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              addMillenniumWithoutOverflow()                                                       Add one millennium to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              subMillenniaWithoutOverflow(int $value = 1)                                          Sub millennia (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              subMillenniumWithoutOverflow()                                                       Sub one millennium to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              addMillenniaWithNoOverflow(int $value = 1)                                           Add millennia (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              addMillenniumWithNoOverflow()                                                        Add one millennium to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              subMillenniaWithNoOverflow(int $value = 1)                                           Sub millennia (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              subMillenniumWithNoOverflow()                                                        Sub one millennium to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              addMillenniaNoOverflow(int $value = 1)                                               Add millennia (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              addMillenniumNoOverflow()                                                            Add one millennium to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              subMillenniaNoOverflow(int $value = 1)                                               Sub millennia (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              subMillenniumNoOverflow()                                                            Sub one millennium to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              addCenturies(int $value = 1)                                                         Add centuries (the $value count passed in) to the instance (using date interval).
 * @method        CarbonImmutable              addCentury()                                                                         Add one century to the instance (using date interval).
 * @method        CarbonImmutable              subCenturies(int $value = 1)                                                         Sub centuries (the $value count passed in) to the instance (using date interval).
 * @method        CarbonImmutable              subCentury()                                                                         Sub one century to the instance (using date interval).
 * @method        CarbonImmutable              addCenturiesWithOverflow(int $value = 1)                                             Add centuries (the $value count passed in) to the instance (using date interval) with overflow explicitly allowed.
 * @method        CarbonImmutable              addCenturyWithOverflow()                                                             Add one century to the instance (using date interval) with overflow explicitly allowed.
 * @method        CarbonImmutable              subCenturiesWithOverflow(int $value = 1)                                             Sub centuries (the $value count passed in) to the instance (using date interval) with overflow explicitly allowed.
 * @method        CarbonImmutable              subCenturyWithOverflow()                                                             Sub one century to the instance (using date interval) with overflow explicitly allowed.
 * @method        CarbonImmutable              addCenturiesWithoutOverflow(int $value = 1)                                          Add centuries (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              addCenturyWithoutOverflow()                                                          Add one century to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              subCenturiesWithoutOverflow(int $value = 1)                                          Sub centuries (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              subCenturyWithoutOverflow()                                                          Sub one century to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              addCenturiesWithNoOverflow(int $value = 1)                                           Add centuries (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              addCenturyWithNoOverflow()                                                           Add one century to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              subCenturiesWithNoOverflow(int $value = 1)                                           Sub centuries (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              subCenturyWithNoOverflow()                                                           Sub one century to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              addCenturiesNoOverflow(int $value = 1)                                               Add centuries (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              addCenturyNoOverflow()                                                               Add one century to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              subCenturiesNoOverflow(int $value = 1)                                               Sub centuries (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              subCenturyNoOverflow()                                                               Sub one century to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              addDecades(int $value = 1)                                                           Add decades (the $value count passed in) to the instance (using date interval).
 * @method        CarbonImmutable              addDecade()                                                                          Add one decade to the instance (using date interval).
 * @method        CarbonImmutable              subDecades(int $value = 1)                                                           Sub decades (the $value count passed in) to the instance (using date interval).
 * @method        CarbonImmutable              subDecade()                                                                          Sub one decade to the instance (using date interval).
 * @method        CarbonImmutable              addDecadesWithOverflow(int $value = 1)                                               Add decades (the $value count passed in) to the instance (using date interval) with overflow explicitly allowed.
 * @method        CarbonImmutable              addDecadeWithOverflow()                                                              Add one decade to the instance (using date interval) with overflow explicitly allowed.
 * @method        CarbonImmutable              subDecadesWithOverflow(int $value = 1)                                               Sub decades (the $value count passed in) to the instance (using date interval) with overflow explicitly allowed.
 * @method        CarbonImmutable              subDecadeWithOverflow()                                                              Sub one decade to the instance (using date interval) with overflow explicitly allowed.
 * @method        CarbonImmutable              addDecadesWithoutOverflow(int $value = 1)                                            Add decades (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              addDecadeWithoutOverflow()                                                           Add one decade to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              subDecadesWithoutOverflow(int $value = 1)                                            Sub decades (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              subDecadeWithoutOverflow()                                                           Sub one decade to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              addDecadesWithNoOverflow(int $value = 1)                                             Add decades (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              addDecadeWithNoOverflow()                                                            Add one decade to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              subDecadesWithNoOverflow(int $value = 1)                                             Sub decades (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              subDecadeWithNoOverflow()                                                            Sub one decade to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              addDecadesNoOverflow(int $value = 1)                                                 Add decades (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              addDecadeNoOverflow()                                                                Add one decade to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              subDecadesNoOverflow(int $value = 1)                                                 Sub decades (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              subDecadeNoOverflow()                                                                Sub one decade to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              addQuarters(int $value = 1)                                                          Add quarters (the $value count passed in) to the instance (using date interval).
 * @method        CarbonImmutable              addQuarter()                                                                         Add one quarter to the instance (using date interval).
 * @method        CarbonImmutable              subQuarters(int $value = 1)                                                          Sub quarters (the $value count passed in) to the instance (using date interval).
 * @method        CarbonImmutable              subQuarter()                                                                         Sub one quarter to the instance (using date interval).
 * @method        CarbonImmutable              addQuartersWithOverflow(int $value = 1)                                              Add quarters (the $value count passed in) to the instance (using date interval) with overflow explicitly allowed.
 * @method        CarbonImmutable              addQuarterWithOverflow()                                                             Add one quarter to the instance (using date interval) with overflow explicitly allowed.
 * @method        CarbonImmutable              subQuartersWithOverflow(int $value = 1)                                              Sub quarters (the $value count passed in) to the instance (using date interval) with overflow explicitly allowed.
 * @method        CarbonImmutable              subQuarterWithOverflow()                                                             Sub one quarter to the instance (using date interval) with overflow explicitly allowed.
 * @method        CarbonImmutable              addQuartersWithoutOverflow(int $value = 1)                                           Add quarters (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              addQuarterWithoutOverflow()                                                          Add one quarter to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              subQuartersWithoutOverflow(int $value = 1)                                           Sub quarters (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              subQuarterWithoutOverflow()                                                          Sub one quarter to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              addQuartersWithNoOverflow(int $value = 1)                                            Add quarters (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              addQuarterWithNoOverflow()                                                           Add one quarter to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              subQuartersWithNoOverflow(int $value = 1)                                            Sub quarters (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              subQuarterWithNoOverflow()                                                           Sub one quarter to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              addQuartersNoOverflow(int $value = 1)                                                Add quarters (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              addQuarterNoOverflow()                                                               Add one quarter to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              subQuartersNoOverflow(int $value = 1)                                                Sub quarters (the $value count passed in) to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              subQuarterNoOverflow()                                                               Sub one quarter to the instance (using date interval) with overflow explicitly forbidden.
 * @method        CarbonImmutable              addWeeks(int $value = 1)                                                             Add weeks (the $value count passed in) to the instance (using date interval).
 * @method        CarbonImmutable              addWeek()                                                                            Add one week to the instance (using date interval).
 * @method        CarbonImmutable              subWeeks(int $value = 1)                                                             Sub weeks (the $value count passed in) to the instance (using date interval).
 * @method        CarbonImmutable              subWeek()                                                                            Sub one week to the instance (using date interval).
 * @method        CarbonImmutable              addWeekdays(int $value = 1)                                                          Add weekdays (the $value count passed in) to the instance (using date interval).
 * @method        CarbonImmutable              addWeekday()                                                                         Add one weekday to the instance (using date interval).
 * @method        CarbonImmutable              subWeekdays(int $value = 1)                                                          Sub weekdays (the $value count passed in) to the instance (using date interval).
 * @method        CarbonImmutable              subWeekday()                                                                         Sub one weekday to the instance (using date interval).
 * @method        CarbonImmutable              addRealMicros(int $value = 1)                                                        Add microseconds (the $value count passed in) to the instance (using timestamp).
 * @method        CarbonImmutable              addRealMicro()                                                                       Add one microsecond to the instance (using timestamp).
 * @method        CarbonImmutable              subRealMicros(int $value = 1)                                                        Sub microseconds (the $value count passed in) to the instance (using timestamp).
 * @method        CarbonImmutable              subRealMicro()                                                                       Sub one microsecond to the instance (using timestamp).
 * @method        CarbonPeriod                 microsUntil($endDate = null, int $factor = 1)                                        Return an iterable period from current date to given end (string, DateTime or Carbon instance) for each microsecond or every X microseconds if a factor is given.
 * @method        CarbonImmutable              addRealMicroseconds(int $value = 1)                                                  Add microseconds (the $value count passed in) to the instance (using timestamp).
 * @method        CarbonImmutable              addRealMicrosecond()                                                                 Add one microsecond to the instance (using timestamp).
 * @method        CarbonImmutable              subRealMicroseconds(int $value = 1)                                                  Sub microseconds (the $value count passed in) to the instance (using timestamp).
 * @method        CarbonImmutable              subRealMicrosecond()                                                                 Sub one microsecond to the instance (using timestamp).
 * @method        CarbonPeriod                 microsecondsUntil($endDate = null, int $factor = 1)                                  Return an iterable period from current date to given end (string, DateTime or Carbon instance) for each microsecond or every X microseconds if a factor is given.
 * @method        CarbonImmutable              addRealMillis(int $value = 1)                                                        Add milliseconds (the $value count passed in) to the instance (using timestamp).
 * @method        CarbonImmutable              addRealMilli()                                                                       Add one millisecond to the instance (using timestamp).
 * @method        CarbonImmutable              subRealMillis(int $value = 1)                                                        Sub milliseconds (the $value count passed in) to the instance (using timestamp).
 * @method        CarbonImmutable              subRealMilli()                                                                       Sub one millisecond to the instance (using timestamp).
 * @method        CarbonPeriod                 millisUntil($endDate = null, int $factor = 1)                                        Return an iterable period from current date to given end (string, DateTime or Carbon instance) for each millisecond or every X milliseconds if a factor is given.
 * @method        CarbonImmutable              addRealMilliseconds(int $value = 1)                                                  Add milliseconds (the $value count passed in) to the instance (using timestamp).
 * @method        CarbonImmutable              addRealMillisecond()                                                                 Add one millisecond to the instance (using timestamp).
 * @method        CarbonImmutable              subRealMilliseconds(int $value = 1)                                                  Sub milliseconds (the $value count passed in) to the instance (using timestamp).
 * @method        CarbonImmutable              subRealMillisecond()                                                                 Sub one millisecond to the instance (using timestamp).
 * @method        CarbonPeriod                 millisecondsUntil($endDate = null, int $factor = 1)                                  Return an iterable period from current date to given end (string, DateTime or Carbon instance) for each millisecond or every X milliseconds if a factor is given.
 * @method        CarbonImmutable              addRealSeconds(int $value = 1)                                                       Add seconds (the $value count passed in) to the instance (using timestamp).
 * @method        CarbonImmutable              addRealSecond()                                                                      Add one second to the instance (using timestamp).
 * @method        CarbonImmutable              subRealSeconds(int $value = 1)                                                       Sub seconds (the $value count passed in) to the instance (using timestamp).
 * @method        CarbonImmutable              subRealSecond()                                                                      Sub one second to the instance (using timestamp).
 * @method        CarbonPeriod                 secondsUntil($endDate = null, int $factor = 1)                                       Return an iterable period from current date to given end (string, DateTime or Carbon instance) for each second or every X seconds if a factor is given.
 * @method        CarbonImmutable              addRealMinutes(int $value = 1)                                                       Add minutes (the $value count passed in) to the instance (using timestamp).
 * @method        CarbonImmutable              addRealMinute()                                                                      Add one minute to the instance (using timestamp).
 * @method        CarbonImmutable              subRealMinutes(int $value = 1)                                                       Sub minutes (the $value count passed in) to the instance (using timestamp).
 * @method        CarbonImmutable              subRealMinute()                                                                      Sub one minute to the instance (using timestamp).
 * @method        CarbonPeriod                 minutesUntil($endDate = null, int $factor = 1)                                       Return an iterable period from current date to given end (string, DateTime or Carbon instance) for each minute or every X minutes if a factor is given.
 * @method        CarbonImmutable              addRealHours(int $value = 1)                                                         Add hours (the $value count passed in) to the instance (using timestamp).
 * @method        CarbonImmutable              addRealHour()                                                                        Add one hour to the instance (using timestamp).
 * @method        CarbonImmutable              subRealHours(int $value = 1)                                                         Sub hours (the $value count passed in) to the instance (using timestamp).
 * @method        CarbonImmutable              subRealHour()                                                                        Sub one hour to the instance (using timestamp).
 * @method        CarbonPeriod                 hoursUntil($endDate = null, int $factor = 1)                                         Return an iterable period from current date to given end (string, DateTime or Carbon instance) for each hour or every X hours if a factor is given.
 * @method        CarbonImmutable              addRealDays(int $value = 1)                                                          Add days (the $value count passed in) to the instance (using timestamp).
 * @method        CarbonImmutable              addRealDay()                                                                         Add one day to the instance (using timestamp).
 * @method        CarbonImmutable              subRealDays(int $value = 1)                                                          Sub days (the $value count passed in) to the instance (using timestamp).
 * @method        CarbonImmutable              subRealDay()                                                                         Sub one day to the instance (using timestamp).
 * @method        CarbonPeriod                 daysUntil($endDate = null, int $factor = 1)                                          Return an iterable period from current date to given end (string, DateTime or Carbon instance) for each day or every X days if a factor is given.
 * @method        CarbonImmutable              addRealWeeks(int $value = 1)                                                         Add weeks (the $value count passed in) to the instance (using timestamp).
 * @method        CarbonImmutable              addRealWeek()                                                                        Add one week to the instance (using timestamp).
 * @method        CarbonImmutable              subRealWeeks(int $value = 1)                                                         Sub weeks (the $value count passed in) to the instance (using timestamp).
 * @method        CarbonImmutable              subRealWeek()                                                                        Sub one week to the instance (using timestamp).
 * @method        CarbonPeriod                 weeksUntil($endDate = null, int $factor = 1)                                         Return an iterable period from current date to given end (string, DateTime or Carbon instance) for each week or every X weeks if a factor is given.
 * @method        CarbonImmutable              addRealMonths(int $value = 1)                                                        Add months (the $value count passed in) to the instance (using timestamp).
 * @method        CarbonImmutable              addRealMonth()                                                                       Add one month to the instance (using timestamp).
 * @method        CarbonImmutable              subRealMonths(int $value = 1)                                                        Sub months (the $value count passed in) to the instance (using timestamp).
 * @method        CarbonImmutable              subRealMonth()                                                                       Sub one month to the instance (using timestamp).
 * @method        CarbonPeriod                 monthsUntil($endDate = null, int $factor = 1)                                        Return an iterable period from current date to given end (string, DateTime or Carbon instance) for each month or every X months if a factor is given.
 * @method        CarbonImmutable              addRealQuarters(int $value = 1)                                                      Add quarters (the $value count passed in) to the instance (using timestamp).
 * @method        CarbonImmutable              addRealQuarter()                                                                     Add one quarter to the instance (using timestamp).
 * @method        CarbonImmutable              subRealQuarters(int $value = 1)                                                      Sub quarters (the $value count passed in) to the instance (using timestamp).
 * @method        CarbonImmutable              subRealQuarter()                                                                     Sub one quarter to the instance (using timestamp).
 * @method        CarbonPeriod                 quartersUntil($endDate = null, int $factor = 1)                                      Return an iterable period from current date to given end (string, DateTime or Carbon instance) for each quarter or every X quarters if a factor is given.
 * @method        CarbonImmutable              addRealYears(int $value = 1)                                                         Add years (the $value count passed in) to the instance (using timestamp).
 * @method        CarbonImmutable              addRealYear()                                                                        Add one year to the instance (using timestamp).
 * @method        CarbonImmutable              subRealYears(int $value = 1)                                                         Sub years (the $value count passed in) to the instance (using timestamp).
 * @method        CarbonImmutable              subRealYear()                                                                        Sub one year to the instance (using timestamp).
 * @method        CarbonPeriod                 yearsUntil($endDate = null, int $factor = 1)                                         Return an iterable period from current date to given end (string, DateTime or Carbon instance) for each year or every X years if a factor is given.
 * @method        CarbonImmutable              addRealDecades(int $value = 1)                                                       Add decades (the $value count passed in) to the instance (using timestamp).
 * @method        CarbonImmutable              addRealDecade()                                                                      Add one decade to the instance (using timestamp).
 * @method        CarbonImmutable              subRealDecades(int $value = 1)                                                       Sub decades (the $value count passed in) to the instance (using timestamp).
 * @method        CarbonImmutable              subRealDecade()                                                                      Sub one decade to the instance (using timestamp).
 * @method        CarbonPeriod                 decadesUntil($endDate = null, int $factor = 1)                                       Return an iterable period from current date to given end (string, DateTime or Carbon instance) for each decade or every X decades if a factor is given.
 * @method        CarbonImmutable              addRealCenturies(int $value = 1)                                                     Add centuries (the $value count passed in) to the instance (using timestamp).
 * @method        CarbonImmutable              addRealCentury()                                                                     Add one century to the instance (using timestamp).
 * @method        CarbonImmutable              subRealCenturies(int $value = 1)                                                     Sub centuries (the $value count passed in) to the instance (using timestamp).
 * @method        CarbonImmutable              subRealCentury()                                                                     Sub one century to the instance (using timestamp).
 * @method        CarbonPeriod                 centuriesUntil($endDate = null, int $factor = 1)                                     Return an iterable period from current date to given end (string, DateTime or Carbon instance) for each century or every X centuries if a factor is given.
 * @method        CarbonImmutable              addRealMillennia(int $value = 1)                                                     Add millennia (the $value count passed in) to the instance (using timestamp).
 * @method        CarbonImmutable              addRealMillennium()                                                                  Add one millennium to the instance (using timestamp).
 * @method        CarbonImmutable              subRealMillennia(int $value = 1)                                                     Sub millennia (the $value count passed in) to the instance (using timestamp).
 * @method        CarbonImmutable              subRealMillennium()                                                                  Sub one millennium to the instance (using timestamp).
 * @method        CarbonPeriod                 millenniaUntil($endDate = null, int $factor = 1)                                     Return an iterable period from current date to given end (string, DateTime or Carbon instance) for each millennium or every X millennia if a factor is given.
 * @method        CarbonImmutable              roundYear(float $precision = 1, string $function = "round")                          Round the current instance year with given precision using the given function.
 * @method        CarbonImmutable              roundYears(float $precision = 1, string $function = "round")                         Round the current instance year with given precision using the given function.
 * @method        CarbonImmutable              floorYear(float $precision = 1)                                                      Truncate the current instance year with given precision.
 * @method        CarbonImmutable              floorYears(float $precision = 1)                                                     Truncate the current instance year with given precision.
 * @method        CarbonImmutable              ceilYear(float $precision = 1)                                                       Ceil the current instance year with given precision.
 * @method        CarbonImmutable              ceilYears(float $precision = 1)                                                      Ceil the current instance year with given precision.
 * @method        CarbonImmutable              roundMonth(float $precision = 1, string $function = "round")                         Round the current instance month with given precision using the given function.
 * @method        CarbonImmutable              roundMonths(float $precision = 1, string $function = "round")                        Round the current instance month with given precision using the given function.
 * @method        CarbonImmutable              floorMonth(float $precision = 1)                                                     Truncate the current instance month with given precision.
 * @method        CarbonImmutable              floorMonths(float $precision = 1)                                                    Truncate the current instance month with given precision.
 * @method        CarbonImmutable              ceilMonth(float $precision = 1)                                                      Ceil the current instance month with given precision.
 * @method        CarbonImmutable              ceilMonths(float $precision = 1)                                                     Ceil the current instance month with given precision.
 * @method        CarbonImmutable              roundDay(float $precision = 1, string $function = "round")                           Round the current instance day with given precision using the given function.
 * @method        CarbonImmutable              roundDays(float $precision = 1, string $function = "round")                          Round the current instance day with given precision using the given function.
 * @method        CarbonImmutable              floorDay(float $precision = 1)                                                       Truncate the current instance day with given precision.
 * @method        CarbonImmutable              floorDays(float $precision = 1)                                                      Truncate the current instance day with given precision.
 * @method        CarbonImmutable              ceilDay(float $precision = 1)                                                        Ceil the current instance day with given precision.
 * @method        CarbonImmutable              ceilDays(float $precision = 1)                                                       Ceil the current instance day with given precision.
 * @method        CarbonImmutable              roundHour(float $precision = 1, string $function = "round")                          Round the current instance hour with given precision using the given function.
 * @method        CarbonImmutable              roundHours(float $precision = 1, string $function = "round")                         Round the current instance hour with given precision using the given function.
 * @method        CarbonImmutable              floorHour(float $precision = 1)                                                      Truncate the current instance hour with given precision.
 * @method        CarbonImmutable              floorHours(float $precision = 1)                                                     Truncate the current instance hour with given precision.
 * @method        CarbonImmutable              ceilHour(float $precision = 1)                                                       Ceil the current instance hour with given precision.
 * @method        CarbonImmutable              ceilHours(float $precision = 1)                                                      Ceil the current instance hour with given precision.
 * @method        CarbonImmutable              roundMinute(float $precision = 1, string $function = "round")                        Round the current instance minute with given precision using the given function.
 * @method        CarbonImmutable              roundMinutes(float $precision = 1, string $function = "round")                       Round the current instance minute with given precision using the given function.
 * @method        CarbonImmutable              floorMinute(float $precision = 1)                                                    Truncate the current instance minute with given precision.
 * @method        CarbonImmutable              floorMinutes(float $precision = 1)                                                   Truncate the current instance minute with given precision.
 * @method        CarbonImmutable              ceilMinute(float $precision = 1)                                                     Ceil the current instance minute with given precision.
 * @method        CarbonImmutable              ceilMinutes(float $precision = 1)                                                    Ceil the current instance minute with given precision.
 * @method        CarbonImmutable              roundSecond(float $precision = 1, string $function = "round")                        Round the current instance second with given precision using the given function.
 * @method        CarbonImmutable              roundSeconds(float $precision = 1, string $function = "round")                       Round the current instance second with given precision using the given function.
 * @method        CarbonImmutable              floorSecond(float $precision = 1)                                                    Truncate the current instance second with given precision.
 * @method        CarbonImmutable              floorSeconds(float $precision = 1)                                                   Truncate the current instance second with given precision.
 * @method        CarbonImmutable              ceilSecond(float $precision = 1)                                                     Ceil the current instance second with given precision.
 * @method        CarbonImmutable              ceilSeconds(float $precision = 1)                                                    Ceil the current instance second with given precision.
 * @method        CarbonImmutable              roundMillennium(float $precision = 1, string $function = "round")                    Round the current instance millennium with given precision using the given function.
 * @method        CarbonImmutable              roundMillennia(float $precision = 1, string $function = "round")                     Round the current instance millennium with given precision using the given function.
 * @method        CarbonImmutable              floorMillennium(float $precision = 1)                                                Truncate the current instance millennium with given precision.
 * @method        CarbonImmutable              floorMillennia(float $precision = 1)                                                 Truncate the current instance millennium with given precision.
 * @method        CarbonImmutable              ceilMillennium(float $precision = 1)                                                 Ceil the current instance millennium with given precision.
 * @method        CarbonImmutable              ceilMillennia(float $precision = 1)                                                  Ceil the current instance millennium with given precision.
 * @method        CarbonImmutable              roundCentury(float $precision = 1, string $function = "round")                       Round the current instance century with given precision using the given function.
 * @method        CarbonImmutable              roundCenturies(float $precision = 1, string $function = "round")                     Round the current instance century with given precision using the given function.
 * @method        CarbonImmutable              floorCentury(float $precision = 1)                                                   Truncate the current instance century with given precision.
 * @method        CarbonImmutable              floorCenturies(float $precision = 1)                                                 Truncate the current instance century with given precision.
 * @method        CarbonImmutable              ceilCentury(float $precision = 1)                                                    Ceil the current instance century with given precision.
 * @method        CarbonImmutable              ceilCenturies(float $precision = 1)                                                  Ceil the current instance century with given precision.
 * @method        CarbonImmutable              roundDecade(float $precision = 1, string $function = "round")                        Round the current instance decade with given precision using the given function.
 * @method        CarbonImmutable              roundDecades(float $precision = 1, string $function = "round")                       Round the current instance decade with given precision using the given function.
 * @method        CarbonImmutable              floorDecade(float $precision = 1)                                                    Truncate the current instance decade with given precision.
 * @method        CarbonImmutable              floorDecades(float $precision = 1)                                                   Truncate the current instance decade with given precision.
 * @method        CarbonImmutable              ceilDecade(float $precision = 1)                                                     Ceil the current instance decade with given precision.
 * @method        CarbonImmutable              ceilDecades(float $precision = 1)                                                    Ceil the current instance decade with given precision.
 * @method        CarbonImmutable              roundQuarter(float $precision = 1, string $function = "round")                       Round the current instance quarter with given precision using the given function.
 * @method        CarbonImmutable              roundQuarters(float $precision = 1, string $function = "round")                      Round the current instance quarter with given precision using the given function.
 * @method        CarbonImmutable              floorQuarter(float $precision = 1)                                                   Truncate the current instance quarter with given precision.
 * @method        CarbonImmutable              floorQuarters(float $precision = 1)                                                  Truncate the current instance quarter with given precision.
 * @method        CarbonImmutable              ceilQuarter(float $precision = 1)                                                    Ceil the current instance quarter with given precision.
 * @method        CarbonImmutable              ceilQuarters(float $precision = 1)                                                   Ceil the current instance quarter with given precision.
 * @method        CarbonImmutable              roundMillisecond(float $precision = 1, string $function = "round")                   Round the current instance millisecond with given precision using the given function.
 * @method        CarbonImmutable              roundMilliseconds(float $precision = 1, string $function = "round")                  Round the current instance millisecond with given precision using the given function.
 * @method        CarbonImmutable              floorMillisecond(float $precision = 1)                                               Truncate the current instance millisecond with given precision.
 * @method        CarbonImmutable              floorMilliseconds(float $precision = 1)                                              Truncate the current instance millisecond with given precision.
 * @method        CarbonImmutable              ceilMillisecond(float $precision = 1)                                                Ceil the current instance millisecond with given precision.
 * @method        CarbonImmutable              ceilMilliseconds(float $precision = 1)                                               Ceil the current instance millisecond with given precision.
 * @method        CarbonImmutable              roundMicrosecond(float $precision = 1, string $function = "round")                   Round the current instance microsecond with given precision using the given function.
 * @method        CarbonImmutable              roundMicroseconds(float $precision = 1, string $function = "round")                  Round the current instance microsecond with given precision using the given function.
 * @method        CarbonImmutable              floorMicrosecond(float $precision = 1)                                               Truncate the current instance microsecond with given precision.
 * @method        CarbonImmutable              floorMicroseconds(float $precision = 1)                                              Truncate the current instance microsecond with given precision.
 * @method        CarbonImmutable              ceilMicrosecond(float $precision = 1)                                                Ceil the current instance microsecond with given precision.
 * @method        CarbonImmutable              ceilMicroseconds(float $precision = 1)                                               Ceil the current instance microsecond with given precision.
 * @method        string                       shortAbsoluteDiffForHumans(DateTimeInterface $other = null, int $parts = 1)          Get the difference (short format, 'Absolute' mode) in a human readable format in the current locale. ($other and $parts parameters can be swapped.)
 * @method        string                       longAbsoluteDiffForHumans(DateTimeInterface $other = null, int $parts = 1)           Get the difference (long format, 'Absolute' mode) in a human readable format in the current locale. ($other and $parts parameters can be swapped.)
 * @method        string                       shortRelativeDiffForHumans(DateTimeInterface $other = null, int $parts = 1)          Get the difference (short format, 'Relative' mode) in a human readable format in the current locale. ($other and $parts parameters can be swapped.)
 * @method        string                       longRelativeDiffForHumans(DateTimeInterface $other = null, int $parts = 1)           Get the difference (long format, 'Relative' mode) in a human readable format in the current locale. ($other and $parts parameters can be swapped.)
 * @method        string                       shortRelativeToNowDiffForHumans(DateTimeInterface $other = null, int $parts = 1)     Get the difference (short format, 'RelativeToNow' mode) in a human readable format in the current locale. ($other and $parts parameters can be swapped.)
 * @method        string                       longRelativeToNowDiffForHumans(DateTimeInterface $other = null, int $parts = 1)      Get the difference (long format, 'RelativeToNow' mode) in a human readable format in the current locale. ($other and $parts parameters can be swapped.)
 * @method        string                       shortRelativeToOtherDiffForHumans(DateTimeInterface $other = null, int $parts = 1)   Get the difference (short format, 'RelativeToOther' mode) in a human readable format in the current locale. ($other and $parts parameters can be swapped.)
 * @method        string                       longRelativeToOtherDiffForHumans(DateTimeInterface $other = null, int $parts = 1)    Get the difference (long format, 'RelativeToOther' mode) in a human readable format in the current locale. ($other and $parts parameters can be swapped.)
 * @method        static CarbonImmutable|false createFromFormat(string $format, string $time, string|DateTimeZone $timezone = null) Parse a string into a new CarbonImmutable object according to the specified format.
 * @method        static CarbonImmutable       __set_state(array $array)                                                            https://php.net/manual/en/datetime.set-state.php
 */

final class QubusDateTimeImmutable extends CarbonImmutable implements DateTimeInterface
{
}
