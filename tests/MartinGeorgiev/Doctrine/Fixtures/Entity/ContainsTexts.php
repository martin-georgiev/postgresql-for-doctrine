<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\Fixtures\Entity;

/**
 * @Entity
 */
class ContainsTexts extends Entity
{
    /**
     * @var string
     *
     * @Column(type="text")
     */
    public $text1;

    /**
     * @var string
     *
     * @Column(type="text")
     */
    public $text2;
}
