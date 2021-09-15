<?php

/**
 * Qubus\Support
 *
 * @link       https://github.com/QubusPHP/support
 * @copyright  2020 Joshua Parker <josh@joshuaparker.blog>
 * @copyright  2015 Nil Portugués Calderó
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 *
 * @since      1.0.0
 */

declare(strict_types=1);

namespace Qubus\Support\Serializer\Strategy;

class NullStrategy implements Strategy
{
    /**
     * @param mixed $value
     * @return string
     */
    public function serialize($value)
    {
        return $value;
    }

    /**
     * @param string $value
     * @return array
     */
    public function unserialize($value)
    {
        return $value;
    }
}
