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

    /**
     * Board constructor.
     * @param AbstractMetaBoard $meta
     * @throws \Exception
     */
    public function __construct(AbstractMetaBoard $meta)
    {
        $gridFactory = new GridFactory();
        $this->grid = $gridFactory->createBoardGridFromSymbols($this, $meta->getGridSymbols());
        $this->meta = $meta;
    }

    /**
     * @param GridCoordinates $coordinates
     * @return BoardSpaceCollection
     * @throws \Exception
     */
    public function getAllAdjacentSpaces(GridCoordinates $coordinates): BoardSpaceCollection
    {
        $adjacentCoordinates = $this->getGrid()->getAllAdjacentCoordinates($coordinates);
        return $this->getSpaces($adjacentCoordinates);
    }

    /**
     * @param GridCoordinates $coordinates
     * @return BoardSpaceCollection
     * @throws \Exception
     */
    public function getOrthongonallyAdjacentSpaces(GridCoordinates $coordinates): BoardSpaceCollection
    {
        $adjacentCoordinates = $this->getGrid()->getOrthogonallyAdjacentCoordinates($coordinates);
        return $this->getSpaces($adjacentCoordinates);
    }

    /**
     * @param GridCoordinates $coordinates
     * @return BoardSpaceCollection
     * @throws \Exception
     */
    public function getDiagonallyAdjacentSpaces(GridCoordinates $coordinates): BoardSpaceCollection
    {
        $adjacentCoordinates = $this->getGrid()->getDiagonallyAdjacentCoordinates($coordinates);
        return $this->getSpaces($adjacentCoordinates);
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
            $allSpaces = array_merge($allSpaces, $rowSpaces);
        }
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
            $openSpaces = array_merge($openSpaces, $rowOpenSpaces->getItems());
        }
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
            $coveredSpaces = array_merge($coveredSpaces, $rowCoveredSpaces->getItems());
        }
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
        $item = $this->getGrid()->getItem($coordinates);
        return $item;
    }

    /**
     * @param BoardSpace $boardSpace
     * @param GridCoordinates $coordinates
     * @throws \Exception
     */
    public function setSpace(BoardSpace $boardSpace, GridCoordinates $coordinates): void
    {
        $this->getGrid()->setItem($boardSpace, $coordinates);
    }

    /**
     * @param array $coordinatesCollection
     * @return BoardSpaceCollection
     * @throws \Exception
     */
    public function getSpaces(array $coordinatesCollection): BoardSpaceCollection
    {
        $spaces = [];
        foreach ($coordinatesCollection as $coordinates) {
            $spaces[] = $this->getSpace($coordinates);
        }
        return new BoardSpaceCollection($spaces);
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->__toString();
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
            $rowsArray[] = join(" ", $rowArray);
        }

        return join("\n", $rowsArray);
    }
}
