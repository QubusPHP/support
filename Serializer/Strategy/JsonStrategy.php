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

namespace Qubus\Support\Serializer\Strategy;

use function json_decode;
use function json_encode;

use const JSON_UNESCAPED_UNICODE;

class JsonStrategy implements Strategy
{
    /**
     * @param mixed $data
     * @return string
     */
    public function serialize($data)
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param $data
     * @return array
     */
    public function unserialize($data)
    {
        return json_decode($data, true);
    }
}
