<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidTimezoneException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\MakeTimestamptz;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class MakeTimestamptzTest extends BaseVariadicFunctionTestCase
{
    protected function createFixture(): BaseVariadicFunction
    {
        return new MakeTimestamptz('MAKE_TIMESTAMPTZ');
    }

    protected function getStringFunctions(): array
    {
        return [
            'MAKE_TIMESTAMPTZ' => MakeTimestamptz::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'creates timestamptz without timezone' => 'SELECT make_timestamptz(2023, 6, 15, 10, 30, 0) AS sclr_0 FROM ContainsDates c0_',
            'creates timestamptz with timezone' => "SELECT make_timestamptz(2023, 6, 15, 10, 30, 0, 'UTC') AS sclr_0 FROM ContainsDates c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'creates timestamptz without timezone' => \sprintf('SELECT MAKE_TIMESTAMPTZ(2023, 6, 15, 10, 30, 0) FROM %s e', ContainsDates::class),
            'creates timestamptz with timezone' => \sprintf("SELECT MAKE_TIMESTAMPTZ(2023, 6, 15, 10, 30, 0, 'UTC') FROM %s e", ContainsDates::class),
        ];
    }

    #[DataProvider('provideInvalidTimezoneValues')]
    #[Test]
    public function throws_exception_for_invalid_timezone(string $invalidTimezone): void
    {
        $this->expectException(InvalidTimezoneException::class);
        $this->expectExceptionMessage(\sprintf('Invalid timezone "%s" provided for make_timestamptz. Must be a valid PHP timezone identifier.', $invalidTimezone));

        $dql = \sprintf("SELECT MAKE_TIMESTAMPTZ(2023, 6, 15, 10, 30, 0, '%s') FROM %s e", $invalidTimezone, ContainsDates::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidTimezoneValues(): array
    {
        return [
            'empty string' => [''],
            'whitespace only' => ['   '],
            'numeric value' => ['123'],
            'invalid timezone' => ['Invalid/Timezone'],
        ];
    }

    #[Test]
    public function throws_exception_for_non_constant_timezone_parameter(): void
    {
        $this->expectException(InvalidTimezoneException::class);
        $this->expectExceptionMessage('The timezone parameter for make_timestamptz must be a string literal');

        $dql = \sprintf('SELECT MAKE_TIMESTAMPTZ(2023, 6, 15, 10, 30, 0, e.datetimetz1) FROM %s e', ContainsDates::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
