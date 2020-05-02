<?php
declare(strict_types=1);

namespace Sagrada;

use Sagrada\Dice\SagradaDie;

class DieCollection
{
    /** @var \SplObjectStorage */
    protected $collection;

    public function __construct()
    {
        $this->collection = new \SplObjectStorage();
    }

    public function add(SagradaDie $die): void
    {
        $this->collection->attach($die);
    }

    public function remove(SagradaDie $die): void
    {
        $this->collection->detach($die);
    }

    public function getAll(): array
    {
        return iterator_to_array($this->collection, false);
    }

    // TODO - Needs unit test
    public function getWithFilteredOutDuplicates(): array
    {
        return array_unique($this->getAll(), SORT_REGULAR);
    }

    public function count(): int
    {
        return $this->collection->count();
    }

    public function __toString()
    {
        $collection = $this->getAll();
        return implode(' | ', $collection);
    }
}
