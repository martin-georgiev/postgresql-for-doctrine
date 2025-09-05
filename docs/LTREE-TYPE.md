# ltree type usage

## Requirements

The `ltree` data type requires enabling the [`ltree` extension](https://www.postgresql.org/docs/16/ltree.html)
in PostgreSQL.

```sql
CREATE EXTENSION IF NOT EXISTS ltree;
```

For [Symfony](https://symfony.com/),
customize the migration that introduces the `ltree` field by adding this line
at the beginning of the `up()` method:

```php
$this->addSql('CREATE EXTENSION IF NOT EXISTS ltree');
```

## Usage

An example implementation (for a Symfony project) is:

```php
<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Ltree;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

/**
 * Manually edit `my_entity_path_gist_idx` in migration to use GIST.
 * Declaring the index using Doctrine attributes prevents its removal during migrations.
 */
#[ORM\Entity()]
#[ORM\Index(columns: ['path'], name: 'my_entity_path_gist_idx')]
class MyEntity implements \Stringable
{
    #[ORM\Column(type: UuidType::NAME)]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\Id()]
    private Uuid $id;

    #[ORM\Column(type: 'ltree')]
    private Ltree $path;

    /**
     * @var Collection<array-key,MyEntity> $children
     */
    #[ORM\OneToMany(targetEntity: MyEntity::class, mappedBy: 'parent')]
    private Collection $children;

    public function __construct(
        #[ORM\Column(unique: true, length: 128)]
        private string $name,

        #[ORM\ManyToOne(targetEntity: MyEntity::class, inversedBy: 'children')]
        private ?MyEntity $parent = null,
    ) {
        $this->id = Uuid::v7();
        $this->children = new ArrayCollection();

        $this->path = Ltree::fromString($this->id->toBase58());
        if ($parent instanceof MyEntity) {
            // Initialize the path using the parent.
            $this->setParent($parent);
        }
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->name;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getParent(): ?MyEntity
    {
        return $this->parent;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPath(): Ltree
    {
        return $this->path;
    }

    /**
     * @return Collection<array-key,MyEntity>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setParent(MyEntity $parent): void
    {
        if ($parent->getId()->equals($this->id)) {
            throw new \InvalidArgumentException("Parent MyEntity can't be self");
        }

        // Prevent cycles: the parent can't be a descendant of the current node.
        if ($parent->getPath()->isDescendantOf($this->getPath())) {
            throw new \InvalidArgumentException("Parent MyEntity can't be a descendant of the current MyEntity");
        }

        $this->parent = $parent;

        // Use withLeaf() to create a new Ltree instance
        // with the parent's path and the current entity's ID.
        $this->path = $parent->getPath()->withLeaf($this->id->toBase58());
    }
}
```

🗃️ Doctrine's schema tool can't define PostgreSQL [GiST](https://www.postgresql.org/docs/16/gist.html)
or [GIN](https://www.postgresql.org/docs/16/gin.html) indexes with the required ltree operator classes.
Create the index via a manual `CREATE INDEX` statement in your migration:

```sql
-- Example GiST index for ltree with a custom signature length (must be a multiple of 4)
CREATE INDEX my_entity_path_gist_idx
    ON my_entity USING GIST (path gist_ltree_ops(siglen = 100));
-- Alternative: GIN index for ltree
CREATE INDEX my_entity_path_gin_idx
    ON my_entity USING GIN (path gin_ltree_ops);
```

⚠️ **Important**: Changing an entity's parent requires cascading the change
to all its children.
This is not handled automatically by Doctrine.
Implement an [onFlush](https://www.doctrine-project.org/projects/doctrine-orm/en/3.3/reference/events.html#reference-events-on-flush)
[Doctrine entity listener](https://symfony.com/doc/7.3/doctrine/events.html#doctrine-lifecycle-listeners)
to handle updating the `path` column of the updated entity's children
when `path` is present in the change set:

```php
<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\MyEntity;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\UnitOfWork;

#[AsDoctrineListener(event: Events::onFlush, priority: 500, connection: 'default')]
final readonly class MyEntityOnFlushListener
{
    public function onFlush(OnFlushEventArgs $eventArgs): void
    {
        $entityManager = $eventArgs->getObjectManager();
        $unitOfWork = $entityManager->getUnitOfWork();
        $entityMetadata = $entityManager->getClassMetadata(MyEntity::class);

        foreach ($unitOfWork->getScheduledEntityUpdates() as $entity) {
            $this->processEntity($entity, $entityMetadata, $unitOfWork);
        }
    }

    /**
     * @param ClassMetadata<MyEntity> $entityMetadata
     */
    private function processEntity(object $entity, ClassMetadata $entityMetadata, UnitOfWork $unitOfWork): void
    {
        if (!$entity instanceof MyEntity) {
            return;
        }

        $changeset = $unitOfWork->getEntityChangeSet($entity);

        // check if $entity->path has changed
        // If the path stays the same, no need to update children
        if (!isset($changeset['path'])) {
            return;
        }

        $this->updateChildrenPaths($entity, $entityMetadata, $unitOfWork);
    }

    /**
     * @param ClassMetadata<MyEntity> $entityMetadata
     */
    private function updateChildrenPaths(MyEntity $entity, ClassMetadata $entityMetadata, UnitOfWork $unitOfWork): void
    {
        foreach ($entity->getChildren() as $child) {
            // call the setParent method on the child, which recomputes its Ltree path.
            $child->setParent($entity);

            $unitOfWork->recomputeSingleEntityChangeSet($entityMetadata, $child);

            // cascade the update to the child's children
            $this->updateChildrenPaths($child, $entityMetadata, $unitOfWork);
        }
    }
}
```

## Ltree Functions

This library provides DQL functions for all PostgreSQL ltree operations. These functions allow you to work with ltree data directly in your Doctrine queries.

### Path Manipulation Functions

#### `SUBLTREE(ltree, start, end)`
Extracts a subpath from an ltree from position `start` to position `end-1` (counting from 0).

```php
// DQL
$dql = "SELECT SUBLTREE(e.path, 1, 2) FROM Entity e";
// SQL: subltree(e.path, 1, 2)
// Example: subltree('Top.Child1.Child2', 1, 2) → 'Child1'
```

#### `SUBPATH(ltree, offset, len)`
Extracts a subpath starting at position `offset` with length `len`. Supports negative values.

```php
// DQL
$dql = "SELECT SUBPATH(e.path, 0, 2) FROM Entity e";
// SQL: subpath(e.path, 0, 2)
// Example: subpath('Top.Child1.Child2', 0, 2) → 'Top.Child1'

// With negative offset
$dql = "SELECT SUBPATH(e.path, -2) FROM Entity e";
// SQL: subpath(e.path, -2)
// Example: subpath('Top.Child1.Child2', -2) → 'Child1.Child2'
```

#### `SUBPATH(ltree, offset)`
Extracts a subpath starting at position `offset` extending to the end of the path.

```php
// DQL
$dql = "SELECT SUBPATH(e.path, 1) FROM Entity e";
// SQL: subpath(e.path, 1)
// Example: subpath('Top.Child1.Child2', 1) → 'Child1.Child2'
```

### Path Information Functions

#### `NLEVEL(ltree)`
Returns the number of labels in the path.

```php
// DQL
$dql = "SELECT NLEVEL(e.path) FROM Entity e";
// SQL: nlevel(e.path)
// Example: nlevel('Top.Child1.Child2') → 3
```

#### `INDEX(a, b)`
Returns the position of the first occurrence of `b` in `a`, or -1 if not found.

```php
// DQL
$dql = "SELECT INDEX(e.path, 'Child1') FROM Entity e";
// SQL: index(e.path, 'Child1')
// Example: index('Top.Child1.Child2', 'Child1') → 1
```

#### `INDEX(a, b, offset)`
Returns the position of the first occurrence of `b` in `a` starting at position `offset`.

```php
// DQL
$dql = "SELECT INDEX(e.path, 'Child1', 1) FROM Entity e";
// SQL: index(e.path, 'Child1', 1)
// Example: index('Top.Child1.Child2', 'Child1', 1) → 1
```

### Ancestor Functions

#### `LCA(ltree1, ltree2, ...)`
Computes the longest common ancestor of multiple paths (up to 8 arguments supported).

```php
// DQL
$dql = "SELECT LCA(e.path1, e.path2) FROM Entity e";
// SQL: lca(e.path1, e.path2)
// Example: lca('Top.Child1.Child2', 'Top.Child1') → 'Top'

// With multiple paths
$dql = "SELECT LCA(e.path1, e.path2, e.path3) FROM Entity e";
// SQL: lca(e.path1, e.path2, e.path3)
// Example: lca('Top.Child1.Child2', 'Top.Child1', 'Top.Child2.Child3') → 'Top'
```

### Type Conversion Functions

#### `TEXT2LTREE(text)`
Casts text to ltree.

```php
// DQL
$dql = "SELECT TEXT2LTREE('Top.Child1.Child2') FROM Entity e";
// SQL: text2ltree('Top.Child1.Child2')
// Example: text2ltree('Top.Child1.Child2') → 'Top.Child1.Child2'::ltree
```

#### `LTREE2TEXT(ltree)`
Casts ltree to text.

```php
// DQL
$dql = "SELECT LTREE2TEXT(e.path) FROM Entity e";
// SQL: ltree2text(e.path)
// Example: ltree2text('Top.Child1.Child2'::ltree) → 'Top.Child1.Child2'
```

### Usage Examples

#### Finding Ancestors and Descendants

```php
// Find all descendants of a specific path
$dql = "SELECT e FROM Entity e WHERE e.path <@ TEXT2LTREE('Top.Child1')";

// Find all ancestors of a specific path
$dql = "SELECT e FROM Entity e WHERE TEXT2LTREE('Top.Child1') <@ e.path";

// Find the longest common ancestor of multiple entities
$dql = "SELECT LCA(e1.path, e2.path) FROM Entity e1, Entity e2 WHERE e1.id = 1 AND e2.id = 2";
```

#### Path Analysis

```php
// Get the depth of a path
$dql = "SELECT NLEVEL(e.path) FROM Entity e";

// Extract the parent path (everything except the last label)
$dql = "SELECT SUBPATH(e.path, 0, NLEVEL(e.path) - 1) FROM Entity e";

// Extract the root label
$dql = "SELECT SUBPATH(e.path, 0, 1) FROM Entity e";
```

#### Path Manipulation

```php
// Find entities at a specific level
$dql = "SELECT e FROM Entity e WHERE NLEVEL(e.path) = 2";

// Find entities with a specific parent
$dql = "SELECT e FROM Entity e WHERE SUBPATH(e.path, 0, NLEVEL(e.path) - 1) = 'Top.Child1'";

// Find entities that contain a specific label
$dql = "SELECT e FROM Entity e WHERE INDEX(e.path, 'Child1') >= 0";
```

### Performance Considerations

- Use GiST or GIN indexes on ltree columns for optimal performance
- The `@>` and `<@` operators work automatically with ltree types
- Consider using `SUBPATH` with negative offsets for efficient parent path extraction
- `LCA` function is efficient for finding common ancestors in hierarchical data
