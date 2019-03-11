<?php
declare(strict_types=1);

namespace Sagrada;

use Sagrada\Board\Board;
use Sagrada\Board\Grid\GridCoordinates;
use Sagrada\Board\Iterators\RowIterator;
use Sagrada\Dice\SagradaDie;
use Sagrada\Validators\DiePlacementValidator;

/**
 * Class DiePlacementFinder
 * @package Sagrada\Validators
 */
class DiePlacementFinder
{
    /**
     * @var DiePlacementValidator
     */
    protected $placementValidator;

    /**
     * DiePlacementFinder constructor.
     * @param DiePlacementValidator $placementValidator
     */
    public function __construct(DiePlacementValidator $placementValidator)
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

//    /**
//     * @param BoardSpace $boardSpace
//     * @param Board $board
//     * @return DiePlacement[]
//     * @throws \Exception
//     */
//    public function getAllIntrinsicallyValidDiePlacementsForBoardSpace(BoardSpace $boardSpace, Board $board): array
//    {
//        // XXX: I think this can be optimized; isValidDiePlacement() is what we need, we may be able
//        // to delete the $this->getAllIntrinsicallyValid*ForBoardSpace() functions entirely
//        $validValues = $this->getAllIntrinsicallyValidValuesForBoardSpace($boardSpace);
//        $validColors = $this->getAllIntrinsicallyValidColorsForBoardSpace($boardSpace);
//
//        $validDiePlacements = [];
//
//        foreach ($validColors as $color) {
//            foreach ($validValues as $value) {
//                $diePlacement = new DiePlacement(
//                    new SagradaDie($color, $value),
//                    $boardSpace->getCoordinates()
//                );
//                if ($this->placementValidator->isValidDiePlacement($diePlacement, $board) === true) {
//                    $validDiePlacements[] = $diePlacement;
//                }
//            }
//        }
//
//        return $validDiePlacements;
//    }
//
//    /**
//     * @param BoardSpace $boardSpace
//     * @return DiceValueInterface[]
//     */
//    public function getAllIntrinsicallyValidValuesForBoardSpace(BoardSpace $boardSpace): array
//    {
//        $valueManager = new DiceValueManager();
//        $allValues = $valueManager->getAllValues();
//
//        $validValues = array_filter(
//            $allValues,
//            function(DiceValueInterface $value) use ($boardSpace): bool {
//                return $this->placementValidator->valueMeetsIntrinsicValueRequirements($value, $boardSpace);
//            }
//        );
//
//        return $validValues;
//    }
//
//    /**
//     * @param BoardSpace $boardSpace
//     * @return DiceColorInterface[]
//     */
//    public function getAllIntrinsicallyValidColorsForBoardSpace(BoardSpace $boardSpace): array
//    {
//        $colorManager = new DiceColorManager();
//        $allColors = $colorManager->getAllColors();
//
//        $validColors = array_filter(
//            $allColors,
//            function (DiceColorInterface $color) use ($boardSpace): bool {
//                return $this->placementValidator->colorMeetsIntrinsicColorRequirements($color, $boardSpace);
//            }
//        );
//
//        return $validColors;
//    }
}