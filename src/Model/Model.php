<?php declare(strict_types=1);

namespace Leo\Model;

class Model
{
    use \Leo\Model\ModelFactoryTrait;

    public static string $dbTable;

    function __init(): void
    {

    }

    public function setValues(iterable $values): self
    {
        foreach ($values as $key => $value) {
            $this->$key = $value;
        }
        return $this;
    }

    public function save()
    {
        return $this->getModelFactory()
            ->save($this)
        ;
    }
}
