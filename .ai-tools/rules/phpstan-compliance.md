---
description: "PHPStan level max compliance: always use project config, type-assert mixed query results"
alwaysApply: true
trigger: always_on
applyTo: "**"
type: always_apply
---

# PHPStan Compliance

## Always Run PHPStan with Project Configuration

**Required**:

```bash
./bin/phpstan analyse --configuration=ci/phpstan/config.neon <files> --memory-limit=512M
```

Reason: the project runs at level `max` with project-specific baselines and extensions. Running without `--configuration` skips them.

## Type Assertions for Query Results

`executeDqlQuery()` returns `mixed`. Before assertions that expect a concrete type:

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

