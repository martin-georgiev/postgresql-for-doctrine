<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

trait MalformedArrayLiteralProviderTrait
{
    /**
     * The braces are wrong before the elements matter, so what sits between them is of no consequence.
     *
     * @return array<string, array{string}>
     */
    public static function provideMalformedArrayLiterals(): array
    {
        return [
            'missing closing brace' => ['{1,2'],
            'missing opening brace' => ['1,2}'],
            'no braces at all' => ['1,2'],
            'no literal at all' => [''],
        ];
    }

    /**
     * Here the elements are the caller's own valid ones on purpose. Built from a value the type rejects anyway,
     * both rows would still throw with the array parsing gone - from the item hook, for the wrong reason.
     *
     * @return array<string, array{string}>
     */
    protected static function malformedLiteralsAround(string $first, string $second): array
    {
        return [
            'empty element between valid ones' => [\sprintf('{%s,,%s}', $first, $second)],
            'valid elements nested one level deep' => [\sprintf('{{%s},{%s}}', $first, $second)],
        ];
    }
}
