<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;
use MartinGeorgiev\Doctrine\DBAL\Type;

#[ORM\Entity()]
class ContainsSpatialArrays extends Entity
{
    #[ORM\Column(type: Type::GEOMETRY_ARRAY)]
    public array $geometries;

    #[ORM\Column(type: Type::GEOGRAPHY_ARRAY)]
    public array $geographies;
}
