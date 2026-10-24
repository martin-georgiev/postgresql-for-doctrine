<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Traits;

/**
 * Common validation logic for XML values.
 *
 * @internal
 *
 * @since 4.4
 */
trait XmlValidationTrait
{
    /**
     * @var string
     */
    private const XML_DECLARATION = '/\A<\?xml[\t\n\r ]+version[\t\n\r ]*=[\t\n\r ]*("[^"\x80-\xff]*"|\'[^\'\x80-\xff]*\')(?:[\t\n\r ]+encoding[\t\n\r ]*=[\t\n\r ]*("[^"\x80-\xff]*"|\'[^\'\x80-\xff]*\'))?(?:[\t\n\r ]+standalone[\t\n\r ]*=[\t\n\r ]*(?:"(?:yes|no)"|\'(?:yes|no)\'))?[\t\n\r ]*\?>/';

    /**
     * @var string
     */
    private const XML_VERSION_NUMBER = '/\A["\']1\.[0-9]+["\']\z/';

    /**
     * @var string
     */
    private const XML_ENCODING_NAME = '/\A["\'][A-Za-z][A-Za-z0-9._-]*["\']\z/';

    protected function isValidXml(string $value): bool
    {
        $hasDeclaration = \preg_match(self::XML_DECLARATION, $value, $declaration, \PREG_UNMATCHED_AS_NULL) === 1;
        $content = $hasDeclaration ? \substr($value, \strlen($declaration[0])) : $value;

        // PostgreSQL parses content that opens with a DOCTYPE as a whole document, declaration included.
        if (!$this->opensWithADoctype($content)) {
            return $this->isWellFormedXmlDocument('<root>'.$content.'</root>');
        }

        if ($hasDeclaration) {
            $isAWellFormedDocumentDeclaration = \preg_match(self::XML_VERSION_NUMBER, $declaration[1]) === 1
                && ($declaration[2] === null || \preg_match(self::XML_ENCODING_NAME, $declaration[2]) === 1);
            if (!$isAWellFormedDocumentDeclaration) {
                return false;
            }
        }

        return $this->isWellFormedXmlDocument($content);
    }

    private function opensWithADoctype(string $content): bool
    {
        $offset = 0;
        while (true) {
            $offset += \strspn($content, "\t\n\r ", $offset);
            $next = \substr($content, $offset, 9);
            if (\str_starts_with($next, '<!--')) {
                $end = \strpos($content, '-->', $offset + 4);
                $closingLength = 3;
            } elseif (\str_starts_with($next, '<?')) {
                $end = \strpos($content, '?>', $offset + 2);
                $closingLength = 2;
            } else {
                return $next === '<!DOCTYPE';
            }

            if ($end === false) {
                return false;
            }

            $offset = $end + $closingLength;
        }
    }

    private function isWellFormedXmlDocument(string $value): bool
    {
        $previousUseInternalErrors = \libxml_use_internal_errors(true);

        try {
            return (new \DOMDocument())->loadXML($value, \LIBXML_NONET);
        } finally {
            \libxml_clear_errors();
            \libxml_use_internal_errors($previousUseInternalErrors);
        }
    }
}
