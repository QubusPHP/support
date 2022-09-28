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
