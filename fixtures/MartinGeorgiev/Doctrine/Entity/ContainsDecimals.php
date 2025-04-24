<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class ContainsDecimals extends Entity
{
    #[ORM\Column(type: Types::DECIMAL)]
    public float $decimal1;

    #[ORM\Column(type: Types::DECIMAL)]
    public float $decimal2;

    #[ORM\Column(type: Types::DECIMAL)]
    public float $decimal3;
}
