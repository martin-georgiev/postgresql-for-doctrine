<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Chr;
use PHPUnit\Framework\Attributes\Test;

class ChrTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'CHR' => Chr::class,
        ];
    }

    #[Test]
    public function returns_character_for_code_point_65(): void
    {
        $dql = 'SELECT CHR(65) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('A', $result[0]['result']);
    }

    #[Test]
    public function returns_character_for_code_point_116(): void
    {
        $dql = 'SELECT CHR(116) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('t', $result[0]['result']);
    }
}
