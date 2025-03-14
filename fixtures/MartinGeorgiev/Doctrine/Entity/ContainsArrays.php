<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class ContainsArrays extends Entity
{
    #[ORM\Column(type: Types::JSON)]
    public array $array1;

    #[ORM\Column(type: Types::JSON)]
    public array $array2;
}
