<?php

declare(strict_types=1);

namespace Qubus\Tests\Support\Mock;

final class SerializedWakeupFixture
{
    public static bool $wokeUp = false;

    public function __wakeup(): void
    {
        self::$wokeUp = true;
    }
}
