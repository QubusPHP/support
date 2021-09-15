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


/**
 * @see https://github.com/gajus/dindent for the canonical source repository
 *
 * @license https://github.com/gajus/dindent/blob/master/LICENSE BSD 3-Clause
 */

declare(strict_types=1);

namespace Qubus\Support;

use Qubus\Exception\Data\TypeException;
use RuntimeException;

final class Indenter
{
    public const ELEMENT_TYPE_BLOCK = 0;
    public const ELEMENT_TYPE_INLINE = 1;

    public const MATCH_INDENT_NO = 0;
    public const MATCH_INDENT_DECREASE = 1;
    public const MATCH_INDENT_INCREASE = 2;
    public const MATCH_DISCARD = 3;

    /** @var array $log */
    private array $log = [];

    /** @var array $options */
    private array $options = [
        'indentation_character' => '    '
    ];

    /** @var array $inlineElements */
    private array $inlineElements = [
        'b',
        'big',
        'i',
        'small',
        'tt',
        'abbr',
        'acronym',
        'cite',
        'code',
        'dfn',
        'em',
        'kbd',
        'strong',
        'samp',
        'var',
        'a',
        'bdo',
        'br',
        'img',
        'span',
        'sub',
        'sup',
        'h1',
        'h2',
        'h3',
        'h4',
        'h5',
        'h6',
        'h7',
        'button',
        'input',
        'label',
        'map',
        'object',
        'output',
        'q',
        'select',
        'small',
        'time',
    ];

    /** @var array $ignoreElements */
    private array $ignoreElements = ['pre', 'script', 'textarea'];

    /** @var array $temporaryReplacementsScript */
    private array $temporaryReplacementsScript = [];

    /** @var array $temporaryReplacementsIgnore */
    private array $temporaryReplacementsIgnore = [];

