<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class ContainsBooleans extends Entity
{
    #[ORM\Column(type: Types::BOOLEAN)]
    public bool $bool1;

    #[ORM\Column(type: Types::BOOLEAN)]
    public bool $bool2;
}
