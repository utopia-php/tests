<?php

namespace Utopia\Test\Extensions\Async;

use PHPUnit\Framework\Constraint\Constraint;
use Utopia\Test\Extensions\Async\Exceptions\Critical;

/**
 * Constraint that retries a probe callable until it succeeds or times out
 */
final class Eventually extends Constraint
{
    public function __construct(private int $timeoutMs, private int $waitMs)
    {
    }

    /**
     * Evaluates the constraint for parameter $probe
     *
     * @param mixed $probe The callable to evaluate
     * @param string $description Additional information about the test
     * @param bool $returnResult Whether to return a result or throw an exception
     * @return bool
     * @throws \Exception If the probe never succeeds within the timeout
     */
    public function evaluate(mixed $probe, string $description = '', bool $returnResult = false): bool
    {
        if (!is_callable($probe)) {
            throw new \InvalidArgumentException('Probe must be a callable');
        }

        $start = microtime(true);
        $lastException = null;

        do {
            try {
                $probe();
                return true;
            } catch (Critical $exception) {
                throw $exception;
            } catch (\Exception $exception) {
                $lastException = $exception;
            }

            usleep($this->waitMs * 1000);
        } while (microtime(true) - $start < $this->timeoutMs / 1000);

        if ($returnResult) {
            return false;
        }

        throw $lastException;
    }

    /**
     * Returns the description of the failure
     *
     * @param mixed $other Evaluated value or object
     * @return string
     */
    protected function failureDescription(mixed $other): string
    {
        return 'the given probe was satisfied within ' . $this->timeoutMs . 'ms.';
    }

    /**
     * Returns a string representation of the constraint
     *
     * @return string
     */
    public function toString(): string
    {
        return 'Eventually';
    }
}
