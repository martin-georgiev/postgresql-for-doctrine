<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class ContainsJsons extends Entity
{
    #[ORM\Column(type: 'json')]
    public array $object1;

    #[ORM\Column(type: 'json')]
    public array $object2;
}
