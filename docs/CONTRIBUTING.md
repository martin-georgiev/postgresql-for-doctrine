# Contributing

**Before opening your first PR**

For the sake of clear Git history and speedy review of your PR, please check that the suggested changes are in line with the project's standards. Code style, static analysis and file validation scripts are already provided and can easily be run from project's root:

`composer check-code-style` which will check for consistent code style

`composer fix-code-style` which will automatically apply fixes to the code style

`composer run-static-analysis` which will run static analysis for the currently configured level

`composer run-tests` which will run the full test suite


----
**How to add more array-like data types?**

1. Extend `MartinGeorgiev\Doctrine\DBAL\Types\BaseArray`.

2. You must give the new data-type a unique within your application name. For this purpose, you can use the `TYPE_NAME` constant.
3. Depending on the new data-type nature you may have to overwrite some of the following methods:

    `transformPostgresArrayToPHPArray()`
    
    `transformArrayItemForPHP()`
    
    `isValidArrayItemForDatabase()`


----
**How to add more functions?**

Most new functions will likely have a signature very similar to those already implemented in the project. This means new functions probably require only extending the base class and decorating it with some behaviour. Here are the two main steps to follow:
1. Extend `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction`.
2. Use calls to `setFunctionPrototype()` and `addNodeMapping()` to implement `customizeFunction()` for your new function class.

Example:

```php
<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

class ArrayAppend extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('array_append(%s, %s)');
        $this->addNodeMapping('StringPrimary'); # corresponds to param №1 in the prototype set in setFunctionPrototype
        $this->addNodeMapping('Literal'); # corresponds to param №2 in the prototype set in setFunctionPrototype
        # Add as more node mappings if needed.
    }
}
```

*Beware that you cannot use **?** (e.g. the `??` operator) as part of any function prototype in Doctrine as this will fail the query parsing.*
