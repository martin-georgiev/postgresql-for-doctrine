<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\Fixtures\Entity;

abstract class Entity
{
    /**
     * @var string
     *
     * @Id
     * @Column(type="string")
     * @GeneratedValue
     */
    public $id;
}
