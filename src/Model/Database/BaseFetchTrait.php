<?php declare(strict_types=1);

namespace Leo\Model\Database;

use Nette\Database\Explorer;

trait BaseFetchTrait
{
    protected Explorer $explorer;
    protected string $table;

    public function injectExplorer(Explorer $explorer): void
    {
        $this->explorer = $explorer;
    }

    public function getTable(): ?string
    {
        return $this->table ?? null;
    }

    public function setTable(string $table): static
    {
        $this->table = $table;
        return $this;
    }

    public function __get(string $name): mixed
    {
        return $this->$name;
    }
}
