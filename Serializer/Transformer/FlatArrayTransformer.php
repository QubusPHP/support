<?php

/**
 * Qubus\Support
 *
 * @link       https://github.com/QubusPHP/support
 * @copyright  2022
 * @author     Joshua Parker <joshua@joshuaparker.dev>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 */

declare(strict_types=1);

namespace Qubus\Support\Serializer\Transformer;

use function is_array;

class FlatArrayTransformer extends ArrayTransformer
{
    /**
     * @param mixed $value
     * @return bool|string
     */
    public function serialize(mixed $value): bool|string
    {
        $array = [parent::serialize($value)];
        $flattenArray = $this->flatten($array);
        return json_encode($flattenArray);
    }

    /**
     * @param array  $array
     * @param string $prefix
     * @return array
     */
    private function flatten(array $array, string $prefix = ''): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result += $this->flatten($value, $prefix . $key . '.');
            } else {
                $result[$prefix . $key] = $value;
            }
        }

        return $result;
    }
}
