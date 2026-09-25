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

// Schema tools (validation, migration diffs) also need PostgreSQL's type name mapped back to it:
$platform = $em->getConnection()->getDatabasePlatform();
$platform->registerDoctrineTypeMapping('order_status', 'order_status');
```

Without that mapping, schema introspection fails with `Unknown database type "order_status" requested`. The framework equivalents:

- **Symfony**: `order_status: order_status` under `doctrine.dbal.connections.default.mapping_types` in `config/packages/doctrine.yaml` ([setup guide](INTEGRATING-WITH-SYMFONY.md#configure-type-mappings))
- **Laravel**: `'order_status' => 'order_status'` under the entity manager's `'mapping_types'` in `config/doctrine.php` ([setup guide](INTEGRATING-WITH-LARAVEL.md#register-dbal-types))

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

Each PostgreSQL enum requires its own subclass, `addType` call and platform mapping.

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

// Schema tools (validation, migration diffs) also need PostgreSQL's type names mapped back to them:
$platform = $em->getConnection()->getDatabasePlatform();
$platform->registerDoctrineTypeMapping('order_status', 'order_status');
$platform->registerDoctrineTypeMapping('payment_method', 'payment_method');
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

// Schema tools (validation, migration diffs) also need PostgreSQL's type name mapped back to it:
$platform = $em->getConnection()->getDatabasePlatform();
$platform->registerDoctrineTypeMapping('_order_status', 'order_status[]');
```

PostgreSQL reports an array column's type as the element type prefixed with an underscore (`_order_status`), so that is the name to map. The framework equivalents:

- **Symfony**: `_order_status: 'order_status[]'` under `doctrine.dbal.connections.default.mapping_types` ([setup guide](INTEGRATING-WITH-SYMFONY.md#configure-type-mappings))
- **Laravel**: `'_order_status' => 'order_status[]'` under the entity manager's `'mapping_types'` ([setup guide](INTEGRATING-WITH-LARAVEL.md#register-dbal-types))

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

Doctrine's schema tool models tables, not user-defined types, and PostgreSQL constrains what a generated migration could safely do anyway: a label added by `ALTER TYPE ... ADD VALUE` cannot be used until the transaction that added it commits, and labels can be renamed but not removed. Automatic creation and diffing would therefore have to guess at transactional boundaries and at recreate-and-migrate strategies. Writing the statements yourself keeps that sequencing in your migration tool, where it belongs.

### Adding a new case

Since PostgreSQL 12, `ALTER TYPE ... ADD VALUE` is allowed inside a transaction block, but the new label cannot be used until that transaction commits - PostgreSQL rejects it with `unsafe use of new value`. A plain `addSql()` in a Doctrine Migrations `up()` is therefore enough on its own:

```php
public function up(Schema $schema): void
{
    $this->addSql("ALTER TYPE order_status ADD VALUE 'returned'");
}
```

Doctrine Migrations wraps each migration in a single transaction, and `preUp()` runs inside it too. When the same migration also uses the new label (a backfill `UPDATE`, a column `DEFAULT`), move that work into a later migration or make this one non-transactional. On PostgreSQL 11 and earlier, which refuse `ADD VALUE` on an existing type inside a transaction block, only the non-transactional route works:

```php
public function isTransactional(): bool
{
    return false;
}
```

Both need the `all_or_nothing` option off: it runs every migration in one transaction, and refuses a non-transactional one.

### Renaming a case

`ALTER TYPE ... RENAME VALUE` (PostgreSQL 10+) renames a label in place. PostgreSQL stores an enum value by the label's OID, not its text, so existing rows - array elements included - read back under the new name without being rewritten. The statement is safe inside the migration transaction, and the new label is usable straight away in the same transaction:

```php
public function up(Schema $schema): void
{
    $this->addSql("ALTER TYPE order_status RENAME VALUE 'shipped' TO 'dispatched'");
}
```

Change the matching PHP enum case's backing value in the same deploy. Once the label is renamed, PostgreSQL rejects the old spelling on write, and the old PHP enum has no case for the new one on read (`InvalidEnumForPHPException`).

### Removing a case

PostgreSQL cannot remove an enum label (`DROP VALUE` is not implemented). Instead, move the data off the label, create a type without it, convert every column that uses the old type, and swap the names. Removing `processing` from the `orders` table above:

```php
public function up(Schema $schema): void
{
    $this->addSql("UPDATE orders SET status = 'pending' WHERE status = 'processing'");
    $this->addSql("UPDATE orders SET status_trail = array_remove(status_trail, 'processing')");
    $this->addSql("CREATE TYPE order_status_new AS ENUM ('pending', 'shipped', 'cancelled', 'returned')");
    $this->addSql('ALTER TABLE orders ALTER COLUMN status DROP DEFAULT, ALTER COLUMN status_trail DROP DEFAULT');
    $this->addSql('ALTER TABLE orders
        ALTER COLUMN status TYPE order_status_new USING status::text::order_status_new,
        ALTER COLUMN status_trail TYPE order_status_new[] USING status_trail::text[]::order_status_new[]');
    $this->addSql('DROP TYPE order_status');
    $this->addSql('ALTER TYPE order_status_new RENAME TO order_status');
    $this->addSql("ALTER TABLE orders ALTER COLUMN status SET DEFAULT 'pending', ALTER COLUMN status_trail SET DEFAULT '{}'");
}
```

A row still holding the removed label makes the conversion fail, so decide where those rows go first. The defaults are dropped and restored because PostgreSQL cannot cast a default to the new type automatically. The whole sequence runs inside the migration's transaction. Remove the matching case from the PHP enum in the same deploy.
