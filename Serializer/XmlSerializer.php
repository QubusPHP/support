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

namespace Qubus\Support\Serializer;

use Qubus\Support\Serializer\Strategy\XmlStrategy;

class XmlSerializer extends Serializer
{
    public function __construct()
    {
        parent::__construct(new XmlStrategy());
    }
}
