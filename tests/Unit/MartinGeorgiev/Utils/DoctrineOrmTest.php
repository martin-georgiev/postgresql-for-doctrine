<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Utils;

use Doctrine\ORM\Query\TokenType;
use MartinGeorgiev\Utils\DoctrineOrm;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DoctrineOrmTest extends TestCase
{
    #[Test]
    public function is_consistent_with_token_type_existence(): void
    {
        $tokenTypeExists = \class_exists(TokenType::class);
        $isPre219 = DoctrineOrm::isPre219();

        self::assertSame(!$tokenTypeExists, $isPre219);
    }
}
