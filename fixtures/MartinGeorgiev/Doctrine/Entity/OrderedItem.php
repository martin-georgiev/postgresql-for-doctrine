<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Declares its own id instead of extending Entity: ORM 2 lists inherited columns after its own, ORM 3 before them.
 */
#[ORM\Entity()]
class OrderedItem
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    public int $id;

    #[ORM\Column(type: Types::TEXT)]
    public string $name;

    #[ORM\Column(type: Types::INTEGER)]
    public int $position;

    #[ORM\ManyToOne(targetEntity: ContainsOrderedItems::class, inversedBy: 'items')]
    public ContainsOrderedItems $owner;
}
