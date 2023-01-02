<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\Fixtures\Entity;

/**
 * @Entity
 */
class ContainsDates extends Entity
{
    /**
     * @Column(type="date_immutable")
     */
    public \DateTimeImmutable $date1;

    /**
     * @Column(type="date_immutable")
     */
    public \DateTimeImmutable $date2;
}
