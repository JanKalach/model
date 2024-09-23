<?php declare(strict_types=1);

namespace Leo\Model;

use Nette\Utils\Paginator;

class Collection extends Iterator
{
    use ModelTrait;
    use CollectionTrait;

    public Paginator $paginator;

    public function __construct()
    {
        $this->paginator = new Paginator();
    }

    public function getById(int $id): ?Model
    {
        /** @var Model $item */
        foreach ($this->items as $item) {
            if ($item->id === $id) {
                return $item;
            }
        }
        return null;
    }
}
