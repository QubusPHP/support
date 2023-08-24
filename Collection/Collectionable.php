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

namespace Qubus\Support\Collection;

interface Collectionable
{
    /**
     * Retrieve all items from the collection.
     *
     * @return array
     */
    public function all(): array;
}
