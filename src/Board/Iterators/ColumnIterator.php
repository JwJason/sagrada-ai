<?php
declare(strict_types=1);

namespace Sagrada\Board\Iterators;

use Sagrada\Board\Space\BoardSpaceCollection;

class ColumnIterator extends AbstractBoardIterator
{
    /** @var int */
    protected $col = 0;

    public function rewind(): void
    {
        $this->col = 0;
    }

    /**
     * @return BoardSpaceCollection
     * @throws \Exception
     */
    public function current(): BoardSpaceCollection
    {
        return $this->board->getCol($this->col);
    }

    /**
     * @return int
     */
    public function key(): int
    {
        return $this->col;
    }

    public function next(): void
    {
        ++$this->col;
    }

    public function valid(): bool
    {
        return $this->board->getGrid()->isValidCol($this->col);
    }
}
