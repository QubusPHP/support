<?php

/**
 * Qubus\Support
 *
 * @link       https://github.com/QubusPHP/support
 * @copyright  2022
 * @author     Joshua Parker <joshua@joshuaparker.dev>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 * @since      2.2.2
 */

declare(strict_types=1);

namespace Qubus\Support;

use Closure;
use FilesystemIterator;
use JSMin\JSMin;
use Minify_CSSmin;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

use function array_key_exists;
use function array_merge;
use function array_reverse;
use function array_slice;
use function array_unique;
use function array_unshift;
use function count;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function filemtime;
use function function_exists;
use function gzencode;
use function htmlentities;
use function implode;
use function in_array;
use function intval;
use function is_array;
use function is_dir;
use function is_numeric;
use function ltrim;
use function md5;
use function mkdir;
use function preg_match;
use function Qubus\Support\Helpers\camel_case;
use function realpath;
use function strlen;
use function substr;

use const DIRECTORY_SEPARATOR;
use const ENT_QUOTES;
use const PHP_EOL;

class Assets
{
    /**
     * Regex to match against a filename/url to determine if it is an asset.
     */
    protected string $assetRegex = '/.\.(css|js)$/i';

    /**
     * Regex to match against a filename/url to determine if it is a CSS asset.
     */
    protected string $cssRegex = '/.\.css$/i';

    /**
     * Regex to match against a filename/url to determine if it is a JavaScript asset.
     */
    protected string $jsRegex = '/.\.js$/i';

    /**
     * Regex to match against a filename/url to determine if it should not be minified by pipeline.
     */
    protected string $noMinificationRegex = '/.[-.]min\.(css|js)$/i';

    /**
     * Absolute path to the public directory of your App (WEBROOT).
     * Required if you enable the pipeline.
     * No trailing slash!.
     */
    protected string $publicDir;

    /**
     * Directory for local CSS assets.
     * Relative to your public directory ('public_dir').
     * No trailing slash!.
     */
    protected string $cssDir = 'css';

    /**
     * Directory for local JavaScript assets.
     * Relative to your public directory ('public_dir').
     * No trailing slash!.
     */
    protected string $jsDir = 'js';

    /**
     * Directory for local package assets.
     * Relative to your public directory ('public_dir').
     * No trailing slash!.
     */
    protected string $packagesDir = 'packages';

    /**
     * Enable assets pipeline (concatenation and minification).
     * Use a string that evaluates to `true` to provide the salt of the pipeline hash.
     * Use 'auto' to automatically calculated the salt from your assets last modification time.
     */
    protected bool|string $pipeline = false;

    /**
     * Directory for storing pipelined assets.
     * Relative to your assets directories ('css_dir' and 'js_dir').
     * No trailing slash!.
     */
    protected string $pipelineDir = 'min';

    /**
     * Enable pipelined assets compression with Gzip. Do not enable unless you know what you are doing!.
     * Useful only if your webserver supports Gzip HTTP_ACCEPT_ENCODING.
     * Set to true to use the default compression level.
     * Set an integer between 0 (no compression) and 9 (maximum compression) to choose compression level.
     */
    protected bool|int $pipelineGzip = false;

    /**
     * Closure used by the pipeline to fetch assets.
     *
     * Useful when file_get_contents() function is not available in your PHP
     * installation or when you want to apply any kind of preprocessing to
     * your assets before they get pipelined.
     *
     * The closure will receive as the only parameter a string with the path/URL of the asset, and
     * it should return the content of the asset file as a string.
     */
    protected Closure $fetchCommand;

    /**
     * Closure invoked by the pipeline whenever new assets are pipelined for the first time.
     *
     * Useful if you need to hook to the pipeline event for things such syncing your pipelined
     * assets with an external server or CDN.
     *
     * The closure will receive five parameters:
     * - String containing the name of the file that has been created.
     * - String containing the relative URL of the file.
     * - String containing the absolute path (filesystem) of the file.
     * - Array containing the assets included in the file.
     * - Boolean indicating whether a gzipped version of the file was also created.
     */
    protected Closure $notifyCommand;

    /**
     * Closure used by the pipeline to minify CSS assets.
     */
    protected Closure $cssMinifier;

    /**
     * Closure used by the pipeline to minify JavaScript assets.
     */
    protected Closure $jsMinifier;

    /**
     * Available collections.
     * Each collection is an array of assets.
     * Collections may also contain other collections.
     *
     * @var array
     */
    protected array $collections = [];

    /**
     * CSS files already added.
     * Not accepted as an option of config() method.
     *
     * @var array
     */
    protected array $css = [];

