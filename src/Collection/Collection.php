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

namespace Qubus\Support\Collection;

use Countable;

use function count;

abstract class Collection extends BaseCollection implements Countable
{
    /**
     * @param array<mixed> $items
     */
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
}
