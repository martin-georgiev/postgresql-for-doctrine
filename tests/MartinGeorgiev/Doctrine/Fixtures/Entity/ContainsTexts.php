<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\Fixtures\Entity;

/**
 * @Entity
 */
class ContainsTexts extends Entity
{
    /**
     * @Column(type="text")
     */
    public string $text1;

    /**
     * @Column(type="text")
     */
    public string $text2;
}
