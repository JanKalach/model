<?php declare(strict_types=1);

namespace Leo\Model\Database;

use Leo\Model\FetchFul\FetchFulOne;
use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;

final class FetchId implements FetchFulOne
{
    private Explorer $explorer;
    private int $id;
    private string $table;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function injectExplorer(Explorer $explorer): void
    {
        $this->explorer = $explorer;
    }

    public function getTable(): ?string
    {
        return $this->tableName ?? null;
    }
    public function setTable(string $table): static
    {
        $this->table = $table;
        return $this;
    }

    public function fetchOne(): ?ActiveRow
    {
        return $this->explorer
            ->table($this->table)
            ->select('*')
            ->where('id', $this->id)
            ->fetch()
        ;
    }
}
