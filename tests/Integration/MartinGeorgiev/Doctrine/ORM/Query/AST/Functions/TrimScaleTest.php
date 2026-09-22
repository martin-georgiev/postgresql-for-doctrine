<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TrimScale;
use PHPUnit\Framework\Attributes\Test;

final class TrimScaleTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TRIM_SCALE' => TrimScale::class,
        ];
    }

    #[Test]
    public function returns_the_trimmed_scale_from_a_literal(): void
    {
        $dql = 'SELECT TRIM_SCALE(8.4100) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('8.41', $result[0]['result']);
    }

    #[Test]
    public function returns_the_trimmed_scale_from_an_entity_field(): void
    {
        $dql = 'SELECT TRIM_SCALE(n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('10.5', $result[0]['result']);
    }
}
