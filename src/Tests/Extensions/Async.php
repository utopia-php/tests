<?php

namespace Utopia\Tests\Extensions;

use PHPUnit\Framework\Assert;
use Utopia\Tests\Extensions\Async\Eventually;

const DEFAULT_TIMEOUT_MS = 10000;
const DEFAULT_WAIT_MS = 500;

trait Async
{
    /**
     * Assert that a probe callable eventually succeeds within a timeout period
     *
     * @param callable $probe The callable to execute repeatedly until it succeeds
     * @param int $timeoutMs Maximum time to wait in milliseconds
     * @param int $waitMs Time to wait between attempts in milliseconds
     * @return void
     */
    public static function assertEventually(callable $probe, int $timeoutMs = DEFAULT_TIMEOUT_MS, int $waitMs = DEFAULT_WAIT_MS): void
    {
        Assert::assertThat($probe, new Eventually($timeoutMs, $waitMs));
    }
}
