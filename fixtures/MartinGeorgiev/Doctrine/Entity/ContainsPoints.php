<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Point;

#[ORM\Entity()]
class ContainsPoints extends Entity
{
    #[ORM\Column(type: 'point')]
    public Point $point1;

    #[ORM\Column(type: 'point')]
    public Point $point2;
}
