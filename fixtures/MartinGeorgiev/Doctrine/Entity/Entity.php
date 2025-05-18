<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
abstract class Entity
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    public int $id;
}
