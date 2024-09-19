<?php declare(strict_types=1);

namespace Leo\Model\Database;

use Leo\Model\FetchFul\FetchFulCollection;
use Leo\Model\FetchFul\FetchFulOne;
use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;

final class FetchAll implements FetchFulCollection
{
    private Explorer $explorer;
    private string $tableName;

    public function injectExplorer(Explorer $explorer): void
    {
        $this->explorer = $explorer;
    }

    public function setTable(string $table): static
    {
        $this->tableName = $table;
        return $this;
    }

    public function fetchAll(): array
    {
        return $this->explorer
            ->table($this->tableName)
            ->select('*')
            ->fetchAll()
        ;
    }
}
