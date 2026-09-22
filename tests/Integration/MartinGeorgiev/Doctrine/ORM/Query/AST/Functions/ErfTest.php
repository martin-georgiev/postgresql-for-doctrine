<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Erf;
use PHPUnit\Framework\Attributes\Test;

final class ErfTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ERF' => Erf::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgresVersion(170000, 'ERF');
    }

    #[Test]
    public function returns_the_error_function_from_a_numeric_literal(): void
    {
        $dql = 'SELECT ERF(1.0) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.8427007929497149, $result[0]['result']);
    }

    #[Test]
    public function returns_the_error_function_from_an_entity_field(): void
    {
        $dql = 'SELECT ERF(n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1.0, $result[0]['result']);
    }
}
