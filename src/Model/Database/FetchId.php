<?php declare(strict_types=1);

namespace Leo\Model\Database;

use Leo\Model\FetchFul\FetchFulOne;
use Nette\Database\Table\ActiveRow;

final class FetchId extends BaseFetchClass implements FetchFulOne
{
    protected int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
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
