<?php declare(strict_types=1);

namespace Leo\Model\Database;

use Leo\Model\FetchFul\FetchFulOne;
use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;

final class FetchId implements FetchFulOne
{
    private Explorer $explorer;
    private int $id;
    private string $tableName;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function injectExplorer(Explorer $explorer): void
    {
        $this->explorer = $explorer;
    }

    public function setTable(string $table): static
    {
        $this->tableName = $table;
        return $this;
    }

    public function fetchOne(): ?ActiveRow
    {
        return $this->explorer
            ->table($this->tableName)
            ->select('*')
            ->where('id', $this->id)
            ->fetch()
        ;
    }
}
