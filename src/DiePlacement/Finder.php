<?php
declare(strict_types=1);

namespace Sagrada\DiePlacement;

use Sagrada\Board\Board;
use Sagrada\Board\Grid\GridCoordinates;
use Sagrada\Board\Iterators\RowIterator;
use Sagrada\DieCollection;
use Sagrada\DiePlacement;
use Sagrada\Dice\SagradaDie;

/**
 * Class Finder
 * @package Sagrada\Validators
 */
class Finder
{
    /**
     * @var Validator
     */
    protected $placementValidator;

    /**
     * Finder constructor.
     * @param Validator $placementValidator
     */
    public function __construct(Validator $placementValidator)
    {
        $this->placementValidator = $placementValidator;
    }

    /**
     * @param SagradaDie $die
     * @param Board $board
     * @return array
     * @throws \Exception
     */
    public function getAllValidDiePlacementsForDie(SagradaDie $die, Board $board): array
    {
        $iterator = new RowIterator($board);
        $placementValidator = $this->placementValidator;
        $validPlacements = [];

        foreach ($iterator as $rowIndex => $row) {
            foreach ($row->getFilteredByNotHavingDice()->getItems() as $colIndex => $space) {
                $coordinates = new GridCoordinates($rowIndex, $colIndex);
                $diePlacement = new DiePlacement($die, $coordinates);

                if ($placementValidator->isValidDiePlacement($diePlacement, $board)) {
                    $validPlacements[] = $diePlacement;
                }
            }
        }

        return $validPlacements;
    }

    public function getAllValidDiePlacementsForDieCollection(DieCollection $dieCollection, Board $board): array
    {
        $filteredCollection = $dieCollection->getWithFilteredOutDuplicates();
        $validDiePlacements = [];

        foreach ($filteredCollection as $die) {
            $validDiePlacements[] = $this->getAllValidDiePlacementsForDie($die, $board);
        }
        return array_merge(...$validDiePlacements);
    }
}
