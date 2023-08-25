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

class ArrayCollection extends Collection
{
    public function __construct(protected array $items)
    {
        parent::__construct($this->items);
    }

    protected function type(): string
    {
        return 'array';
    }
}
