<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;
use MartinGeorgiev\Doctrine\DBAL\Type;

#[ORM\Entity()]
class ContainsRanges extends Entity
{
    #[ORM\Column(type: Type::INT4RANGE)]
    public mixed $int4Range;

    #[ORM\Column(type: Type::INT8RANGE)]
    public mixed $int8Range;

    #[ORM\Column(type: Type::NUMRANGE)]
    public mixed $numRange;

    #[ORM\Column(type: Type::DATERANGE)]
    public mixed $dateRange;

    #[ORM\Column(type: Type::TSRANGE)]
    public mixed $tsRange;

    #[ORM\Column(type: Type::TSTZRANGE)]
    public mixed $tstzRange;
}
