<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\SplitPart;
use PHPUnit\Framework\Attributes\Test;

class SplitPartTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SPLIT_PART' => SplitPart::class,
        ];
    }

    #[Test]
    public function can_split_by_delimiter_and_get_first_part(): void
    {
        $dql = "SELECT SPLIT_PART(t.text1, ',', 1) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE t.id = 4";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('special', $result[0]['result']);
    }

    #[Test]
    public function can_split_by_delimiter_and_get_second_part(): void
    {
        $dql = "SELECT SPLIT_PART(t.text1, ',', 2) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE t.id = 4";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('chars;test', $result[0]['result']);
    }

    #[Test]
    public function returns_empty_string_for_out_of_range_index(): void
    {
        $dql = "SELECT SPLIT_PART(t.text1, ',', 10) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE t.id = 4";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('', $result[0]['result']);
    }
}
