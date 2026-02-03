<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Right;
use PHPUnit\Framework\Attributes\Test;

class RightTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'RIGHT' => Right::class,
        ];
    }

    #[Test]
    public function can_extract_right_characters(): void
    {
        $dql = 'SELECT RIGHT(t.text1, 6) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('string', $result[0]['result']);
    }
}
