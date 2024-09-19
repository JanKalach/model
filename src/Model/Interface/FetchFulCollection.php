<?php declare(strict_types=1);

namespace Leo\Model\FetchFul;

use Nette\Database\Table\ActiveRow;

interface FetchFulCollection
{
    public function setTable(string $table): static;

    public function fetchAll(): array;
}
