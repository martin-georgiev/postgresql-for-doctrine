<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class OrderedItem extends Entity
{
    #[ORM\ManyToOne(targetEntity: ContainsOrderedItems::class, inversedBy: 'items')]
    public ContainsOrderedItems $owner;

    #[ORM\Column(type: Types::TEXT)]
    public string $name;

    #[ORM\Column(type: Types::INTEGER)]
    public int $position;
}
