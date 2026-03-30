<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidFloatArrayItemForPHPException;
use PHPUnit\Framework\Attributes\Test;

abstract class BaseFloatArrayTestCase extends BaseNumericArrayTestCase
{
    protected static function getInvalidItemException(): string
    {
        return InvalidFloatArrayItemForPHPException::class;
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return \array_merge(static::commonInvalidDatabaseValueInputs(), [
            'invalid scientific notation (trailing e)' => ['1e'],
            'invalid scientific notation (leading e)' => ['e1'],
            'invalid number format' => ['1.23.45'],
        ]);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidTypeInputsForPHP(): array
    {
        return \array_merge(static::commonInvalidTypeInputsForPHP(), [
            'invalid scientific notation (trailing e)' => ['1e'],
            'invalid scientific notation (leading e)' => ['e1'],
            'invalid number format' => ['1.23.45'],
        ]);
    }

    #[Test]
    public function throws_domain_exception_when_invalid_array_item_value(): void
    {
        $this->expectException(InvalidFloatArrayItemForPHPException::class);
        $this->expectExceptionMessage('cannot be transformed to valid PHP float');

        $this->fixture->transformArrayItemForPHP('1.e234');
    }
}
