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

use Cocur\Slugify\Slugify;

use function filter_var;
use function html_entity_decode;
use function in_array;
use function is_array;
use function is_numeric;
use function preg_match;
use function preg_replace;
use function preg_replace_callback;
use function Qubus\Support\Helpers\remove_accents;
use function range;
use function str_replace;
use function strip_tags;
use function strrpos;
use function strtolower;
use function strtoupper;
use function strval;
use function substr;
use function trim;
use function ucfirst;
use function ucwords;

use const ENT_QUOTES;
use const FILTER_SANITIZE_STRING;

/**
 * Pluralize and singularize English words.
 */
class Inflector
{
    /** @var  array  default list of uncountable words, in English */
    protected static $uncountableWords = [
        'equipment',
        'information',
        'rice',
        'money',
        'species',
        'series',
        'fish',
        'meta',
        'feedback',
        'people',
        'stadia',
        'chassis',
        'clippers',
        'debris',
        'diabetes',
        'gallows',
        'graffiti',
        'headquarters',
        'innings',
        'news',
        'nexus',
        'proceedings',
        'research',
        'weather',
    ];

    /** @var array Default list of iregular plural words, in English */
    protected static $pluralRules = [
        '/^(ox)$/i'                => '\1\2en', // ox
        '/([m|l])ouse$/i'          => '\1ice', // mouse, louse
        '/(matr|vert|ind)ix|ex$/i' => '\1ices', // matrix, vertex, index
        '/(x|ch|ss|sh)$/i'         => '\1es', // search, switch, fix, box, process, address
        '/([^aeiouy]|qu)y$/i'      => '\1ies', // query, ability, agency
        '/(hive)$/i'               => '\1s', // archive, hive
        '/(chef)$/i'               => '\1s', // chef
        '/(?:([^f])fe|([lr])f)$/i' => '\1\2ves', // half, safe, wife
        '/sis$/i'                  => 'ses', // basis, diagnosis
        '/([ti])um$/i'             => '\1a', // datum, medium
        '/(p)erson$/i'             => '\1eople', // person, salesperson
        '/(m)an$/i'                => '\1en', // man, woman, spokesman
        '/(c)hild$/i'              => '\1hildren', // child
        '/(buffal|tomat)o$/i'      => '\1\2oes', // buffalo, tomato
        '/(bu|campu)s$/i'          => '\1\2ses', // bus, campus
        '/(alumn|bacill|cact|foc|fung|nucle|radi|stimul|syllab|termin)us$/i' => '\1i', // alumnus, cactus, fungus
        '/(alias|status|virus)$/i'                                           => '\1es', // alias
        '/(octop)us$/i'                                                      => '\1i', // octopus
        '/(ax|cris|test)is$/i'                                               => '\1es', // axis, crisis
        '/(quiz)$/i'                                                         => '\1zes', // quiz
        '/s$/'                                                               => 's', // no change (compatibility)
        '/^$/'                                                               => '',
        '/$/'                                                                => 's',
    ];

    /** @var  array  default list of iregular singular words, in English */
    protected static $singularRules = [
        '/(matr)ices$/i'     => '\1ix',
        '/(s)tatuses$/i'     => '\1\2tatus',
        '/^(.*)(menu)s$/i'   => '\1\2',
        '/(quiz)zes$/i'      => '\\1',
        '/(vert|ind)ices$/i' => '\1ex',
        '/^(ox)en/i'         => '\1',
        '/(alias)es$/i'      => '\1',
        '/([octop|vir])i$/i' => '\1us',
        '/(alumn|bacill|cact|foc|fung|nucle|radi|stimul|syllab|termin|viri?)i$/i' => '\1us',
        '/([ftw]ax)es/i'        => '\1',
        '/(cris|ax|test)es$/i'  => '\1is',
        '/(shoe)s$/i'           => '\1',
        '/(o)es$/i'             => '\1',
        '/(bus|campus)es$/i'    => '\1',
        '/([^a])uses$/'         => '\1us',
        '/ouses$/'              => 'ouse',
        '/([m|l])ice$/i'        => '\1ouse',
        '/(x|ch|ss|sh)es$/i'    => '\1',
        '/(m)ovies$/i'          => '\1\2ovie',
        '/(s)eries$/i'          => '\1\2eries',
        '/([^aeiouy]|qu)ies$/i' => '\1y',
        '/([lr])ves$/i'         => '\1f',
        '/(tive)s$/i'           => '\1',
        '/(hive)s$/i'           => '\1',
        '/(drive)s$/i'          => '\1',
        '/([^f])ves$/i'         => '\1fe',
        '/([le])ves$/i'         => '\1f',
        '/([^rfoa])ves$/i'      => '\1fe',
        '/(^analy)ses$/i'       => '\1sis',
        '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '\1\2sis',
        '/([ti])a$/i'    => '\1um',
        '/(p)eople$/i'   => '\1\2erson',
        '/(m)en$/i'      => '\1an',
        '/(s)tatuses$/i' => '\1\2tatus',
        '/(c)hildren$/i' => '\1\2hild',
        '/(n)ews$/i'     => '\1\2ews',
        '/eaus$/'        => 'eau',
        '/([^us])s$/i'   => '\1',
        '/^(.*us)$/'     => '\\1',
        '/s$/i'          => '',
    ];

