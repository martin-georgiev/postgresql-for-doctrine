<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TrimScale;
use PHPUnit\Framework\Attributes\Test;

class TrimScaleTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TRIM_SCALE' => TrimScale::class,
        ];
    }

    #[Test]
    public function can_calculate_trim_scale_of_literal(): void
    {
        $dql = 'SELECT TRIM_SCALE(8.4100) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('8.41', $result[0]['result']);
    }

    #[Test]
    public function can_calculate_trim_scale_with_entity_property(): void
    {
        $dql = 'SELECT TRIM_SCALE(n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('10.5', $result[0]['result']);
    }
}
