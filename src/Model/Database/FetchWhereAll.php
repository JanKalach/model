<?php declare(strict_types=1);

namespace Leo\Model\Database;

use Leo\Model\FetchFul\FetchFulCollection;

final class FetchWhereAll implements FetchFulCollection
{
    use BaseFetchTrait;
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