    /**
     * JavaScript files already added.
     * Not accepted as an option of config() method.
     *
     * @var array
     */
    protected array $js = [];

    /**
     * @param  array $options See config() method for details.
     * @return void
     */
    public function __construct(array $options = [])
    {
        // Forward config options
        if ($options) {
            $this->config($options);
        }
    }

    /**
     * Set up configuration options.
     *
     * All the class properties except 'js' and 'css' are accepted here.
     * Also, an extra option 'autoload' may be passed containing an array of
     * assets and/or collections that will be automatically added on startup.
     *
     * @param  array   $config Configurable options.
     */
    public function config(array $config): Assets
    {
        // Set regex options
        foreach (
            [
                'asset_regex',
                'css_regex',
                'js_regex',
                'no_minification_regex',
            ] as $option
        ) {
            $propertyOption = camel_case($option);
            if (isset($config[$option]) && (@preg_match($config[$option], '') !== false)) {
                $this->$propertyOption = $config[$option];
            }
        }

        // Set common options
        foreach (
            [
                'public_dir',
                'css_dir',
                'js_dir',
                'packages_dir',
                'pipeline',
                'pipeline_dir',
                'pipeline_gzip',
            ] as $option
        ) {
            $propertyOption = camel_case($option);

            if (isset($config[$option])) {
                $this->$propertyOption = $config[$option];
            }
        }

        // Set pipeline options
        foreach (
            [
                'fetch_command',
                'notify_command',
                'css_minifier',
                'js_minifier',
            ] as $option
        ) {
            $propertyOption = camel_case($option);

            if (isset($config[$option]) && $config[$option] instanceof Closure) {
                $this->$propertyOption = $config[$option];
            }
        }

        // Set collections
        if (isset($config['collections']) && is_array($config['collections'])) {
            $this->collections = $config['collections'];
        }

        // Autoload assets
        if (isset($config['autoload']) && is_array($config['autoload'])) {
            foreach ($config['autoload'] as $asset) {
                $this->add($asset);
            }
        }

        return $this;
    }

    /**
     * Add an asset or a collection of assets.
     *
     * It automatically detects the asset type (JavaScript, CSS or collection).
     * You may add more than one asset passing an array as argument.
     *
     * @param mixed $asset
     * @return Assets
     */
    public function add(mixed $asset): Assets
    {
        // More than one asset
        if (is_array($asset)) {
            foreach ($asset as $a) {
                $this->add($a);
            }
        } elseif (isset($this->collections[$asset])) { // Collection
            $this->add($this->collections[$asset]);
        } elseif (preg_match($this->jsRegex, $asset)) { // JavaScript asset
            $this->addJs($asset);
        } elseif (preg_match($this->cssRegex, $asset)) { // CSS asset
            $this->addCss($asset);
        }

        return $this;
    }

    /**
     * Add an asset or a collection of assets to the beginning of the queue.
     *
     * It automatically detects the asset type (JavaScript, CSS or collection).
     * You may prepend more than one asset passing an array as argument.
     *
     * @param mixed $asset
     * @return Assets
     */
    public function prepend(mixed $asset): Assets
    {
        // More than one asset
        if (is_array($asset)) {
            foreach (array_reverse($asset) as $a) {
                $this->prepend($a);
            }
        } elseif (isset($this->collections[$asset])) { // Collection
            $this->prepend($this->collections[$asset]);
        } elseif (preg_match($this->jsRegex, $asset)) { // JavaScript asset
            $this->prependJs($asset);
        } elseif (preg_match($this->cssRegex, $asset)) { // CSS asset
            $this->prependCss($asset);
        }

        return $this;
    }

    /**
     * Add a CSS asset.
     *
     * It checks for duplicates.
     * You may add more than one asset passing an array as argument.
     *
     * @param mixed $asset
     * @return Assets
     */
    public function addCss(mixed $asset): Assets
    {
        if (is_array($asset)) {
            foreach ($asset as $a) {
                $this->addCss($a);
            }

            return $this;
        }

        if (! $this->isRemoteLink($asset)) {
            $asset = $this->buildLocalLink($asset, $this->cssDir);
        }

        if (! in_array($asset, $this->css)) {
            $this->css[] = $asset;
        }

        return $this;
    }

