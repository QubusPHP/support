<?php

declare(strict_types=1);

/**
 * Qubus\Support
 *
 * @link       https://github.com/QubusPHP/support
 * @copyright  2022
 * @author     Joshua Parker <josh@joshuaparker.blog>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 *
 * @since      2.2.2
 */

namespace Qubus\Tests\Support\Mock;

use Qubus\Support\Collection\Collection;

class BarCollection extends Collection
{
    protected function type(): string
    {
        return Bar::class;
    }
}