    protected static bool $init = false;

    /**
     * Load any localized rulesets based on the current language configuration
     * If not exists, the current rules remain active
     */
    public static function initialize()
    {
        /** @todo */

        static::$init = true;
    }

    /**
     * Add order suffix to numbers ex. 1st 2nd 3rd 4th 5th.
     *
     * @link    http://snipplr.com/view/4627/a-function-to-add-a-prefix-to-numbers-ex-1st-2nd-3rd-4th-5th/
     *
     * @param   int     $number the number to ordinalize
     * @return  string  the ordinalized version of $number
     */
    public static function ordinalize($number): string
    {
        if (! is_numeric($number)) {
            return $number;
        }

        if (in_array($number % 100, range(11, 13))) {
            return $number . 'th';
        } else {
            switch ($number % 10) {
                case 1:
                    return $number . 'st';
                break;
                case 2:
                    return $number . 'nd';
                break;
                case 3:
                    return $number . 'rd';
                break;
                default:
                    return $number . 'th';
                break;
            }
        }
    }

    /**
     * Gets the plural version of the given word.
     *
     * @param   string  $word   the word to pluralize
     * @param   int     $count  number of instances
     * @return string the plural version of $word
     */
    public static function pluralize(string $word, int $count = 0): string
    {
        static::$init || static::initialize();

        $result = strval($word);

        // If a counter is provided, and that equals 1
        // return as singular.
        if ($count === 1) {
            return $result;
        }

        if (! static::isCountable($result)) {
            return $result;
        }

        foreach (static::$pluralRules as $rule => $replacement) {
            if (preg_match($rule, $result)) {
                $result = preg_replace($rule, $replacement, $result);
                break;
            }
        }

        return $result;
    }

    /**
     * Gets the singular version of the given word
     *
     * @param string $word the word to singularize
     * @return string the singular version of $word
     */
    public static function singularize(string $word): string
    {
        static::$init || static::initialize();

        $result = strval($word);

        if (! static::isCountable($result)) {
            return $result;
        }

        foreach (static::$singularRules as $rule => $replacement) {
            if (preg_match($rule, $result)) {
                $result = preg_replace($rule, $replacement, $result);
                break;
            }
        }

        return $result;
    }

    /**
     * Takes a string that has words separated by underscores and turns it into
     * a CamelCased string.
     *
     * @param string $underscoredWord  the underscored word
     * @return string the CamelCased version of $underscoredWord
     */
    public static function camelize(string $underscoredWord): string
    {
        return preg_replace_callback(
            '/(^|_)(.)/',
            function ($parm) {
                return strtoupper($parm[2]);
            },
            strval($underscoredWord)
        );
    }

    /**
     * Takes a CamelCased string and returns an underscore separated version.
     *
     * @param string $camelCasedWord  the CamelCased word
     * @return string an underscore separated version of $camelCasedWord
     */
    public static function underscore(string $camelCasedWord): string
    {
        return strtolower(
            preg_replace(
                '/([A-Z]+)([A-Z])/',
                '\1_\2',
                preg_replace(
                    '/([a-z\d])([A-Z])/',
                    '\1_\2',
                    strval($camelCasedWord)
                )
            )
        );
    }