    /**
     * Add a CSS asset to the beginning of the queue.
     *
     * It checks for duplicates.
     * You may prepend more than one asset passing an array as argument.
     *
     * @param mixed $asset
     * @return Assets
     */
    public function prependCss(mixed $asset): Assets
    {
        if (is_array($asset)) {
            foreach (array_reverse($asset) as $a) {
                $this->prependCss($a);
            }

            return $this;
        }

        if (! $this->isRemoteLink($asset)) {
            $asset = $this->buildLocalLink($asset, $this->cssDir);
        }

        if (! in_array($asset, $this->css)) {
            array_unshift($this->css, $asset);
        }

        return $this;
    }

    /**
     * Add a JavaScript asset.
     *
     * It checks for duplicates.
     * You may add more than one asset passing an array as argument.
     *
     * @param mixed $asset
     * @return Assets
     */
    public function addJs(mixed $asset): Assets
    {
        if (is_array($asset)) {
            foreach ($asset as $a) {
                $this->addJs($a);
            }

            return $this;
        }

        if (! $this->isRemoteLink($asset)) {
            $asset = $this->buildLocalLink($asset, $this->jsDir);
        }

        if (! in_array($asset, $this->js)) {
            $this->js[] = $asset;
        }

        return $this;
    }

    /**
     * Add a JavaScript asset to the beginning of the queue.
     *
     * It checks for duplicates.
     * You may prepend more than one asset passing an array as argument.
     *
     * @param mixed $asset
     * @return Assets
     */
    public function prependJs(mixed $asset): Assets
    {
        if (is_array($asset)) {
            foreach (array_reverse($asset) as $a) {
                $this->prependJs($a);
            }

            return $this;
        }

        if (! $this->isRemoteLink($asset)) {
            $asset = $this->buildLocalLink($asset, $this->jsDir);
        }

        if (! in_array($asset, $this->js)) {
            array_unshift($this->js, $asset);
        }

        return $this;
    }

    /**
     * Build the CSS `<link>` tags.
     *
     * Accepts an array of $attributes for the HTML tag.
     * You can take control of the tag rendering by
     * providing a closure that will receive an array of assets.
     *
     * @param array|Closure|null $attributes
     */
    public function css(array|Closure $attributes = null): string
    {
        if (! $this->css) {
            return '';
        }

        $assets = $this->pipeline ? [$this->cssPipeline()] : $this->css;

        if ($attributes instanceof Closure) {
            return $attributes->__invoke($assets);
        }

        // Build attributes
        $attributes = (array) $attributes;
        unset($attributes['href']);

        if (! array_key_exists('type', $attributes)) {
            $attributes['type'] = 'text/css';
        }

        if (! array_key_exists('rel', $attributes)) {
            $attributes['rel'] = 'stylesheet';
        }

        $attributes = $this->buildTagAttributes($attributes);

        // Build tags
        $output = '';
        foreach ($assets as $asset) {
            $output .= '<link href="' . $asset . '"' . $attributes . " />\n";
        }

        return $output;
    }

    /**
     * Build the JavaScript `<script>` tags.
     *
     * Accepts an array of $attributes for the HTML tag.
     * You can take control of the tag rendering by
     * providing a closure that will receive an array of assets.
     *
     * @param array|Closure|null $attributes
     */
    public function js(array|Closure $attributes = null): string
    {
        if (! $this->js) {
            return '';
        }

        $assets = $this->pipeline ? [$this->jsPipeline()] : $this->js;

        if ($attributes instanceof Closure) {
            return $attributes->__invoke($assets);
        }

        // Build attributes
        $attributes = (array) $attributes;
        unset($attributes['src']);

        if (! array_key_exists('type', $attributes)) {
            $attributes['type'] = 'text/javascript';
        }

        $attributes = $this->buildTagAttributes($attributes);

        // Build tags
        $output = '';
        foreach ($assets as $asset) {
            $output .= '<script src="' . $asset . '"' . $attributes . "></script>\n";
        }

        return $output;
    }

    /**
     * Add/replace collection.
     *
     * @param string $collectionName
     * @param array $assets
     * @return Assets
     */
    public function registerCollection(string $collectionName, array $assets): Assets
    {
        $this->collections[$collectionName] = $assets;

        return $this;
    }

    /**
     * Reset all assets.
     *
     * @return Assets
     */
    public function reset(): Assets
    {
        return $this->resetCss()->resetJs();
    }

    /**
     * Reset CSS assets.
     */
    public function resetCss(): Assets
    {
        $this->css = [];

        return $this;
    }

    /**
     * Reset JavaScript assets.
     */
    public function resetJs(): Assets
    {
        $this->js = [];

        return $this;
    }

