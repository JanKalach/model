<?php declare(strict_types=1);

namespace Leo\Model;

class Iterator implements \Iterator
{
    protected array $items = [];
    protected int $index = 0;
    protected int $itemCount = 0;

    public function count(): int
    {
        return $this->itemCount;
    }

    public function current(): mixed
    {
        return $this->items[$this->index];
    }

    public function next(): void
    {
        $this->index++;
    }

    public function valid(): bool
    {
        return isset($this->items[$this->key()]);
    }

    public function key(): int
    {
        return $this->index;
    }

    public function reverse(): void
    {
        $this->items = array_reverse($this->items);
        $this->rewind();
    }

    public function rewind(): void
    {
        $this->index = 0;
    }

    public function add($item): void
    {
        $this->items[] = $item;
        $this->itemCount = count($this->items);
    }

    public function first()
    {
        return $this->items[0] ?? null;
    }

    public function last()
    {
        return $this->items[$this->itemCount - 1] ?? null;
    }
}