    /** @var array $temporaryReplacementsInline */
    private array $temporaryReplacementsInline = [];

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        foreach ($options as $name => $value) {
            if (!array_key_exists($name, $this->options)) {
                throw new TypeException('Unrecognized option.');
            }

            $this->options[$name] = $value;
        }
    }

    /**
     * @param string $elementName Element name, e.g. "b".
     * @param ELEMENT_TYPE_BLOCK|ELEMENT_TYPE_INLINE $type
     * @return void
     */
    public function setElementType(string $elementName, $type): void
    {
        if ($type === static::ELEMENT_TYPE_BLOCK) {
            $this->inlineElements = array_diff($this->inlineElements, [$elementName]);
        } elseif ($type === static::ELEMENT_TYPE_INLINE) {
            $this->inlineElements[] = $elementName;
        } else {
            throw new TypeException('Unrecognized element type.');
        }

        $this->inlineElements = array_unique($this->inlineElements);
    }

    /**
     * @param string|null $input HTML input.
     * @return string Indented HTML.
     */
    public function indent(?string $input = null): string
    {
        if (null === $input) {
            return '';
        }

        $this->log = [];

        // Indenter does not indent. Instead, it temporarily removes it from the code, indents the input,
        // and restores the script body.
        foreach ($this->ignoreElements as $key) {
            if (preg_match_all('/<'.$key.'\b[^>]*>([\s\S]*?)<\/'.$key.'>/mi', $input, $matches)) {
                $this->temporaryReplacementsIgnore[$key] = $matches[0];
                foreach ($matches[0] as $i => $match) {
                    $input = str_replace($match, '<'.$key.'>'.($i + 1).'</'.$key.'>', $input);
                }
            }
        }

        // Indenter does not indent <script> body. Instead, it temporary removes it from the code,
        // indents the input, and restores the script body.
        if (preg_match_all('/<script\b[^>]*>([\s\S]*?)<\/script>/mi', $input, $matches)) {
            $this->temporaryReplacementsScript = $matches[0];
            foreach ($matches[0] as $i => $match) {
                $input = str_replace($match, '<script>' . ($i + 1) . '</script>', $input);
            }
        }

        // Removing double whitespaces to make the source code easier to read.
        // With exception of <pre>/ CSS white-space changing the default behaviour, double whitespace
        // is meaningless in HTML output.
        // This reason alone is sufficient not to use Indenter in production.
        $input = str_replace("\t", '', $input);
        $input = preg_replace('/\s{2,}/u', ' ', $input);

        // Remove inline elements and replace them with text entities.
        if (preg_match_all('/<(' . implode('|', $this->inlineElements) . ')[^>]*>(?:[^<]*)<\/\1>/', $input, $matches)) {
            $this->temporaryReplacementsInline = $matches[0];
            foreach ($matches[0] as $i => $match) {
                $input = str_replace($match, 'ᐃ' . ($i + 1) . 'ᐃ', $input);
            }
        }

        $subject = $input;

        $output = '';

        $nextLineIndentationLevel = 0;

        do {
            $indentationLevel = $nextLineIndentationLevel;

            $patterns = [
                // block tag
                '/^(<([a-z]+)(?:[^>]*)>(?:[^<]*)<\/(?:\2)>)/' => static::MATCH_INDENT_NO,
                // DOCTYPE
                '/^<!([^>]*)>/' => static::MATCH_INDENT_NO,
                // tag with implied closing
                '/^<(input|link|meta|base|br|img|source|hr)([^>]*)>/' => static::MATCH_INDENT_NO,
                // self closing SVG tags
                '/^<(animate|stop|path|circle|line|polyline|rect|use)([^>]*)\/>/' => static::MATCH_INDENT_NO,
                // opening tag
                '/^<[^\/]([^>]*)>/' => static::MATCH_INDENT_INCREASE,
                // closing tag
                '/^<\/([^>]*)>/' => static::MATCH_INDENT_DECREASE,
                // self-closing tag
                '/^<(.+)\/>/' => static::MATCH_INDENT_DECREASE,
                // whitespace
                '/^(\s+)/' => static::MATCH_DISCARD,
                // text node
                '/([^<]+)/' => static::MATCH_INDENT_NO
            ];

            $rules = ['NO', 'DECREASE', 'INCREASE', 'DISCARD'];

            foreach ($patterns as $pattern => $rule) {
                if ($match = preg_match($pattern, $subject, $matches)) {
                    $this->log[] = [
                        'rule' => $rules[$rule],
                        'pattern' => $pattern,
                        'subject' => $subject,
                        'match' => $matches[0]
                    ];

                    $subject = mb_substr($subject, mb_strlen($matches[0]));

                    if ($rule === static::MATCH_DISCARD) {
                        break;
                    }

                    if ($rule === static::MATCH_INDENT_NO) {
                    } elseif ($rule === static::MATCH_INDENT_DECREASE) {
                        $nextLineIndentationLevel--;
                        $indentationLevel--;
                    } else {
                        $nextLineIndentationLevel++;
                    }

                    if ($indentationLevel < 0) {
                        $indentationLevel = 0;
                    }

                    $output .= str_repeat($this->options['indentation_character'], $indentationLevel) . $matches[0] . "\n";

                    break;
                }
            }
        } while ($match);

        $interpretedInput = '';
        foreach ($this->log as $e) {
            $interpretedInput .= $e['match'];
        }

        if ($interpretedInput !== $input) {
            throw new RuntimeException('Did not reproduce the exact input.');
        }

        $output = preg_replace('/(<(\w+)[^>]*>)\s*(<\/\2>)/u', '\\1\\3', $output);

        foreach ($this->ignoreElements as $key) {
            if (isset($this->temporaryReplacementsIgnore[$key])) {
                foreach ($this->temporaryReplacementsIgnore[$key] as $i => $original) {
                    $output = str_replace('<'.$key.'>' . ($i + 1) . '</'.$key.'>', $original, $output);
                }
            }
        }

        foreach ($this->temporaryReplacementsScript as $i => $original) {
            $output = str_replace('<script>' . ($i + 1) . '</script>', $original, $output);
        }

        foreach ($this->temporaryReplacementsInline as $i => $original) {
            $output = str_replace('ᐃ' . ($i + 1) . 'ᐃ', $original, $output);
        }

        return trim($output);
    }

    /**
     * Debugging utility. Get log for the last indent operation.
     *
     * @return array
     */
    public function getLog(): array
    {
        return $this->log;
    }
}
