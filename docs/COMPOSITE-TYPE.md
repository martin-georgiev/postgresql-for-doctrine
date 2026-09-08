# PostgreSQL Composite Types

PostgreSQL [composite (row) types](https://www.postgresql.org/docs/18/rowtypes.html) bundle several named fields into a single column type. Like enums, they are user-defined - there is no pre-registered constant, so each PostgreSQL composite type maps to its own subclass of `Composite`.

> 📖 **See also**: [Available Types](AVAILABLE-TYPES.md), [Enum Types](ENUM-TYPE.md)

## How it works

[`Composite`](../src/MartinGeorgiev/Doctrine/DBAL/Types/Composite.php) is an abstract base class. You create one concrete subclass per PostgreSQL composite type. The subclass declares `TYPE_NAME` (matching the PostgreSQL type name exactly) and implements `getFieldTypes()`, which maps each field name to the Doctrine type used to convert it.

A composite column becomes a PHP `array` keyed by those field names, with each field already converted by its Doctrine type - so an `integer` field arrives as an `int`, a `date` field as a `DateTime`, a `json` field as an `array`.

> ⚠️ **Field order is the contract.** PostgreSQL record literals are positional; the field *names* exist only on the PHP side. If `getFieldTypes()` lists the fields in a different order from `CREATE TYPE`, values are silently written into the wrong columns. Keep the two in step.

## Setup

### 1. Create the PostgreSQL composite type

```sql
CREATE TYPE inventory_item AS (name text, supplier_id integer, price numeric);
```

### 2. Create the DBAL type subclass

```php
use Doctrine\DBAL\Types\Types;
use MartinGeorgiev\Doctrine\DBAL\Types\Composite;

final class InventoryItemType extends Composite
{
    protected const TYPE_NAME = 'inventory_item';

    protected function getFieldTypes(): array
    {
        return [
            'name' => Types::TEXT,
            'supplier_id' => Types::INTEGER,
            'price' => Types::DECIMAL,
        ];
    }
}
```

### 3. Register the type

```php
use Doctrine\DBAL\Types\Type;

Type::addType('inventory_item', InventoryItemType::class);
$platform->registerDoctrineTypeMapping('inventory_item', 'inventory_item');
```

### 4. Use in an entity

```php
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Product
{
    /**
     * @var array{name: string|null, supplier_id: int|null, price: string|null}|null
     */
    #[ORM\Column(type: 'inventory_item', nullable: true)]
    private ?array $item = null;
}
```

```php
$product->item = ['name' => 'widget', 'supplier_id' => 42, 'price' => '9.99'];
```

### 5. Query individual fields

Use the [`COMPOSITE_FIELD()`](AVAILABLE-FUNCTIONS-AND-OPERATORS.md) DQL function to reach a single field inside a query:

```php
$dql = "SELECT p FROM Product p WHERE COMPOSITE_FIELD(p.item, 'price') > 9.99";
```

## NULL handling

PostgreSQL distinguishes a NULL column from a row whose fields are all NULL, and so does this type:

| PHP value | Stored as |
|---|---|
| `null` | SQL `NULL` |
| `['name' => null, 'supplier_id' => null, 'price' => null]` | `(,,)` |
| `['name' => '', 'supplier_id' => null, 'price' => null]` | `("",,)` |

Note that an unquoted empty field means NULL, while `""` means the empty string. The unquoted word `NULL` is *not* a null - it is the four-character string `"NULL"`.

## Field types

Every field is converted by a registered Doctrine type, so anything Doctrine can convert works - including this library's own types, provided they are registered.

A field type whose `convertToDatabaseValue()` returns something other than a string, int, float, bool or null is rejected with `InvalidCompositeForDatabaseException` - the record literal has no way to carry it.

That includes another composite type: name it in `getFieldTypes()`, and the nested record is decomposed too. For example, a `person` whose `home` field is an `address` type will arrive as `['name' => 'bob', 'home' => ['street' => '1 Main St', 'city' => 'Sofia']]`. Declare the `home` field as `Types::TEXT` instead to receive the raw inner literal `("1 Main St",Sofia)` and parse it yourself.

## Arrays of composites

A `composite[]` column maps to a PHP array of field-keyed arrays. Extend `CompositeArray`, declare `TYPE_NAME` with the `[]` suffix and point `getCompositeClass()` at the scalar type's class:

```php
use MartinGeorgiev\Doctrine\DBAL\Types\CompositeArray;

final class InventoryItemArrayType extends CompositeArray
{
    protected const TYPE_NAME = 'inventory_item[]';

    protected function getCompositeClass(): string
    {
        return InventoryItemType::class;
    }
}
```

Register both types, as you would for any other pair of scalar and array types. PostgreSQL escapes at both levels - a field holding a comma arrives as `{"(\"a,b\",1)"}` - which the type handles for you.

## Value stability

What this type writes is byte-identical to what PostgreSQL emits for the same value, so a value does not drift across repeated save-load cycles. Three field types are re-rendered by PostgreSQL itself rather than echoed back verbatim; the PHP value still round-trips, but the stored literal differs from the one that was written:

- `timestamptz` is rendered in the session time zone (`2024-01-15 10:30:00+00` under UTC, `2024-01-15 12:30:00+02` under `Europe/Sofia`). The same instant comes back either way.
- `jsonb` normalizes whitespace and key order. Use `json` if you need to preserve the document verbatim.
- `numeric` keeps the scale you supply (`9.90` stays `9.90`) but canonicalizes exponent notation (`1e10` becomes `10000000000`).

## Mapping to an object

The base class deliberately maps to an array rather than to a class of your own - there is no single right way to hydrate an arbitrary object.
If you need to narrow that to your own class, hydrate at the entity boundary instead:

```php
#[ORM\Entity]
class Product
{
    /**
     * @var array{name: string|null, supplier_id: int|null, price: string|null}|null
     */
    #[ORM\Column(type: 'inventory_item', nullable: true)]
    private ?array $item = null;

    public function getItem(): ?InventoryItem
    {
        return $this->item === null ? null : InventoryItem::fromArray($this->item);
    }

    public function setItem(?InventoryItem $item): void
    {
        $this->item = $item?->toArray();
    }
}
```

The write direction *can* be overridden, because the parameter is untyped, if you would rather accept your object anywhere the type is bound:

```php
final class InventoryItemType extends Composite
{
    protected const TYPE_NAME = 'inventory_item';

    protected function getFieldTypes(): array
    {
        return ['name' => Types::TEXT, 'supplier_id' => Types::INTEGER, 'price' => Types::DECIMAL];
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value instanceof InventoryItem) {
            $value = $value->toArray();
        }

        return parent::convertToDatabaseValue($value, $platform);
    }
}
```

## Limitations

- **No schema introspection.** The library never reads `pg_type` to discover your fields. `getFieldTypes()` is the single source of truth, which keeps the type usable offline and in unit tests but means you must keep it aligned with your migrations by hand.
- **No schema generation or diffing.** As with enums, `CREATE TYPE` and `ALTER TYPE` statements are yours to write.

## Migrations

```sql
CREATE TYPE inventory_item AS (name text, supplier_id integer, price numeric);
ALTER TABLE products ADD COLUMN item inventory_item;
```

Unlike enums, composite types can be altered freely:

```sql
ALTER TYPE inventory_item ADD ATTRIBUTE weight numeric CASCADE;
ALTER TYPE inventory_item DROP ATTRIBUTE weight CASCADE;
ALTER TYPE inventory_item RENAME ATTRIBUTE name TO title CASCADE;
```

`CASCADE` is required when tables already use the type. Update `getFieldTypes()` after any of them.

Adding or dropping an attribute changes the field count, which is reported as `InvalidCompositeForPHPException` on the next read rather than silently mis-assigning values. A rename isn't caught: the count still matches, so reads keep succeeding and simply return the old key names.
