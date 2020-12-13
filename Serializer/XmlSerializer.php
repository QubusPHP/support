<?php

/**
 * Qubus\Support
 *
 * @link       https://github.com/QubusPHP/support
 * @copyright  2020 Joshua Parker
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 *
 * @since      1.0.0
 */

declare(strict_types=1);

namespace Qubus\Support\Serializer;

use Qubus\Support\Serializer\Strategy\XmlStrategy;

class XmlSerializer extends Serializer
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct(new XmlStrategy());
    }
}
