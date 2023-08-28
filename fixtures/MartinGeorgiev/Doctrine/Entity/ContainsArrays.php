<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class ContainsArrays extends Entity
{
    /**
     * @ORM\Column(type="array")
     */
    public array $array1;

    /**
     * @ORM\Column(type="array")
     */
    public array $array2;
}