    /**
     * Minify and concatenate CSS files.
     */
    protected function cssPipeline(): string
    {
        // If a custom minifier has been set use it, otherwise fallback to default
        $minifier = $this->cssMinifier ?? function ($buffer) {
            return Minify_CSSmin::minify($buffer);
        };

        return $this->pipeline($this->css, '.css', $this->cssDir, $minifier);
    }

    /**
     * Minify and concatenate JavaScript files.
     */
    protected function jsPipeline(): string
    {
        // If a custom minifier has been set use it, otherwise fallback to default
        $minifier = $this->jsMinifier ?? function ($buffer) {
            return JSMin::minify($buffer);
        };

        return $this->pipeline($this->js, '.js', $this->jsDir, $minifier);
    }

    /**
     * Minify and concatenate files.
     *
     * @param array $assets
     * @param string $extension
     * @param string $subdirectory
     * @param Closure $minifier
     * @return string
     */
    protected function pipeline(array $assets, string $extension, string $subdirectory, Closure $minifier): string
    {
        // Create destination dir if it doesn't exist.
        $pipelineDir = $this->publicDir . DIRECTORY_SEPARATOR
        . $subdirectory . DIRECTORY_SEPARATOR . $this->pipelineDir;
        if (! is_dir($pipelineDir)) {
            mkdir($pipelineDir, 0755, true);
        }

        // Generate paths
        $filename     = $this->calculatePipelineHash($assets) . $extension;
        $relativePath = "$subdirectory/{$this->pipelineDir}/$filename";
        $absolutePath = realpath($pipelineDir) . DIRECTORY_SEPARATOR . $filename;

        // If pipeline already exists return it
        if (file_exists($absolutePath)) {
            return $relativePath;
        }

        // Download, concatenate and minify files
        $buffer = $this->packLinks($assets, $minifier);

        // Write minified file
        file_put_contents($absolutePath, $buffer);

        // Write gzipped file
        if ($gzipAvailable = function_exists('gzencode') && $this->pipelineGzip !== false) {
            $level = $this->pipelineGzip === true ? -1 : intval($this->pipelineGzip);
            file_put_contents("$absolutePath.gz", gzencode($buffer, $level));
        }

        // Hook for pipeline event
        if ($this->notifyCommand instanceof Closure) {
            $this->notifyCommand->__invoke($filename, $relativePath, $absolutePath, $assets, $gzipAvailable);
        }

        return $relativePath;
    }

    /**
     * Calculate the pipeline hash.
     *
     * @param array $assets
     * @return string
     */
    protected function calculatePipelineHash(array $assets): string
    {
        $salt = $this->pipeline;

        // Pipeline disabled. Do not salt hash
        if (! $salt) {
            return md5(implode($assets));
        }

        // Custom salt
        if ($salt !== 'auto') {
            return md5(implode($assets) . $salt);
        }

        // Automatic salt based on the last modification time of the assets
        $timestamps = [];
        foreach ($assets as $asset) {
            if ($this->isRemoteLink($asset)) {
                continue;
            }

            $file = realpath($this->publicDir . DIRECTORY_SEPARATOR . $asset);
            if ($file === false) {
                continue;
            }

            $timestamps[] = filemtime($file);
        }

        return md5(implode($assets) . implode($timestamps));
    }

    /**
     * Download, concatenate and minify the content of several links.
     *
     * @param array $links
     * @param Closure $minifier
     * @return string
     */
    protected function packLinks(array $links, Closure $minifier): string
    {
        $buffer = '';
        foreach ($links as $link) {
            $originalLink = $link;

            // Get real link path
            if ($this->isRemoteLink($link)) {
                // Add current protocol to agnostic links
                if (substr($link, 0, 2) === '//') {
                    $protocol = isset($_SERVER['HTTPS']) &&
                    ! empty($_SERVER['HTTPS']) &&
                    $_SERVER['HTTPS'] !== 'off' ? 'https:' : 'http:';
                    $link     = $protocol . $link;
                }
            } else {
                $link = realpath($this->publicDir . DIRECTORY_SEPARATOR . $link);
                if ($link === false) {
                    continue;
                }
            }

            // Fetch link content
            $content = $this->fetchCommand instanceof Closure
            ? $this->fetchCommand->__invoke($link)
            : file_get_contents($link);

            // Minify
            $buffer .= preg_match($this->noMinificationRegex, $originalLink) ? $content : $minifier->__invoke($content);

            // Avoid JavaScript minification problems
            $buffer .= PHP_EOL;
        }

        return $buffer;
    }

