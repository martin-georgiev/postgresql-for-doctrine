<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class ContainsRanges extends Entity
{
    #[ORM\Column(type: 'int4range')]
    public mixed $int4Range;

    #[ORM\Column(type: 'int8range')]
    public mixed $int8Range;

    #[ORM\Column(type: 'numrange')]
    public mixed $numRange;

    #[ORM\Column(type: 'daterange')]
    public mixed $dateRange;

    #[ORM\Column(type: 'tsrange')]
    public mixed $tsRange;

    #[ORM\Column(type: 'tstzrange')]
    public mixed $tstzRange;
}
