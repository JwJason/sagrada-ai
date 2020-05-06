<?php
declare(strict_types=1);

namespace Sagrada\Board;

use Sagrada\Board\Factories\GridFactory;
use Sagrada\Board\Grid\Grid;
use Sagrada\Board\Grid\GridCoordinates;
use Sagrada\Board\Iterators\RowIterator;
use Sagrada\Board\Meta\AbstractMetaBoard;
use Sagrada\Board\Space\BoardSpace;
use Sagrada\Board\Space\BoardSpaceCollection;

class Board
{
    /** @var Grid */
    protected $grid;

    /** @var AbstractMetaBoard */
    protected $meta;

    /** @var bool */
    protected $hasAnyDice;

    public function __construct(AbstractMetaBoard $meta, bool $cacheAdjacencyLookups=false)
    {
        $gridFactory = new GridFactory();
        $this->grid = $gridFactory->createBoardGridFromSymbols($this, $meta->getGridSymbols(), $cacheAdjacencyLookups);
        $this->meta = $meta;
        $this->hasAnyDice = false;
    }

    /**
     * @param GridCoordinates $coordinates
     * @return BoardSpaceCollection
     * @throws \Exception
     */
    public function getAllAdjacentSpaces(GridCoordinates $coordinates): BoardSpaceCollection
    {
        $spaces = $this->getGrid()->getAllAdjacentSpaces($coordinates);
        return new BoardSpaceCollection($spaces);
    }

    /**
     * @param GridCoordinates $coordinates
     * @return BoardSpaceCollection
     * @throws \Exception
     */
    public function getOrthongonallyAdjacentSpaces(GridCoordinates $coordinates): BoardSpaceCollection
    {
        $spaces = $this->getGrid()->getOrthogonallyAdjacentSpaces($coordinates);
        return new BoardSpaceCollection($spaces);
    }

    /**
     * @param GridCoordinates $coordinates
     * @return BoardSpaceCollection
     * @throws \Exception
     */
    public function getDiagonallyAdjacentSpaces(GridCoordinates $coordinates): BoardSpaceCollection
    {
        $spaces = $this->getGrid()->getDiagonallyAdjacentSpaces($coordinates);
        return new BoardSpaceCollection($spaces);
    }

    /**
     * @param int $col
     * @return BoardSpaceCollection
     * @throws \Exception
     */
    public function getCol(int $col): BoardSpaceCollection
    {
        $colArray = $this->getGrid()->getCol($col);
        return new BoardSpaceCollection($colArray);
    }

    /**
     * @return Grid
     */
    public function getGrid(): Grid
    {
        return $this->grid;
    }

    /**
     * @return AbstractMetaBoard
     */
    public function getMeta(): AbstractMetaBoard
    {
        return $this->meta;
    }

    /**
     * @return BoardSpaceCollection
     */
    public function getAllSpaces(): BoardSpaceCollection
    {
        $iterator = new RowIterator($this);
        $allSpaces = [];
        foreach ($iterator as $row) {
            $rowSpaces = $row->getItems();
            $allSpaces[] = $rowSpaces;
        }
        $allSpaces = array_merge(...$allSpaces);
        return new BoardSpaceCollection($allSpaces);
    }

    /**
     * @return BoardSpaceCollection
     */
    public function getAllOpenSpaces(): BoardSpaceCollection
    {
        $iterator = new RowIterator($this);
        $openSpaces = [];
        foreach ($iterator as $row) {
            $rowOpenSpaces = $row->getFilteredByNotHavingDice();
            $openSpaces[] = $rowOpenSpaces->getItems();
        }
        $openSpaces = array_merge(...$openSpaces);
        return new BoardSpaceCollection($openSpaces);
    }

    /**
     * @return BoardSpaceCollection
     */
    public function getAllCoveredSpaces(): BoardSpaceCollection
    {
        $iterator = new RowIterator($this);
        $coveredSpaces = [];
        foreach ($iterator as $row) {
            $rowCoveredSpaces = $row->getFilteredByHavingDice();
            $coveredSpaces[] = $rowCoveredSpaces->getItems();
        }
        $coveredSpaces = array_merge(...$coveredSpaces);
        return new BoardSpaceCollection($coveredSpaces);
    }

    /**
     * @param int $row
     * @return BoardSpaceCollection
     * @throws \Exception
     */
    public function getRow(int $row): BoardSpaceCollection
    {
        $rowArray = $this->getGrid()->getRow($row);
        return new BoardSpaceCollection($rowArray);
    }

    /**
     * @param GridCoordinates $coordinates
     * @throws \Exception
     * @return BoardSpace
     */
    public function getSpace(GridCoordinates $coordinates): BoardSpace
    {
        return $this->getGrid()->getItem($coordinates);
    }

    /**
     * @param BoardSpace $boardSpace
     * @param GridCoordinates $coordinates
     * @throws \Exception
     */
    public function setSpace(BoardSpace $boardSpace, GridCoordinates $coordinates): void
    {
        $this->getGrid()->setItem($boardSpace, $coordinates);
        $this->hasAnyDice = true;
    }

    /**
     * @return bool
     */
    public function hasAnyDice(): bool
    {
        return $this->hasAnyDice;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $rowsArray = [];
        $iterator = new RowIterator($this);

        foreach ($iterator as $row) {
            $rowArray = array_map(function(BoardSpace $boardSpace) {
                return $boardSpace->toString();
            }, $row->getItems());
            $rowsArray[] = implode(" ", $rowArray);
        }

        return implode("\n", $rowsArray);
    }
}
