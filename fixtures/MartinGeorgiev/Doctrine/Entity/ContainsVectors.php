<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;
use MartinGeorgiev\Doctrine\DBAL\Type;

#[ORM\Entity()]
class ContainsVectors extends Entity
{
    #[ORM\Column(type: Type::VECTOR)]
    public array $vector1;

    #[ORM\Column(type: Type::VECTOR)]
    public array $vector2;
}
