<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class Sale
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    public int $id;

    #[ORM\Column(type: Types::STRING)]
    public string $reference;

    #[ORM\ManyToOne(targetEntity: Customer::class)]
    public Customer $customer;

    #[ORM\Column(type: Types::DECIMAL)]
    public string $amount;

    #[ORM\Column(type: Types::STRING)]
    public string $status;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    public \DateTimeImmutable $placedAt;
}
