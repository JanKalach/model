<?php declare(strict_types=1);

namespace Leo\Model;

class Model
{
    use ModelTrait;

    public static string $dbTable;

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
