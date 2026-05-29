# Hstore Functions

> 📖 **See also**: [Available Types](AVAILABLE-TYPES.md) for the `hstore` and `hstore[]` DBAL types | [Integrating with Doctrine](INTEGRATING-WITH-DOCTRINE.md)

> ⚠️ **Requires extension**: `CREATE EXTENSION IF NOT EXISTS hstore;`

| PostgreSQL function | Register for DQL as | Implemented by |
|---|---|---|
| akeys | HSTORE_AKEYS | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Hstore\Akeys` |
| avals | HSTORE_AVALS | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Hstore\Avals` |
| defined | HSTORE_DEFINED | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Hstore\Defined` |
| delete | HSTORE_DELETE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Hstore\Delete` |
| hstore_to_json | HSTORE_TO_JSON | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Hstore\HstoreToJson` |
| hstore_to_json_loose | HSTORE_TO_JSON_LOOSE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Hstore\HstoreToJsonLoose` |
| skeys | HSTORE_SKEYS | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Hstore\Skeys` |
| svals | HSTORE_SVALS | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Hstore\Svals` |
