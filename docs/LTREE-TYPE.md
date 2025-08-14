# ltree type usage

## Requirements

The `ltree` data type requires enabling the [`ltree` module](https://www.postgresql.org/docs/current/ltree.html)
in PostgreSQL.

```sql
CREATE EXTENSION IF NOT EXISTS ltree;
```

For [Symfony](https://symfony.com/),
customize the migration introducing the `ltree` field by adding this line
at the beginning of the `up()` method:

```php
$this->addSql('CREATE EXTENSION IF NOT EXISTS ltree WITH SCHEMA public');
```

## Usage

An example implementation (for a Symfony project) is:

```php
<?php

declare(strict_types=1);

namespace App\Entity;

use App\EventListener\MyEntityListener;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Ltree;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\LtreeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity()]
#[ORM\Index(columns: ['path'])]
#[ORM\EntityListeners([MyEntityListener::class])]
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
        private ?MyEntity $parent,
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

        if (in_array($this->getId()->toBase58(), $parent->getPath()->getPathFromRoot(), true)) {
            throw new \InvalidArgumentException("Parent MyEntity can't be a child of the current MyEntity");
        }

        $this->parent = $parent;

        // Use withLeaf() to create a new Ltree instance
        // with the parent's path and the current entity's ID.
        $this->path = $parent->getPath()->withLeaf($this->id->toBase58());
    }
}
```

⚠️ **Important**: Changing an entity's parent requires to cascade the change
to all its children.
This is not handled automatically by Doctrine.
Implement a [preUpdate](https://www.doctrine-project.org/projects/doctrine-orm/en/3.3/reference/events.html#reference-events-pre-update)
[Doctrine Entity Listener](https://www.doctrine-project.org/projects/doctrine-orm/en/3.3/reference/events.html#entity-listeners-class)
([Doctrine Entity Listener @ Symfony](https://symfony.com/doc/current/doctrine/events.html#doctrine-entity-listeners))
to handle updating the `path` column of the updated entity children
when the `path` is present in the changed fields:

```php
<?php

namespace App\EventListener;

use App\Entity\MyEntity;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;

final readonly class MyEntityListener
{
    #[ORM\PreUpdate]
    public function preUpdate(MyEntity $entity, PreUpdateEventArgs $eventArgs): void
    {
        if ($eventArgs->hasChangedField('path')) {
            foreach($entity->getChildren() as $child) {
                $child->setParent($entity);
            }
        }
    }
}
```
