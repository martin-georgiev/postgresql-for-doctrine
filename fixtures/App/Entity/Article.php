<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use MartinGeorgiev\Doctrine\DBAL\Type;

#[ORM\Entity()]
class Article
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    public int $id;

    #[ORM\Column(type: Types::STRING)]
    public string $title;

    #[ORM\Column(type: Types::TEXT)]
    public string $body;

    #[ORM\Column(type: Type::TSVECTOR)]
    public string $search;

    #[ORM\Column(type: Type::VECTOR)]
    public array $embedding;
}
