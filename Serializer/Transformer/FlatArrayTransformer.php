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

namespace Qubus\Support\Serializer\Transformer;

class FlatArrayTransformer extends ArrayTransformer
{
    /**
     * @param mixed $value
     *
     * @return string
     */
    public function serialize($value)
    {
        return $this->flatten(parent::serialize($value));
    }

    /**
     * @param array  $array
     * @param string $prefix
     *
     * @return array
     */
    private function flatten(array $array, $prefix = '')
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result = array_merge($result, $this->flatten($value, $prefix.$key.'.'));
            } else {
                $result[$prefix.$key] = $value;
            }
        }

        return $result;
    }
}
