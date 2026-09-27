<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class ContainsOrderedItems extends Entity
{
    /**
     * @var Collection<int, OrderedItem>
     */
    #[ORM\OneToMany(targetEntity: OrderedItem::class, mappedBy: 'owner')]
    #[ORM\OrderBy(['position' => 'ASC'])]
    public Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }
}
