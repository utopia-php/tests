# Utopia Tests

> [!IMPORTANT]
> This repository is a read-only mirror of `packages/tests` in Appwrite's private Cloud repository (appwrite-labs/cloud). Development happens there, so pull requests and issues opened here are closed automatically.

A lightweight PHP testing library that provides useful testing utilities and extensions for PHPUnit.

## Installation

```bash
composer require utopia-php/tests
```

## Requirements

- PHP 8.3 or later
- PHPUnit 12.4 or later

## Features

### Async Extension

The `Async` trait provides utilities for testing asynchronous or eventually consistent behavior.

#### `assertEventually()`

Repeatedly executes a callable until it succeeds or times out. This is useful for testing:
- Asynchronous operations
- Eventually consistent systems
- Polling-based workflows
- Background jobs

**Usage:**

```php
use PHPUnit\Framework\TestCase;
use Utopia\Tests\Extensions\Async;

class MyTest extends TestCase
{
    use Async;

    public function testAsyncOperation(): void
    {
        $result = null;

        // Start some async operation
        $this->startAsyncJob(function ($data) use (&$result) {
            $result = $data;
        });

        // Wait until the result is set (max 10 seconds, check every 500ms)
        self::assertEventually(function () use (&$result) {
            $this->assertNotNull($result);
            $this->assertSame('expected', $result);
        }, timeoutMs: 10000, waitMs: 500);
    }
}
```

**Parameters:**

- `callable $probe` - The function to execute repeatedly. Should contain assertions.
- `int $timeoutMs` - Maximum time to wait in milliseconds (default: 10000)
- `int $waitMs` - Time to wait between attempts in milliseconds (default: 500)

**Critical Exceptions:**

If you need to immediately fail the test without retrying, throw a `Critical` exception:

```php
use Utopia\Tests\Extensions\Async\Exceptions\Critical;

self::assertEventually(function () use ($connection) {
    if ($connection->isClosed()) {
        throw new Critical('Connection closed unexpectedly');
    }
    $this->assertTrue($connection->hasData());
});
```

## Development

The package is developed in `packages/tests` of appwrite-labs/cloud, which supplies PHPUnit, Pint, PHPStan and Rector. From that repository's root:

```bash
bin/monorepo test tests          # unit tests
bin/monorepo check tests --fix   # Pint, PHPStan, Rector
```

On a standalone checkout of this mirror, the manifest no longer pulls in PHPUnit, so add it first:

```bash
composer install
composer require --dev phpunit/phpunit:^12
composer test
```

## License

MIT License. See [LICENSE](LICENSE) for more information.
