<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\Fixtures\Entity;

/**
 * @Entity
 */
class ContainsJsons extends Entity
{
    /**
     * @var array
     *
     * @Column(type="json")
     */
    public $object1;

    /**
     * @var array
     *
     * @Column(type="json")
     */
    public $object2;
}
