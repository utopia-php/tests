# Utopia Test

A lightweight PHP testing library that provides useful testing utilities and extensions for PHPUnit.

## Installation

```bash
composer require utopia-php/test
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
use Utopia\Test\Extensions\Async;

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
            $this->assertEquals('expected', $result);
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
use Utopia\Test\Extensions\Async\Exceptions\Critical;

self::assertEventually(function () use ($connection) {
    if ($connection->isClosed()) {
        throw new Critical('Connection closed unexpectedly');
    }
    $this->assertTrue($connection->hasData());
});
```

## Development

### Run Tests

```bash
composer install --ignore-platform-reqs
composer test
```

### Code Formatting

```bash
composer format
```

### Static Analysis

```bash
composer check
```

### Linting

```bash
composer lint
```

## License

MIT License. See [LICENSE](LICENSE) for more information.
