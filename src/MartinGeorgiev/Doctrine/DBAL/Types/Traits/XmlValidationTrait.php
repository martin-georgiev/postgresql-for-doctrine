<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Traits;

/**
 * Common validation logic for XML values.
 *
 * @since 4.4
 */
trait XmlValidationTrait
{
    protected function isValidXml(string $value): bool
    {
        if ($value === '') {
            return false;
        }

        $previousUseInternalErrors = \libxml_use_internal_errors(true);
        \libxml_clear_errors();

        try {
            $domDocument = new \DOMDocument();
            $loaded = $domDocument->loadXML($value, \LIBXML_NONET);

            return $loaded && \libxml_get_errors() === [];
        } finally {
            \libxml_clear_errors();
            \libxml_use_internal_errors($previousUseInternalErrors);
        }
    }
}
