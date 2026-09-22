<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BitOr;
use PHPUnit\Framework\Attributes\Test;

final class BitOrTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'BIT_OR' => BitOr::class,
        ];
    }

    #[Test]
    public function returns_the_bitwise_or_from_an_entity_field(): void
    {
        $dql = 'SELECT BIT_OR(t.integer1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(10, $result[0]['result']);
    }
}
