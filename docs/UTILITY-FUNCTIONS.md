# <picture><source media="(prefers-color-scheme: dark)" srcset="assets/logo-dark.svg"><img src="assets/logo.svg" alt="" width="32" height="32" align="absmiddle"></picture> Utility Functions

| PostgreSQL function | Register for DQL as | Implemented by |
|---|---|---|
| cast | CAST | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cast` |
| to_char | TO_CHAR | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToChar` |
| to_number | TO_NUMBER | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToNumber` |
| gen_random_uuid | GEN_RANDOM_UUID | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\GenRandomUuid` |
| uuidv4 | UUIDV4 | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Uuidv4` |
| uuidv7 | UUIDV7 | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Uuidv7` |
| uuid_extract_timestamp | UUID_EXTRACT_TIMESTAMP | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\UuidExtractTimestamp` |
| uuid_extract_version | UUID_EXTRACT_VERSION | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\UuidExtractVersion` |


**Note**: `TO_NUMBER` supports Roman numeral conversion via the `RN` pattern (PostgreSQL 18+).
