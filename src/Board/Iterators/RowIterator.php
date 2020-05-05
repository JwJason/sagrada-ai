<?php
declare(strict_types=1);

namespace Sagrada\Board\Iterators;

use Sagrada\Board\Space\BoardSpaceCollection;

class RowIterator extends AbstractBoardIterator
{
    /** @var int */
    protected $row = 0;

    public function rewind(): void
    {
        $this->row = 0;
    }

    /**
     * @return BoardSpaceCollection
     * @throws \Exception
     */
    public function current(): BoardSpaceCollection
    {
        return $this->board->getRow($this->row);
    }

    /**
     * @return int
     */
    public function key(): int
    {
        return $this->row;
    }

    public function next(): void
    {
        ++$this->row;
    }

    public function valid(): bool
    {
        return $this->board->getGrid()->isValidRow($this->row);
    }
}
