<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class ContainsNumerics extends Entity
{
    #[ORM\Column(type: Types::INTEGER)]
    public int $integer1;

    #[ORM\Column(type: Types::INTEGER)]
    public int $integer2;

    #[ORM\Column(type: Types::BIGINT)]
    public int $bigint1;

    #[ORM\Column(type: Types::BIGINT)]
    public int $bigint2;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    public float $decimal1;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    public float $decimal2;
}
