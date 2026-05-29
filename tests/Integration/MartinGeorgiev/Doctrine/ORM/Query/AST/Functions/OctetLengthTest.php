<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\OctetLength;
use PHPUnit\Framework\Attributes\Test;

class OctetLengthTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'OCTET_LENGTH' => OctetLength::class,
        ];
    }

    #[Test]
    public function returns_byte_length_of_literal(): void
    {
        $dql = "SELECT OCTET_LENGTH('hello') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame(5, $result[0]['result']);
    }

    #[Test]
    public function returns_byte_length_of_text_field(): void
    {
        $dql = 'SELECT OCTET_LENGTH(t.text1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts t
                WHERE t.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame(3, $result[0]['result']);
    }
}
