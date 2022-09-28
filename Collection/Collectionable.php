<?php

/**
 * Qubus\Support
 *
 * @link       https://github.com/QubusPHP/support
 * @copyright  2022
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 *
 * @author     Joshua Parker <josh@joshuaparker.blog>
 * @since      2.2.2
 */

declare(strict_types=1);

namespace Qubus\Support\Collection;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Qubus\Support\Serializable;

interface Collectionable
{
    /**
     * Retrieve all items from the collection.
     *
     * @return array
     */
    public function all(): array;
}
