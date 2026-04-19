<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidSparsevecForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidSparsevecForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Sparsevec;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Sparsevec as SparsevecValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class SparsevecTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private Sparsevec $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new Sparsevec();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('sparsevec', $this->fixture->getName());
    }

    #[DataProvider('provideFieldDeclarationsWithDimensions')]
    #[Test]
    public function returns_type_with_dimensions_when_positive_length_specified(int $length): void
    {
        $this->assertSame(\sprintf('SPARSEVEC(%d)', $length), $this->fixture->getSQLDeclaration(['length' => $length], $this->platform));
    }

    /**
     * @return array<string, array{int}>
     */
    public static function provideFieldDeclarationsWithDimensions(): array
    {
        return [
            '1 dimension' => [1],
            '3 dimensions' => [3],
            '1024 dimensions' => [1024],
        ];
    }

    /**
     * @param array<string, mixed> $fieldDeclaration
     */
    #[DataProvider('provideFieldDeclarationsReturningBareType')]
    #[Test]
    public function returns_bare_type_for_invalid_or_missing_length(array $fieldDeclaration): void
    {
        $this->assertSame('SPARSEVEC', $this->fixture->getSQLDeclaration($fieldDeclaration, $this->platform));
    }

    /**
     * @return array<string, array{array<string, mixed>}>
     */
    public static function provideFieldDeclarationsReturningBareType(): array
    {
        return [
            'no length key' => [[]],
            'null length' => [['length' => null]],
            'non-integer length' => [['length' => 'abc']],
            'zero length' => [['length' => 0]],
            'negative length' => [['length' => -1]],
        ];
    }

    #[Test]
    public function can_transform_null_to_database_value(): void
    {
        $this->assertNull($this->fixture->convertToDatabaseValue(null, $this->platform));
    }

    #[Test]
    public function can_transform_from_php_value(): void
    {
        $sparsevec = new SparsevecValueObject([1 => 1.5, 3 => 2.0], 5);
        $this->assertSame('{1:1.5,3:2}/5', $this->fixture->convertToDatabaseValue($sparsevec, $this->platform));
    }

    #[Test]
    public function can_transform_null_to_php_value(): void
    {
        $this->assertNull($this->fixture->convertToPHPValue(null, $this->platform));
    }

    #[Test]
    public function can_transform_to_php_value(): void
    {
        $result = $this->fixture->convertToPHPValue('{1:1.5,3:2.0}/5', $this->platform);
        $this->assertInstanceOf(SparsevecValueObject::class, $result);
        $this->assertSame(5, $result->getDimensions());
        $this->assertSame([1 => 1.5, 3 => 2.0], $result->getElements());
    }

    #[DataProvider('provideInvalidDatabaseInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value(mixed $phpValue): void
    {
        $this->expectException(InvalidSparsevecForDatabaseException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseInputs(): array
    {
        return [
            'string input' => ['not a sparsevec'],
            'integer input' => [42],
            'boolean input' => [true],
            'array input' => [[1, 2, 3]],
            'object input' => [new \stdClass()],
        ];
    }

    #[DataProvider('provideInvalidPHPInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value(mixed $postgresValue): void
    {
        $this->expectException(InvalidSparsevecForPHPException::class);
        $this->fixture->convertToPHPValue($postgresValue, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidPHPInputs(): array
    {
        return [
            'integer input' => [42],
            'array input' => [['not', 'a', 'string']],
            'boolean input' => [true],
            'object input' => [new \stdClass()],
        ];
    }

    #[Test]
    public function throws_exception_for_invalid_sparsevec_format(): void
    {
        $this->expectException(InvalidSparsevecForPHPException::class);
        $this->fixture->convertToPHPValue('invalid-format', $this->platform);
    }

    #[DataProvider('provideOutOfRangeIndexInputs')]
    #[Test]
    public function throws_exception_for_out_of_range_index(string $input): void
    {
        $this->expectException(InvalidSparsevecForPHPException::class);
        $this->fixture->convertToPHPValue($input, $this->platform);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideOutOfRangeIndexInputs(): array
    {
        return [
            'index below range' => ['{0:1.5}/3'],
            'index above range' => ['{4:1.5}/3'],
        ];
    }
}
