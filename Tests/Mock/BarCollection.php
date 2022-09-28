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

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Qubus\Support\Assets;
use Qubus\Support\Collection\Collection;
use ReflectionClass;

use function array_pop;
use function uniqid;

class BarCollection extends Collection
{
    protected function type(): string
    {
        return Bar::class;
    }
}
