<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\Fixtures\Entity;

/**
 * @Entity
 */
class ContainsArrays extends Entity
{
    /**
     * @var array
     *
     * @Column(type="array")
     */
    public $array1;

    /**
     * @var array
     *
     * @Column(type="array")
     */
    public $array2;
}
