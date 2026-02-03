<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Left;
use PHPUnit\Framework\Attributes\Test;

class LeftTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'LEFT' => Left::class,
        ];
    }

    #[Test]
    public function can_extract_left_characters(): void
    {
        $dql = 'SELECT LEFT(t.text1, 4) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('this', $result[0]['result']);
    }
}
