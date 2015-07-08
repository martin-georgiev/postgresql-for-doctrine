<?php

namespace MartinGeorgiev\Doctrine\DBAL\Types;

/**
 * Implementation of Postgres' smallint[] data type
 */
class JsonbArray extends AbstractTypeArray
{
    use JsonTransformer;
    
    /**
     * @var string
     */
    const TYPE_NAME = 'jsonb[]';
}
