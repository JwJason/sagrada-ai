<?php
declare(strict_types=1);

namespace Sagrada\Board\Factories;

use Sagrada\Board\Board;
use Sagrada\Board\Grid\GridCoordinates;
use Sagrada\Board\Space\BoardSpace;
use Sagrada\Board\Grid\Grid;
use Sagrada\Board\Space\Restriction\RestrictionsFactory;
use Sagrada\DieSpace\DieSpace;

class GridFactory
{
    public function createBoardGridFromSymbols(Board $board, array $gridSymbols, bool $cacheAdjacencyLookups=false): Grid
    {
        $grid = [];
        $factory = new RestrictionsFactory();

        foreach ($gridSymbols as $rowIndex => $row) {
            $gridRow = [];
            foreach ($row as $colIndex => $gridSymbol) {
                $coordinates = new GridCoordinates($rowIndex, $colIndex);
                $restriction = $factory->createRestrictionFromSymbol($gridSymbol);
                $space = new BoardSpace($coordinates, $board, new DieSpace(), $restriction);
                $space->setIntrinsicRestriction($restriction);
                $gridRow[] = $space;
            }
            $grid[] = $gridRow;
        }

        return new Grid($grid, $cacheAdjacencyLookups);
    }
}
