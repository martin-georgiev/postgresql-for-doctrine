<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class ContainsDecimals extends Entity
{
    #[ORM\Column(type: 'decimal')]
    public float $decimal1;

    #[ORM\Column(type: 'decimal')]
    public float $decimal2;

    #[ORM\Column(type: 'decimal')]
    public float $decimal3;
}