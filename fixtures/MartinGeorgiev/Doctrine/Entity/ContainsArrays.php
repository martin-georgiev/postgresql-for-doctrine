<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;
use MartinGeorgiev\Doctrine\DBAL\Type;

#[ORM\Entity()]
class ContainsArrays extends Entity
{
    #[ORM\Column(type: Type::TEXT_ARRAY)]
    public array $textArray;

    #[ORM\Column(type: Type::SMALLINT_ARRAY)]
    public array $smallintArray;

    #[ORM\Column(type: Type::INTEGER_ARRAY)]
    public array $integerArray;

    #[ORM\Column(type: Type::BIGINT_ARRAY)]
    public array $bigintArray;

    #[ORM\Column(type: Type::BOOL_ARRAY)]
    public array $boolArray;
}
