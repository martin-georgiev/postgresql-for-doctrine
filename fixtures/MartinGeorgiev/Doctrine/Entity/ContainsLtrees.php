<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class ContainsLtrees extends Entity
{
    #[ORM\Column(type: 'ltree')]
    public string $ltree1;

    #[ORM\Column(type: 'ltree')]
    public string $ltree2;

    #[ORM\Column(type: 'ltree')]
    public string $ltree3;
}
