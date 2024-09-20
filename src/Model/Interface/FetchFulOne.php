<?php declare(strict_types=1);

namespace Leo\Model\FetchFul;

use Nette\Database\Table\ActiveRow;

interface FetchFulOne
{
    public function getTable(): ?string;

    public function setTable(string $table): static;

    public function fetchOne(): ?ActiveRow;
}
