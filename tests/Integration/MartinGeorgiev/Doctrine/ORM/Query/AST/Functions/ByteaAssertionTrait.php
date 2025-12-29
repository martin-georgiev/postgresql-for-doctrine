<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

trait ByteaAssertionTrait
{
    /**
     * PostgreSQL bytea values may be returned as stream resources depending on
     * the PHP/PDO version. This method handles both cases and compares the
     * binary content as hex strings.
     *
     * @param string $expectedHex the expected hex string (without \x prefix)
     * @param mixed $actual the actual result from the query (may be string or resource)
     * @param string $message optional assertion message
     */
    protected function assertByteaEquals(string $expectedHex, mixed $actual, string $message = ''): void
    {
        $byteaContent = $actual;

        if (\is_resource($byteaContent)) {
            \rewind($byteaContent);
            $byteaContent = \stream_get_contents($byteaContent);
        }

        $this->assertIsString($byteaContent, 'Bytea result should be a string after stream conversion');
        $this->assertSame($expectedHex, \bin2hex((string) $byteaContent), $message);
    }
}
