# PostgreSQL Enum Types

PostgreSQL [native enum types](https://www.postgresql.org/docs/18/datatype-enum.html) are user-defined types with a fixed set of ordered string values. Unlike every other type in this library, there is no pre-registered constant - each PostgreSQL enum maps to its own subclass of `Enum`.

> 📖 **See also**: [Available Types](AVAILABLE-TYPES.md)

## How it works

[`Enum`](../src/MartinGeorgiev/Doctrine/DBAL/Types/Enum.php) is an abstract base class. You create one concrete subclass per PostgreSQL enum type. The subclass declares `TYPE_NAME` (matching the PostgreSQL type name exactly) and implements `getEnumClass()` returning the fully-qualified name of a PHP `BackedEnum`.

**Only string-backed PHP enums are supported.** PostgreSQL enum values are text labels, so int-backed enums cannot be mapped correctly.

## Setup

### 1. Create the PostgreSQL enum type

```sql
CREATE TYPE order_status AS ENUM ('pending', 'processing', 'shipped', 'cancelled');
```

### 2. Define a PHP-backed enum

Values must match the PostgreSQL enum cases exactly (case-sensitive).

```php
enum OrderStatus: string
{
    case PENDING    = 'pending';
    case PROCESSING = 'processing';
    case SHIPPED    = 'shipped';
    case CANCELLED  = 'cancelled';
}
```

### 3. Create the DBAL type subclass

```php
use MartinGeorgiev\Doctrine\DBAL\Types\Enum;

final class OrderStatusType extends Enum
{
    protected const TYPE_NAME = 'order_status';

    protected function getEnumClass(): string
    {
        return OrderStatus::class;
    }
}
```

The `TYPE_NAME` constant must match the PostgreSQL type name exactly - it is used as both the DBAL type name and the SQL declaration.

### 4. Register the type

```php
use Doctrine\DBAL\Types\Type;

Type::addType('order_status', OrderStatusType::class);
```

### 5. Use in an entity

```php
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Order
{
    #[ORM\Column(type: 'order_status')]
    private OrderStatus $status;
}
```

## Multiple enum types

Each PostgreSQL enum requires its own subclass and `addType` call.

```php
// Two PostgreSQL enums → two subclasses
final class OrderStatusType extends Enum
{
    protected const TYPE_NAME = 'order_status';
    protected function getEnumClass(): string { return OrderStatus::class; }
}

final class PaymentMethodType extends Enum
{
    protected const TYPE_NAME = 'payment_method';
    protected function getEnumClass(): string { return PaymentMethod::class; }
}

Type::addType('order_status', OrderStatusType::class);
Type::addType('payment_method', PaymentMethodType::class);
```

## Migrations

### Adding a new enum type

```sql
CREATE TYPE order_status AS ENUM ('pending', 'processing', 'shipped', 'cancelled');
ALTER TABLE orders ADD COLUMN status order_status NOT NULL DEFAULT 'pending';
```

### Adding a new case

`ALTER TYPE ... ADD VALUE` cannot run inside a transaction. In Symfony Migrations, use `$this->addSql()` directly. Doctrine Migrations wraps each migration in a transaction by default, so you must opt out:

```php
public function preUp(Schema $schema): void
{
    $this->connection->executeStatement('ALTER TYPE order_status ADD VALUE \'returned\'');
}

public function up(Schema $schema): void
{
    // other schema changes that can run in a transaction
}
```

Alternatively, run it outside a transaction block in your migration tool.

### Renaming or removing a case

PostgreSQL does not support removing or renaming enum cases. The workaround is to create a new type and migrate the column:

```sql
CREATE TYPE order_status_new AS ENUM ('pending', 'processing', 'shipped', 'cancelled', 'returned');
ALTER TABLE orders ALTER COLUMN status TYPE order_status_new USING status::text::order_status_new;
DROP TYPE order_status;
ALTER TYPE order_status_new RENAME TO order_status;
```
