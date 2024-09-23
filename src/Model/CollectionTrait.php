<?php declare(strict_types=1);

namespace Leo\Model;

trait CollectionTrait
{
    protected array $items = [];

    public function getItems(): array
    {
        return $this->items;
    }

    public function sort(string $orderField, bool $returnItems = false): self|array
    {
        $items = $this->items;
        usort($items, function ($a, $b) use ($orderField) {
            return $this->__sortCallback($a->$orderField, $b->$orderField);
        });
        if ($returnItems) {
            return $items;
        }
        $this->items = $items;
        return $this;
    }

    private function __sortCallback($a, $b): int
    {
        if (is_string($a) || is_string($b)) {
            return strcoll($a, $b);
        }
        return $a > $b ? 1 : -1;
    }

    public function rsort(string $orderField): self
    {
        $items = $this->items;
        usort($items, function ($a, $b) use ($orderField) {
            return $this->__sortCallback($b->$orderField, $a->$orderField);
        });
        $this->items = $items;
        return $this;
    }

    public function usort($callable): self
    {
        usort($this->items, $callable);
        return $this;
    }

    public function uksort($callable): self
    {
        uksort($this->items, $callable);
        return $this;
    }

    public function toArray(): array
    {
        $ret = [];
        /** @var \Leo\Model $item */
        foreach ($this->items as $item) {
            $ret[$item->id] = $item->toArray();
        }
        return $ret;
    }

    public function fetch(string $key): array
    {
        $ret = [];
        foreach ($this->items as $item) {
            if (!isset($item->$key)) {
                continue;
            }
            $ret[] = $item->$key;
        }
        return $ret;
    }

    public function fetchAssoc(string $assoc): array
    {
        $ret = [];
        foreach ($this->items as $item) {
            $ret[$item->$assoc] = $item;
        }
        return $ret;
    }

    public function fetchPairs($id, $key = null): array
    {
        $property = $key ?? $id;
        $ret = [];
        foreach ($this->items as $item) {
            $ret[$item->$id] = $item->$property;
        }
        return $ret;
    }

}
