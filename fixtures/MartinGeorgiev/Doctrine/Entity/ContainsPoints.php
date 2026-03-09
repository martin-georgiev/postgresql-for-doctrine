<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Point;

#[ORM\Entity()]
class ContainsPoints extends Entity
{
    #[ORM\Column(type: Type::POINT)]
    public Point $point1;

    #[ORM\Column(type: Type::POINT)]
    public Point $point2;
}