    /**
     * Translate string to 7-bit ASCII.
     *
     * Only works with UTF-8.
     *
     * @param  string $str           String to translate
     * @param  bool   $allowNonAscii Whether to remove non ascii
     * @return string Translated string.
     */
    public static function ascii(string $str, bool $allowNonAscii = false): string
    {
        // Translate unicode characters to their simpler counterparts
        $str = remove_accents($str);

        if (! $allowNonAscii) {
            return preg_replace('/[^\x09\x0A\x0D\x20-\x7E]/', '', $str);
        }

        return $str;
    }

    /**
     * Converts your text to a URL-friendly title so it can be used in the URL.
     * Only works with UTF8 input and and only outputs 7 bit ASCII characters.
     *
     * @param string            $string             The text to slugify.
     * @param array             $constructorOptions Options that can be passed to the constructor.
     * @param string|array|null $onTheFlyOptions    Override options that can be passed to slugify method.
     * @return string The slugified text.
     */
    public static function slugify(
        string $string,
        array $constructorOptions = [],
        stirng|array|null $onTheFlyOptions = null
    ): string {
        // Sanitize string.
        if (! is_array($string)) {
            $string = filter_var($string, FILTER_SANITIZE_STRING);
        }

        // Remove tags
        if (is_array($string)) {
            foreach ($string as $k => $v) {
                $string[$k] = strip_tags($v);
            }
        }

        // Decode all entities to their simpler forms
        $string = html_entity_decode($string, ENT_QUOTES, 'UTF-8');

        return (new Slugify($constructorOptions))->slugify($string, $onTheFlyOptions);
    }

    /**
     * Turns an underscore or dash separated word and turns it into a human looking string.
     *
     * @param  string  $str       the word
     * @param  string  $sep       the separator (either _ or -)
     * @param  bool    $lowercase lowercase string and upper case first
     * @return string the human version of given string
     */
    public static function humanize(string $str, string $sep = '_', bool $lowercase = true): string
    {
        // Allow dash, otherwise default to underscore
        $sep = $sep !== '-' ? '_' : $sep;

        if ($lowercase === true) {
            $str = ucfirst($str);
        }

        return str_replace($sep, " ", strval($str));
    }

    /**
     * Takes the class name out of a modulized string.
     *
     * @param  string $classNameInModule the modulized class
     * @return string  the string without the class name
     */
    public static function demodulize(string $classNameInModule): string
    {
        return preg_replace('/^.*::/', '', strval($classNameInModule));
    }

    /**
     * Takes the namespace off the given class name.
     *
     * @param  string $className the class name
     * @return string the string without the namespace
     */
    public static function denamespace(string $className): string
    {
        $className = trim($className, '\\');
        if ($lastSeparator = strrpos($className, '\\')) {
            $className = substr($className, $lastSeparator + 1);
        }
        return $className;
    }

    /**
     * Returns the namespace of the given class name.
     *
     * @param  string $className the class name
     * @return string the string without the namespace
     */
    public static function getNamespace(string $className): string
    {
        $className = trim($className, '\\');
        if ($lastSeparator = strrpos($className, '\\')) {
            return substr($className, 0, $lastSeparator + 1);
        }
        return '';
    }

    /**
     * Takes a class name and determines the table name. The table name is a
     * pluralized version of the class name.
     *
     * @param  string $className The table name.
     * @return string The table name.
     */
    public static function tableize(string $className): string
    {
        $className = static::denamespace($className);
        return strtolower(static::pluralize(static::underscore($className)));
    }

    /**
     * Takes an underscored classname and uppercases all letters after the underscores.
     *
     * @param  string $class classname
     * @param  string $sep   separator
     * @return string
     */
    public static function wordsToUpper(string $class, string $sep = '_'): string
    {
        return str_replace(' ', $sep, ucwords(str_replace($sep, ' ', $class)));
    }

    /**
     * Takes a table name and creates the class name.
     *
     * @param  string  $name          the table name
     * @param  bool    $forceSingular whether to singularize the table name or not
     * @return string the class name
     */
    public static function classify(string $name, bool $forceSingular = true): string
    {
        $class = $forceSingular ? static::singularize($name) : $name;
        return static::wordsToUpper($class);
    }

    /**
     * Checks if the given word has a plural version.
     *
     * @param string $word the word to check
     * @return bool if the word is countable
     */
    public static function isCountable(string $word): bool
    {
        static::$init || static::initialize();

        return ! in_array(strtolower(strval($word)), static::$uncountableWords);
    }
}
