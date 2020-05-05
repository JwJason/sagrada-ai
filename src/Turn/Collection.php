<?php
declare(strict_types=1);

namespace Sagrada\Turn;

use Sagrada\Turn;

class Collection
{
    /** @var Turn[] */
    protected $collection = [];

    public function add(Turn $turn): void
    {
        $this->collection[] = $turn;
    }

    public function getAll(): array
    {
        return $this->collection;
    }

    public function first(): Turn
    {
        if (empty($this->collection)) {
            throw new \RuntimeException('Cannot get first(); no items in collection');
        }
        return $this->collection[0];
    }

    public function last(): Turn
    {
        if (empty($this->collection)) {
            throw new \RuntimeException('Cannot get last(); no items in collection');
        }
        return end($this->collection);
    }
}
