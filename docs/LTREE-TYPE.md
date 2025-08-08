# ltree type usage

## Requirements

The `ltree` data type requires enabling the [`ltree` extension](https://www.postgresql.org/docs/current/ltree.html)
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
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\LtreeInterface;
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

    #[ORM\Column(type: 'ltree', unique: true)]
    private LtreeInterface $path;

    /**
     * @var Collection<array-key,MyEntity> $children
     */
    #[ORM\OneToMany(targetEntity: MyEntity::class, mappedBy: 'parent')]
    private Collection $children;

    /**
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
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

    public function getPath(): LtreeInterface
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

🗃️ Doctrine can't create PostgreSQL [GiST indexes](https://www.postgresql.org/docs/current/gist.html)
or [GIN indexes](https://www.postgresql.org/docs/current/gin.html).
Add a GiST index to an `ltree` column by manually adding its `CREATE INDEX`
command to the migration:

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
Implement a [onFlush](https://www.doctrine-project.org/projects/doctrine-orm/en/3.3/reference/events.html#reference-events-on-flush)
[Doctrine Entity Listener](https://symfony.com/doc/current/doctrine/events.html#doctrine-lifecycle-listeners)
to handle updating the `path` column of the updated entity children
when the `path` is present in the changed fields:

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
