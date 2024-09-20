<?php declare(strict_types=1);

namespace Leo\Model\Database;

use Leo\Model\FetchFul\FetchFulCollection;
use Leo\Model\FetchFul\FetchFulOne;
use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;

final class FetchWhereAll implements FetchFulCollection
{
    private Explorer $explorer;
    private string $table;

    private int $count = 0;

    public function __construct(
        private array $where = ['1' => '1'],
        private ?string $order = null,
        private ?int $page = null,
        private ?int $itemsPerPage = null,
        private string $columns = '*'
    )
    {

    }

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

    public function getCount(): int
    {
        return $this->count;
    }

    public function fetchAll(): array
    {
        $fetch = $this->explorer
            ->table($this->table)
            ->select($this->columns)
            ->where($this->where)
            ->order($this->order)
        ;
        $this->count = $fetch->count();
        if ($this->page !== null && $this->itemsPerPage !== null && $this->page > 0) {
            $fetch->page($this->page, $this->itemsPerPage);
        }
        return $fetch->fetchAll();
    }
}
