<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;

#[ORM\Entity()]
class ContainsGeometries extends Entity
{
    #[ORM\Column(type: 'geometry')]
    public ?WktSpatialData $geometry1 = null;

    #[ORM\Column(type: 'geometry')]
    public ?WktSpatialData $geometry2 = null;

    #[ORM\Column(type: 'geography')]
    public ?WktSpatialData $geography1 = null;

    #[ORM\Column(type: 'geography')]
    public ?WktSpatialData $geography2 = null;
}
