<?php declare(strict_types=1);

namespace Leo\Model;

use Leo\Bridges;

class Model
{
    use Bridges\ModelFactoryTrait;
    use Bridges\ModelCacheTrait;

    public static string $dbTable;

    function __init(): void
    {

    }
}
