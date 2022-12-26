<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\Fixtures\Entity;

/**
 * @Entity
 */
class ContainsJsons extends Entity
{
    /**
     * @Column(type="json")
     */
    public array $object1;

    /**
     * @Column(type="json")
     */
    public array $object2;
}
