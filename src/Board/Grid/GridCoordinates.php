<?php
declare(strict_types=1);

namespace Sagrada\Board\Grid;

class GridCoordinates
{
    protected $col;
    protected $row;

    public function __construct(int $row, int $col)
    {
        $this->col = $col;
        $this->row = $row;
    }

    /**
     * @return int
     */
    public function getCol(): int
    {
        return $this->col;
    }

    /**
     * @return int
     */
    public function getRow(): int
    {
        return $this->row;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf('(row=%d|col=%d)', $this->getRow(), $this->getCol());
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->__toString();
    }
}
