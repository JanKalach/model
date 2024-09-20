<?php declare(strict_types=1);

namespace Leo\Model\FetchFul;

use Nette\Database\Table\ActiveRow;

interface FetchFulCollection
{
    public function getTable(): ?string;

    public function setTable(string $table): static;

    public function getCount(): int;

    public function fetchAll(): array;
}
