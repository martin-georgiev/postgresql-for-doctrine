# <picture><source media="(prefers-color-scheme: dark)" srcset="assets/logo-dark.svg"><img src="assets/logo.svg" alt="" width="32" height="32" align="absmiddle"></picture> PostgreSQL Enum Types

PostgreSQL [native enum types](https://www.postgresql.org/docs/18/datatype-enum.html) are user-defined types with a fixed set of ordered string values. Unlike every other type in this library, there is no pre-registered constant - each PostgreSQL enum maps to its own subclass of `Enum`, and each array of one to its own subclass of `EnumArray`.

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
use Doctrine\DBAL\Types\Type as DoctrineType;

DoctrineType::addType('order_status', OrderStatusType::class);
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

DoctrineType::addType('order_status', OrderStatusType::class);
DoctrineType::addType('payment_method', PaymentMethodType::class);
```

## Arrays of enum values

A column declared as `order_status[]` maps to a PHP array of `OrderStatus` cases through
[`EnumArray`](../src/MartinGeorgiev/Doctrine/DBAL/Types/EnumArray.php). It is configured exactly like `Enum`: one
concrete subclass per PostgreSQL enum, declaring `TYPE_NAME` and implementing `getEnumClass()`. The only difference is
that `TYPE_NAME` carries the `[]` suffix.

```php
use MartinGeorgiev\Doctrine\DBAL\Types\EnumArray;

final class OrderStatusArrayType extends EnumArray
{
    protected const TYPE_NAME = 'order_status[]';

    protected function getEnumClass(): string
    {
        return OrderStatus::class;
    }
}

DoctrineType::addType('order_status[]', OrderStatusArrayType::class);
```

The scalar and the array type are independent registrations - add whichever ones your schema uses.

```sql
CREATE TABLE orders (
    id           SERIAL PRIMARY KEY,
    status       order_status NOT NULL,
    status_trail order_status[] NOT NULL DEFAULT '{}'
);
```

```php
#[ORM\Entity]
class Order
{
    #[ORM\Column(type: 'order_status')]
    private OrderStatus $status;

    /**
     * @var array<int, OrderStatus>
     */
    #[ORM\Column(type: 'order_status[]')]
    private array $statusTrail = [];
}
```

### Label escaping

PostgreSQL enum labels are arbitrary text, so a label may contain a comma, a space, a double quote, a backslash or be
empty. `EnumArray` quotes and escapes every element it writes, and reverses that on read, so all of those round-trip
unchanged. Labels that merely look numeric or boolean (`'42'`, `'true'`) also survive as labels rather than being
coerced to PHP scalars.

### NULL elements

A `NULL` element inside the array maps to a PHP `null` entry, and `null` entries are written back as SQL `NULL`. This is
distinct from a `NULL` column, which maps to `null` instead of an array.

```php
$order->statusTrail = [OrderStatus::PENDING, null, OrderStatus::SHIPPED]; // {"pending",NULL,"shipped"}
```

A label spelled exactly `NULL` stays a label. PostgreSQL quotes it (`"NULL"`) and leaves a real NULL element bare, so the
two round-trip distinctly:

```php
$order->statusTrail = [Status::NULL_LABEL, null]; // {"NULL",NULL} — first is the label, second is SQL NULL
```

## Migrations

### Adding a new enum type

Write the statement by hand, keeping the labels in step with the PHP enum's cases:

```sql
CREATE TYPE order_status AS ENUM ('pending', 'processing', 'shipped', 'cancelled');
ALTER TABLE orders ADD COLUMN status order_status NOT NULL DEFAULT 'pending';
```

### Why the library does not create the type for you

Doctrine's schema tool models tables, not user-defined types, and PostgreSQL constrains what a generated migration could safely do anyway: `ALTER TYPE ... ADD VALUE` cannot run inside a transaction, and labels can be neither renamed nor removed. Automatic creation and diffing would therefore have to guess at transactional boundaries and at recreate-and-migrate strategies. Writing the statements yourself keeps that sequencing in your migration tool, where it belongs.

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
