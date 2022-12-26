<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\Fixtures\Entity;

/**
 * @Entity
 */
class ContainsArrays extends Entity
{
    /**
     * @Column(type="array")
     */
    public array $array1;

    /**
     * @Column(type="array")
     */
    public array $array2;
}
