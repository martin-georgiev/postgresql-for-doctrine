<?php

namespace MartinGeorgiev\Doctrine\DBAL\Types;

/**
 * Implementation of Postgres' text[] data type
 */
class TextArray extends JsonbArray
{
    use JsonTransformer;
    
    /**
     * @var string
     */
    const TYPE_NAME = 'text[]';
}
