<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Utopia\Tests\Extensions\Async;
use Utopia\Tests\Extensions\Async\Exceptions\Critical;

class AsyncTest extends TestCase
{
    use Async;

    public function testEventuallySucceeds(): void
    {
        $counter = 0;

        // This will retry until $counter reaches 3
        self::assertEventually(function () use (&$counter) {
            $counter++;
            $this->assertGreaterThan(2, $counter);
        }, timeoutMs: 5000, waitMs: 100);

        $this->assertGreaterThanOrEqual(3, $counter);
    }

    public function testEventuallyTimesOut(): void
    {
        $this->expectException(\PHPUnit\Framework\ExpectationFailedException::class);
        $this->expectExceptionMessage('This should never pass');

        // This will always fail and timeout
        self::assertEventually(function () {
            // @phpstan-ignore method.impossibleType (intentionally testing failure case)
            $this->assertTrue(false, 'This should never pass');
        }, timeoutMs: 1000, waitMs: 100);
    }

    public function testCriticalExceptionStopsRetrying(): void
    {
        $counter = 0;

        $this->expectException(Critical::class);

        self::assertEventually(function () use (&$counter) {
            $counter++;
            if ($counter === 2) {
                throw new Critical('Critical error occurred');
            }
            $this->fail('Should fail before reaching this');
        }, timeoutMs: 5000, waitMs: 100);

        // Counter should be exactly 2, not more
        $this->assertEquals(2, $counter);
    }

    public function testDefaultTimeout(): void
    {
        $executed = false;

        // Uses default timeout (10000ms) and wait (500ms)
        self::assertEventually(function () use (&$executed) {
            $executed = true;
        });

        $this->assertTrue($executed);
    }

    public function testInvalidProbeThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        // PHP 8+ will throw TypeError for non-callable argument
        // @phpstan-ignore argument.type
        self::assertEventually('not a callable');
    }

    public function testMultipleAssertionsInProbe(): void
    {
        $counter = 0;

        self::assertEventually(function () use (&$counter) {
            $counter++;
            $this->assertGreaterThan(0, $counter);
            $this->assertLessThanOrEqual(10, $counter);
            $this->assertEquals(3, $counter); // Will pass on 3rd attempt
        }, timeoutMs: 3000, waitMs: 100);

        $this->assertEquals(3, $counter);
    }
}
