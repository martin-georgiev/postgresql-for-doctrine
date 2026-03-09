<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;

#[ORM\Entity()]
class ContainsGeometries extends Entity
{
    #[ORM\Column(type: Type::GEOMETRY)]
    public WktSpatialData $geometry1;

    #[ORM\Column(type: Type::GEOMETRY)]
    public WktSpatialData $geometry2;

    #[ORM\Column(type: Type::GEOGRAPHY)]
    public WktSpatialData $geography1;

    #[ORM\Column(type: Type::GEOGRAPHY)]
    public WktSpatialData $geography2;
}
