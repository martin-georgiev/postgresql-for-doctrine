<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Ltree;

#[ORM\Entity()]
class Product
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    public int $id;

    #[ORM\Column(type: Types::STRING)]
    public string $name;

    #[ORM\Column(type: Types::DECIMAL)]
    public string $price;

    #[ORM\Column(type: Type::TEXT_ARRAY)]
    public array $tags;

    #[ORM\Column(type: Type::JSONB)]
    public array $attributes;

    #[ORM\Column(type: Type::LTREE)]
    public Ltree $category;

    #[ORM\Column(type: Types::STRING)]
    public string $status;
}