    /**
     * Build link to local asset.
     *
     * Detect packages links.
     *
     * @return string the link
     */
    protected function buildLocalLink(string $asset, string $dir): string
    {
        $package = $this->assetIsFromPackage($asset);

        if ($package === false) {
            return $dir . '/' . $asset;
        }

        return $this->packagesDir . '/' . $package[0] . '/' . $package[1] . '/' . ltrim($dir, '/') . '/' . $package[2];
    }

    /**
     * Build an HTML attribute string from an array.
     *
     * @param array $attributes
     * @return string
     */
    public function buildTagAttributes(array $attributes): string
    {
        $html = [];

        foreach ($attributes as $key => $value) {
            if ($value === null) {
                continue;
            }

            if (is_numeric($key)) {
                $key = $value;
            }

            $html[] = $key . '="' . htmlentities($value, ENT_QUOTES, 'UTF-8', false) . '"';
        }

        return count($html) > 0 ? ' ' . implode(' ', $html) : '';
    }

    /**
     * Determine whether an asset is normal or from a package.
     *
     * @param string $asset
     * @return bool|array
     */
    protected function assetIsFromPackage(string $asset): bool|array
    {
        if (preg_match('{^([A-Za-z0-9_.-]+)/([A-Za-z0-9_.-]+):(.*)$}', $asset, $matches)) {
            return array_slice($matches, 1, 3);
        }

        return false;
    }

    /**
     * Determine whether a link is local or remote.
     *
     * Understands both "http://" and "https://" as well as protocol agnostic links "//"
     */
    protected function isRemoteLink(string $link): bool
    {
        return substr($link, 0, 7) === 'http://' ||
        substr($link, 0, 8) === 'https://' ||
        substr($link, 0, 2) === '//';
    }

    /**
     * Get all CSS assets already added.
     *
     * @return array
     */
    public function getCss(): array
    {
        return $this->css;
    }

    /**
     * Get all JavaScript assets already added.
     *
     * @return array
     */
    public function getJs(): array
    {
        return $this->js;
    }

    /**
     * Add all assets matching $pattern within $directory.
     *
     * @param string $directory Relative to $this->publicDir
     * @param string|null $pattern (regex)
     * @return Assets
     */
    public function addDir(string $directory, ?string $pattern = null): Assets
    {
        // Make sure directory exists
        $absolutePath = realpath($this->publicDir . DIRECTORY_SEPARATOR . $directory);
        if ($absolutePath === false) {
            return $this;
        }

        // By default, match all assets
        if ($pattern === null) {
            $pattern = $this->assetRegex;
        }

        // Get assets files
        $files = $this->rglob($absolutePath, $pattern, $this->publicDir);

        // No luck? Nothing to do
        if (! $files) {
            return $this;
        }

        // Avoid polling if the pattern is our old friend JavaScript
        if ($pattern === $this->jsRegex) {
            $this->js = array_unique(array_merge($this->js, $files));
        } elseif ($pattern === $this->cssRegex) { // Avoid polling if the pattern is our old friend CSS
            $this->css = array_unique(array_merge($this->css, $files));
        } else { // Unknown pattern. We must poll to know the asset type :(
            foreach ($files as $asset) {
                if (preg_match($this->jsRegex, $asset)) {
                    $this->js[] = $asset;
                }
                if (preg_match($this->cssRegex, $asset)) {
                    $this->css[] = $asset;
                }
            }
            $this->js  = array_unique($this->js);
            $this->css = array_unique($this->css);
        }

        return $this;
    }

    /**
     * Add all CSS assets within $directory (relative to public dir).
     *
     * @param  string $directory Relative to $this->publicDir
     * @return Assets
     */
    public function addDirCss(string $directory): static
    {
        return $this->addDir($directory, $this->cssRegex);
    }

    /**
     * Add all JavaScript assets within $directory (relative to public dir).
     *
     * @param  string $directory Relative to $this->publicDir
     * @return Assets
     */
    public function addDirJs(string $directory): static
    {
        return $this->addDir($directory, $this->jsRegex);
    }

    /**
     * Recursively get files matching $pattern within $directory.
     *
     * @param string $directory
     * @param string $pattern (regex)
     * @param string|null $ltrim Will be trimmed from the left of the file path
     * @return array
     */
    protected function rglob(string $directory, string $pattern, ?string $ltrim = null): array
    {
        $iterator = new RegexIterator(
            new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(
                    $directory,
                    FilesystemIterator::SKIP_DOTS
                )
            ),
            $pattern
        );
        $offset   = strlen($ltrim);
        $files    = [];

        foreach ($iterator as $file) {
            $files[] = substr($file->getPathname(), $offset);
        }

        return $files;
    }
}
