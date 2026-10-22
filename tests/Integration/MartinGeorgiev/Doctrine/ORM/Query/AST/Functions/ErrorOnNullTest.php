<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ErrorOnNull;
use PHPUnit\Framework\Attributes\Test;

final class ErrorOnNullTest extends NumericTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgresVersion(190000, 'error_on_null function');
    }

    protected function getStringFunctions(): array
    {
        return [
            'ERROR_ON_NULL' => ErrorOnNull::class,
        ];
    }

    #[Test]
    public function returns_the_argument_from_a_numeric_literal(): void
    {
        $dql = 'SELECT ERROR_ON_NULL(42) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(42, $result[0]['result']);
    }

    #[Test]
    public function returns_the_argument_from_an_entity_field(): void
    {
        $dql = 'SELECT ERROR_ON_NULL(n.integer1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(10, $result[0]['result']);
    }
}
