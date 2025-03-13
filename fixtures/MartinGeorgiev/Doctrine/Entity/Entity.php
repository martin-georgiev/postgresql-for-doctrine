<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

abstract class Entity
{
    #[ORM\Id()]
    #[ORM\Column(type: Types::STRING)]
    #[ORM\GeneratedValue()]
    public string $id;
}
