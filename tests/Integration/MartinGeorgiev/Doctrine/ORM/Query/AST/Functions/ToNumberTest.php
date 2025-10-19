<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\Query\QueryException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToNumber;
use PHPUnit\Framework\Attributes\Test;

class ToNumberTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'to_number' => ToNumber::class,
        ];
    }

    #[Test]
    public function tonumber(): void
    {
        $dql = "SELECT to_number('12,454.8-', '99G999D9S') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('-12454.8', $result[0]['result']);
    }

    #[Test]
    public function tonumber_throws_with_invalid_format(): void
    {
        $this->expectException(Exception::class);
        $dql = "SELECT to_number('12,454.8-', 'invalid_format') FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $this->executeDqlQuery($dql);
    }

    #[Test]
    public function tonumber_throws_with_unsupported_null_format(): void
    {
        $this->expectException(QueryException::class);
        $dql = "SELECT to_number('12,454.8-', null) FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $this->executeDqlQuery($dql);
    }

    #[Test]
    public function tonumber_throws_with_unsupported_input_type(): void
    {
        $this->expectException(QueryException::class);
        $dql = "SELECT to_number(123456, '999D99S') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $this->executeDqlQuery($dql);
    }
}
