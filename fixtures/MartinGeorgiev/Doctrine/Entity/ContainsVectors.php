<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class ContainsVectors extends Entity
{
    #[ORM\Column(type: 'vector')]
    public array $vector1;

    #[ORM\Column(type: 'vector')]
    public array $vector2;
}
