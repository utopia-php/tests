<?php

namespace Utopia\Tests\Extensions\Async\Exceptions;

/**
 * Critical exception that should not be retried
 *
 * When thrown from a probe callable, it will immediately fail the test
 * without waiting for the timeout period
 */
class Critical extends \Exception
{
}
