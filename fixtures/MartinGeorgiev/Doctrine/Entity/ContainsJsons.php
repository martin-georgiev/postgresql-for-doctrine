<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use MartinGeorgiev\Doctrine\DBAL\Type;

#[ORM\Entity()]
class ContainsJsons extends Entity
{
    #[ORM\Column(type: Type::JSONB)]
    public array $jsonbObject1;

    #[ORM\Column(type: Type::JSONB)]
    public array $jsonbObject2;

    #[ORM\Column(type: Types::JSON)]
    public array $jsonObject1;

    #[ORM\Column(type: Types::JSON)]
    public array $jsonObject2;
}
