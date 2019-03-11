<?php
declare(strict_types=1);

namespace Sagrada\Board\Grid;

class Grid
{
    protected $grid;

    /**
     * Grid constructor.
     * @param array $gridArray
     * @throws \Exception
     */
    public function __construct(array $gridArray)
    {
        $this->setGrid($gridArray);
    }

    /**
     * @param GridCoordinates $coordinates
     * @return GridCoordinates[]
     * @throws \Exception
     */
    public function getOrthogonallyAdjacentCoordinates(GridCoordinates $coordinates): array
    {
        if (!$this->areValidCoordinates($coordinates)) {
            throw new \Exception(sprintf('Invalid coordinates: %s', $coordinates));
        }

        $row = $coordinates->getRow();
        $col = $coordinates->getCol();
        $adjacentCoordinates = [
            'left'   => new GridCoordinates($row - 1, $col),
            'right'  => new GridCoordinates($row + 1, $col),
            'top'    => new GridCoordinates($row, $col - 1),
            'bottom' => new GridCoordinates($row, $col + 1),
        ];

        return array_filter($adjacentCoordinates, function(GridCoordinates $adjacentCoordinate) {
            return $this->areValidCoordinates($adjacentCoordinate);
        });
    }

    /**
     * @param GridCoordinates $coordinates
     * @return GridCoordinates[]
     * @throws \Exception
     */
    public function getDiagonallyAdjacentCoordinates(GridCoordinates $coordinates): array
    {
        if (!$this->areValidCoordinates($coordinates)) {
            throw new \Exception(sprintf('Invalid coordinates: %s', $coordinates));
        }

        $row = $coordinates->getRow();
        $col = $coordinates->getCol();
        $adjacentCoordinates = [
            'leftTop'   => new GridCoordinates($row - 1, $col - 1),
            'rightTop'  => new GridCoordinates($row + 1, $col - 1),
            'leftBottom'    => new GridCoordinates($row - 1, $col + 1),
            'rightBottom' => new GridCoordinates($row + 1, $col + 1),
        ];

        return array_filter($adjacentCoordinates, function(GridCoordinates $adjacentCoordinate) {
            return $this->areValidCoordinates($adjacentCoordinate);
        });
    }

    /**
     * @param GridCoordinates $coordinates
     * @return GridCoordinates[]
     * @throws \Exception
     */
    public function getAllAdjacentCoordinates(GridCoordinates $coordinates): array
    {
        return array_merge(
            $this->getOrthogonallyAdjacentCoordinates($coordinates),
            $this->getDiagonallyAdjacentCoordinates($coordinates)
        );
    }

    /**
     * @param int $col
     * @return array
     * @throws \Exception
     */
    public function getCol(int $col): array
    {
        if (!$this->isValidCol($col)) {
            throw new \Exception(sprintf('Invalid column index: %d', $col));
        }
        $grid = $this->getGrid();
        return array_column($grid, $col);
    }

    /**
     * @param GridCoordinates $coordinates
     * @throws \Exception
     * @return mixed
     */
    public function getItem(GridCoordinates $coordinates)
    {
        if (!$this->areValidCoordinates($coordinates)) {
            throw new \Exception(sprintf('Invalid coordinates: %s', $coordinates));
        }
        $grid = $this->getGrid();
        $row = $coordinates->getRow();
        $col = $coordinates->getCol();
        return $grid[$row][$col];
    }

    /**
     * @param $item
     * @param GridCoordinates $coordinates
     * @throws \Exception
     */
    public function setItem($item, GridCoordinates $coordinates): void
    {
        if (!$this->areValidCoordinates($coordinates)) {
            throw new \Exception(sprintf('Invalid coordinates: %s', $coordinates));
        }
        $grid = $this->getGrid();
        $row = $coordinates->getRow();
        $col = $coordinates->getCol();
        $grid[$row][$col] = $item;
    }

    /**
     * @return array
     */
    public function getGrid(): array
    {
        return $this->grid;
    }

    /**
     * @param array $grid
     * @throws \Exception
     */
    public function setGrid(array $grid): void
    {
        $this->validateGridArrayOrThrowException($grid);
        $this->grid = $grid;
    }

    /**
     * @param int $row
     * @return array
     * @throws \Exception
     */
    public function getRow(int $row): array
    {
        if (!$this->isValidRow($row)) {
            throw new \Exception(sprintf('Invalid row index: %d', $row));
        }
        $grid = $this->getGrid();
        return $grid[$row];
    }

    /**
     * @return int
     */
    public function getColCount(): int
    {
        if (isset($this->grid[0])) {
            return count($this->grid[0]);
        } else {
            return 0;
        }
    }

    /**
     * @return int
     */
    public function getRowCount(): int
    {
        return count($this->grid);
    }

    /**
     * @param int $col
     * @return bool
     */
    public function isValidCol(int $col): bool
    {
        return $col < $this->getColCount();
    }

    /**
     * @param int $row
     * @return bool
     */
    public function isValidRow(int $row): bool
    {
        return $row < $this->getRowCount();
    }

    /**
     * @param GridCoordinates $coordinates
     * @return bool
     */
    public function areValidCoordinates(GridCoordinates $coordinates): bool
    {
        $row = $coordinates->getRow();
        $col = $coordinates->getCol();
        $grid = $this->grid;

        return isset($grid[$row]) && isset($grid[$row][$col]);
    }

    /**
     * @param array $grid
     * @throws \Exception
     */
    protected function validateGridArrayOrThrowException(array $grid): void
    {
        $expectedCount = null;

        foreach ($grid as $row) {
            if (!is_array($row)) {
                throw new \Exception(
                    sprintf(
                        'Invalid grid row: expected type array, got type %s',
                        gettype($row)
                    )
                );
            }

            $rowCount = count($row);
            if ($expectedCount !== null && $expectedCount !== $rowCount) {
                throw new \Exception(
                    sprintf(
                        'Invalid grid row: expected row count of %d, got row count of %d',
                        $expectedCount,
                        $rowCount
                    )
                );
            }
            $expectedCount = $expectedCount ?? $rowCount;
        }
    }
}
