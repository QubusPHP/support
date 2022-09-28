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

use Countable;

use function count;

abstract class Collection extends BaseCollection implements Countable
{
    public function __construct(protected array $items)
    {
        parent::__construct($this->type(), $this->items);
    }

    /**
     * Collection type.
     *
     * @return string
     */
    abstract protected function type(): string;

    /**
     * Total number of collections.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->items());
    }

    /**
     * Returns an array of collections.
     *
     * @return array
     */
    public function items(): array
    {
        return $this->items;
    }
}
