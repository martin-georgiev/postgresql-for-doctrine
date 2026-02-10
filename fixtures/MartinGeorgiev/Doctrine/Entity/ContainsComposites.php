<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class ContainsComposites extends Entity
{
    /**
     * Represents a PostgreSQL composite type column.
     * In real usage, this would be mapped to a custom composite type.
     */
    #[ORM\Column(type: Types::STRING)]
    public string $item;

    #[ORM\Column(type: Types::STRING)]
    public string $address;

    #[ORM\Column(type: Types::STRING)]
    public string $special;
}
