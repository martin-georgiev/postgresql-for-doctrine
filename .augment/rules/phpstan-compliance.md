# PHPStan Compliance

## Always Run PHPStan with Project Configuration
When verifying code quality with PHPStan, ALWAYS use the project's configuration file:

```bash
./bin/phpstan analyse --configuration=ci/phpstan/config.neon <files> --memory-limit=512M
```

Do NOT run PHPStan without the configuration file, as the project uses level `max` and has specific baselines and extensions configured.

## Type Assertions for Query Results
Query results from `executeDqlQuery()` return `mixed` values. Before using these values in assertions or operations that expect specific types:

1. **For string operations** (like `assertStringContainsString`):
   ```php
   $this->assertIsString($result[0]['result']);
   $this->assertStringContainsString('expected', $result[0]['result']);
   ```

2. **For JSON decoding**:
   ```php
   $this->assertIsString($result[0]['result']);
   $decoded = \json_decode($result[0]['result'], true);
   $this->assertIsArray($decoded);
   $this->assertSame('value', $decoded['key']);
   ```

3. **For array operations** (like `assertCount`, `assertContains`):
   ```php
   $this->assertIsArray($decoded);
   $this->assertCount(3, $decoded);
   ```

This pattern ensures PHPStan at level `max` can verify type safety.

