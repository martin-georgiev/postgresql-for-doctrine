<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToOct;
use PHPUnit\Framework\Attributes\Test;

final class ToOctTest extends NumericTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgresVersion(170000, 'to_oct function');
    }

    protected function getStringFunctions(): array
    {
        return [
            'TO_OCT' => ToOct::class,
        ];
    }

    #[Test]
    public function returns_the_octal_representation_from_a_numeric_literal(): void
    {
        $dql = 'SELECT TO_OCT(255) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('377', $result[0]['result']);
    }

    #[Test]
    public function returns_the_octal_representation_from_an_entity_field(): void
    {
        $dql = 'SELECT TO_OCT(n.integer1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('12', $result[0]['result']);
    }
}
