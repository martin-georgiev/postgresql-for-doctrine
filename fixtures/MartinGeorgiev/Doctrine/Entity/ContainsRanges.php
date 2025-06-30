<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\BaseIntegerRange;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int4Range;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int8Range;

/**
 * @ORM\Entity
 *
 * @ORM\Table(name="contains_ranges", schema="test")
 */
class ContainsRanges
{
    /**
     * @ORM\Id
     *
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @ORM\Column(type="integer")
     */
    public ?int $id = null;

    /**
     * @ORM\Column(type="int4range", nullable=true)
     */
    public ?BaseIntegerRange $int4Range1 = null;

    /**
     * @ORM\Column(type="int4range", nullable=true)
     */
    public ?BaseIntegerRange $int4Range2 = null;

    /**
     * @ORM\Column(type="int8range", nullable=true)
     */
    public ?BaseIntegerRange $int8Range1 = null;

    /**
     * @ORM\Column(type="int8range", nullable=true)
     */
    public ?BaseIntegerRange $int8Range2 = null;

    public function __construct()
    {
        $this->int4Range1 = new Int4Range(1, 1000);
        $this->int4Range2 = new Int4Range(0, 2147483647);

        $this->int8Range1 = new Int8Range(1, PHP_INT_MAX);
        $this->int8Range2 = new Int8Range(PHP_INT_MIN, 0);
    }
}
