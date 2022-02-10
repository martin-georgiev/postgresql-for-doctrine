<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\Fixtures\Entity;

/**
 * @Entity
 */
class ContainsDates extends Entity
{
    /**
     * @var \DateTimeImmutable
     *
     * @Column(type="date_immutable")
     */
    public $date1;

    /**
     * @var \DateTimeImmutable
     *
     * @Column(type="date_immutable")
     */
    public $date2;
}
