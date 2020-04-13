<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\Doctrine\Fixtures\Entity;

/**
 * @Entity
 */
class ContainsArrays extends Entity
{
    /**
     * @Column(type="array")
     */
    public $array1;

    /**
     * @Column(type="array")
     */
    public $array2;
}
