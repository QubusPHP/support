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

namespace Qubus\Support\Traits;

trait SortCallbackAware
{
    /**
     * Protected callback function for the usort function.
     *
     * @param array $a First array.
     * @param array $b Second Array.
     * @return int Comparison
     */
    protected function afsort(array $a, array $b): int
    {
        if (isset($a['priority']) && isset($b['priority'])) {
            $priority1 = (int) $a['priority'];
            $priority2 = (int) $b['priority'];

            if ($priority1 < $priority2) {
                return -1;
            } elseif ($priority1 > $priority2) {
                return 1;
            }
        }
        return 0;
    }
}
