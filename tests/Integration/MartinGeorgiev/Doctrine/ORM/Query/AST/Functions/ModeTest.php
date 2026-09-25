<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Mode;
use PHPUnit\Framework\Attributes\Test;

final class ModeTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'MODE' => Mode::class,
        ];
    }

    #[Test]
    public function returns_the_most_frequent_value_from_an_entity_field(): void
    {
        $dql = 'SELECT MODE(WITHIN GROUP ORDER BY n.integer1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(10, $result[0]['result']);
    }
}
