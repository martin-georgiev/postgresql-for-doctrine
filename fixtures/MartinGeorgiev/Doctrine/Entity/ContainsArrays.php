<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class ContainsArrays extends Entity
{
    #[ORM\Column(type: 'text[]')]
    public array $textArray;

    #[ORM\Column(type: 'smallint[]')]
    public array $smallintArray;

    #[ORM\Column(type: 'integer[]')]
    public array $integerArray;

    #[ORM\Column(type: 'bigint[]')]
    public array $bigintArray;

    #[ORM\Column(type: 'bool[]')]
    public array $boolArray;
}
