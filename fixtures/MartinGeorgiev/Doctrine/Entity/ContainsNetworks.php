<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;
use MartinGeorgiev\Doctrine\DBAL\Type;

#[ORM\Entity()]
class ContainsNetworks extends Entity
{
    #[ORM\Column(type: Type::INET, nullable: true)]
    public ?string $ip;

    #[ORM\Column(type: Type::CIDR, nullable: true)]
    public ?string $cidr;
}
