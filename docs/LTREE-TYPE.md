# PostgreSQL ltree Types

PostgreSQL's `ltree` extension stores hierarchical label-tree paths (e.g. `Top.Sports.Football`) and supports ancestor/descendant queries with GiST indexes. It also ships two companion query types — `lquery` for path patterns and `ltxtquery` for full-text style label queries.

> 📖 **See also**: [Available Types](AVAILABLE-TYPES.md) | [Ltree Functions and Operators](AVAILABLE-FUNCTIONS-AND-OPERATORS.md#-ltree-functions) | [Hierarchical Data with `ltree`](USE-CASES-AND-EXAMPLES.md#hierarchical-data-with-ltree)

## Requirements

The `ltree` extension must be enabled in PostgreSQL:

```sql
CREATE EXTENSION IF NOT EXISTS ltree;
```

In Symfony, add this to the beginning of the `up()` method in any migration that introduces an `ltree` column:

```php
$this->addSql('CREATE EXTENSION IF NOT EXISTS ltree');
```

## Registration

```php
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Lquery;
use MartinGeorgiev\Doctrine\DBAL\Types\LqueryArray;
use MartinGeorgiev\Doctrine\DBAL\Types\Ltree;
use MartinGeorgiev\Doctrine\DBAL\Types\LtreeArray;
use MartinGeorgiev\Doctrine\DBAL\Types\Ltxtquery;
use MartinGeorgiev\Doctrine\DBAL\Types\LtxtqueryArray;

Type::addType(Type::LQUERY, Lquery::class);
Type::addType(Type::LQUERY_ARRAY, LqueryArray::class);
Type::addType(Type::LTREE, Ltree::class);
Type::addType(Type::LTREE_ARRAY, LtreeArray::class);
Type::addType(Type::LTXTQUERY, Ltxtquery::class);
Type::addType(Type::LTXTQUERY_ARRAY, LtxtqueryArray::class);
```

## ltree

Stores a single hierarchical path. Maps to `MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Ltree` in PHP.

```php
use Doctrine\ORM\Mapping as ORM;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Ltree;

#[ORM\Entity]
class Category
{
    #[ORM\Column(type: Type::LTREE)]
    private Ltree $path;
}

// Setting a path
$category->path = Ltree::fromString('Top.Sports.Football');

// Working with paths
$path = Ltree::fromString('Top.Sports.Football');
$path->isDescendantOf(Ltree::fromString('Top.Sports')); // true
$path->getParent();                                     // Top.Sports
$path->withLeaf('UEFA');                                // Top.Sports.Football.UEFA
```

🗃️ Doctrine can't define GiST indexes with the required ltree operator classes. Create the index manually in a migration:

```sql
CREATE INDEX category_path_gist_idx ON category USING GIST (path gist_ltree_ops(siglen=100));
```

## ltree[]

Stores an array of ltree paths. Maps to `array<Ltree>` in PHP. Null elements are supported.

```php
use Doctrine\ORM\Mapping as ORM;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Ltree;

#[ORM\Entity]
class Article
{
    #[ORM\Column(type: Type::LTREE_ARRAY)]
    private array $tags = [];
}

// Setting paths
$article->tags = [
    Ltree::fromString('Top.Sports.Football'),
    Ltree::fromString('Top.Sports.Basketball'),
];
```

## lquery

Stores a path-matching pattern for `ltree` values. Maps to `string` in PHP.

```php
use Doctrine\ORM\Mapping as ORM;
use MartinGeorgiev\Doctrine\DBAL\Type;

#[ORM\Entity]
class SavedFilter
{
    #[ORM\Column(type: Type::LQUERY)]
    private string $pattern;
}

$filter->pattern = 'Top.*{1,2}.sport@*.!football|tennis';
```

Pattern syntax, as accepted by PostgreSQL:

| Element | Meaning |
|---------|---------|
| `Top` | Matches the label `Top` exactly |
| `*` | Matches any sequence of labels |
| `*{1,2}` | Matches 1 to 2 labels; `*{2}`, `*{2,}` and `*{,2}` are also valid |
| `a\|b` | Matches label `a` or label `b` |
| `!a\|b` | Matches any label that is neither `a` nor `b` |
| `a{1,2}` | Quantifiers also apply to non-star items |
| `a@` | Case-insensitive match |
| `a*` | Prefix match |
| `a%` | Match against a `_`-separated word inside the label |

🗃️ PostgreSQL normalizes the modifier order on storage, so `sport*@` is read back as `sport@*`.

## lquery[]

Stores an array of `lquery` patterns. Maps to `array<string>` in PHP. Null elements are supported.
The array form is what the `MATCHES_ANY_LQUERY` operator consumes.

```php
$filter->patterns = ['Top.*', '!football|tennis'];
```

## ltxtquery

Stores a full-text style query over the labels of an `ltree` value. Maps to `string` in PHP.

```php
use Doctrine\ORM\Mapping as ORM;
use MartinGeorgiev\Doctrine\DBAL\Type;

#[ORM\Entity]
class SavedFilter
{
    #[ORM\Column(type: Type::LTXTQUERY)]
    private string $query;
}

$filter->query = 'Earth & Moon@* & !Transportation';
```

Words are combined with `&` (and), `|` (or) and `!` (not), and may be grouped with parentheses.
Each word accepts the same `@`, `*` and `%` modifiers as `lquery` labels.

🗃️ PostgreSQL normalizes operator spacing on storage, so `Earth&Moon` is read back as `Earth & Moon`.

## ltxtquery[]

Stores an array of `ltxtquery` queries. Maps to `array<string>` in PHP. Null elements are supported.
No PostgreSQL operator consumes this type — it is provided so that collections of saved queries can be persisted in a single column.

## Label-tree Functions

> 📖 **See also**: [AVAILABLE-FUNCTIONS-AND-OPERATORS.md](AVAILABLE-FUNCTIONS-AND-OPERATORS.md#-ltree-functions) for the full function index

### Path Manipulation Functions

#### `SUBLTREE(ltree, start, end)`
Extracts a subpath from position `start` to `end-1` (counting from 0).

```php
$dql = "SELECT SUBLTREE(e.path, 1, 2) FROM Entity e";
// subltree('Top.Child1.Child2', 1, 2) → 'Child1'
```

#### `SUBPATH(ltree, offset, len)`
Extracts a subpath starting at `offset` with length `len`. Supports negative values.

```php
$dql = "SELECT SUBPATH(e.path, 0, 2) FROM Entity e";
// subpath('Top.Child1.Child2', 0, 2) → 'Top.Child1'

$dql = "SELECT SUBPATH(e.path, -2) FROM Entity e";
// subpath('Top.Child1.Child2', -2) → 'Child1.Child2'
```

#### `SUBPATH(ltree, offset)`
Extracts from `offset` to the end.

```php
$dql = "SELECT SUBPATH(e.path, 1) FROM Entity e";
// subpath('Top.Child1.Child2', 1) → 'Child1.Child2'
```

### Path Information Functions

#### `NLEVEL(ltree)`
Returns the number of labels in the path.

```php
$dql = "SELECT NLEVEL(e.path) FROM Entity e";
// nlevel('Top.Child1.Child2') → 3
```

#### `INDEX(a, b)`
Returns the position of the first occurrence of `b` in `a`, or -1 if not found.

```php
$dql = "SELECT INDEX(e.path, 'Child1') FROM Entity e";
// index('Top.Child1.Child2', 'Child1') → 1
```

#### `INDEX(a, b, offset)`
Same as above, but starts searching from `offset`.

```php
$dql = "SELECT INDEX(e.path, 'Child1', 1) FROM Entity e";
```

### Ancestor Functions

#### `LCA(ltree1, ltree2, ...)`
Computes the longest common ancestor (up to 8 arguments).

```php
$dql = "SELECT LCA(e.path1, e.path2, e.path3) FROM Entity e";
// lca('Top.Child1.Child2', 'Top.Child1', 'Top.Child2') → 'Top'
```

### Type Conversion Functions

#### `TEXT2LTREE(text)`
Casts text to ltree.

```php
$dql = "SELECT e FROM Entity e WHERE e.path <@ TEXT2LTREE('Top.Sports')";
```

#### `LTREE2TEXT(ltree)`
Casts ltree to text.

```php
$dql = "SELECT LTREE2TEXT(e.path) FROM Entity e";
```

### Match Operators

| PostgreSQL operator | DQL function | Implementation |
|---------------------|--------------|----------------|
| `ltree ~ lquery` | `MATCHES_LQUERY` | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree\MatchesLquery` |
| `ltree ? lquery[]` | `MATCHES_ANY_LQUERY` | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree\MatchesAnyLquery` |
| `ltree @ ltxtquery` | `MATCHES_LTXTQUERY` | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree\MatchesLtxtquery` |

All three return a boolean and must be compared with `= TRUE` or `= FALSE` when used in a DQL `WHERE` clause.
The pattern argument is cast to `lquery` / `ltxtquery` in the generated SQL, so plain DQL string literals and bound parameters work without any further ceremony.

#### `MATCHES_LQUERY(ltree, lquery)`
Checks whether the path matches a single `lquery` pattern.

```php
$dql = "SELECT e FROM Entity e WHERE MATCHES_LQUERY(e.path, 'Top.*{1,2}.Football') = TRUE";
// 'Top.Sports.Football' ~ 'Top.*{1,2}.Football' → true
```

#### `MATCHES_ANY_LQUERY(ltree, lquery[])`
Checks whether the path matches any pattern in an array of `lquery` patterns.

```php
$dql = "SELECT e FROM Entity e WHERE MATCHES_ANY_LQUERY(e.path, ARRAY('Top.Sports.*', 'Top.Culture.*')) = TRUE";
```

#### `MATCHES_LTXTQUERY(ltree, ltxtquery)`
Checks whether the labels of the path satisfy an `ltxtquery`.

```php
$dql = "SELECT e FROM Entity e WHERE MATCHES_LTXTQUERY(e.path, 'Sports & !Football') = TRUE";
// 'Top.Sports.Basketball' @ 'Sports & !Football' → true
```

### DQL Examples

```php
// All descendants of Top.Sports
$dql = "SELECT e FROM Entity e WHERE e.path <@ TEXT2LTREE('Top.Sports')";

// All ancestors of a given path
$dql = "SELECT e FROM Entity e WHERE TEXT2LTREE('Top.Sports.Football') <@ e.path";

// Entities at depth 2
$dql = "SELECT e FROM Entity e WHERE NLEVEL(e.path) = 2";

// Parent path
$dql = "SELECT SUBPATH(e.path, 0, NLEVEL(e.path) - 1) FROM Entity e";

// Longest common ancestor of two entities
$dql = "SELECT LCA(e1.path, e2.path) FROM Entity e1, Entity e2 WHERE e1.id = 1 AND e2.id = 2";

// Everything under Top.Sports, at most two levels deep
$dql = "SELECT e FROM Entity e WHERE MATCHES_LQUERY(e.path, 'Top.Sports.*{1,2}') = TRUE";

// Paths mentioning Sports but not Football
$dql = "SELECT e FROM Entity e WHERE MATCHES_LTXTQUERY(e.path, 'Sports & !Football') = TRUE";

// Pattern supplied as a bound parameter
$dql = "SELECT e FROM Entity e WHERE MATCHES_LQUERY(e.path, :pattern) = TRUE";
```

### Performance

- Use GiST indexes on `ltree` columns
- `<@` and `@>` operators use those indexes automatically, as do the `~`, `?` and `@` match operators
- `SUBPATH` with negative offsets is efficient for parent extraction
- `LCA` is well-suited for finding shared ancestors in hierarchical queries
