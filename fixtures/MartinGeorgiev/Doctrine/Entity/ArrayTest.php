<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ArrayTest extends Entity
{
    #[ORM\Column(name: 'text_array', type: 'text[]')]
    public array $textArray;

    #[ORM\Column(name: 'int_array', type: 'integer[]')]
    public array $intArray;

    #[ORM\Column(name: 'bool_array', type: 'bool[]')]
    public array $boolArray;
}
