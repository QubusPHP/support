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

namespace Qubus\Support\Serializer\Transformer;

use Qubus\Support\Serializer\Serializer;

class ArrayTransformer extends BaseTransformer
{
    public function __construct()
    {
        //overwriting default constructor.
    }

    /**
     * @param mixed $value
     * @return bool|string
     */
    public function serialize(mixed $value): bool|string
    {
        $this->recursiveSetValues($value);
        $this->recursiveUnset($value, [Serializer::CLASS_IDENTIFIER_KEY]);
        $this->recursiveFlattenOneElementObjectsToScalarType($value);

        return json_encode($value);
    }
}
