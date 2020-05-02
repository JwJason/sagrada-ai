<?php
declare(strict_types=1);

namespace Sagrada\Ai;

use Sagrada\Board\Space\BoardSpace;
use Sagrada\Dice\Color\DiceColorInterface;
use Sagrada\DiceBag;
use Sagrada\Dice\Value\DiceValueManager;
use Sagrada\DiePlacement;
use Sagrada\Validators\PlacementFinder;

// XXX : Only examines *intrinsic* restrictions. Fix or remove.
/**
 * Class ProbabilityCalculator
 * @package Sagrada\Ai
 */
class ProbabilityCalculator
{
    /**
     * @param BoardSpace $boardSpace
     * @param DiceBag $diceBag
     * @param PlacementFinder $placementFinder
     * @return float|int
     * @throws \Exception
     */
    public function getProbabilityOfAnyValidMoveOnSpace(
        BoardSpace $boardSpace,
        DiceBag $diceBag,
        PlacementFinder $placementFinder
    ) : float {
        if ($boardSpace->hasDie()) {
            throw new \Exception("There is already a die on this board space.");
        }

        $validColors = $placementFinder->getAllIntrinsicallyValidColorsForBoardSpace($boardSpace);

        $remainingDice = $diceBag->getAllRemainingCount();
        if ($remainingDice === 0) {
            return 0.0;
        }

        $colorProbabilities = array_map(
            function (DiceColorInterface $color) use ($diceBag, $remainingDice) : float {
                $remainingDiceOfColor = $diceBag->getColorRemainingCount($color);
                return $remainingDiceOfColor / $remainingDice;
            },
            $validColors
        );
        $totalColorProbability = array_reduce(
            $colorProbabilities,
            function (float $colorProbability, float $total) {
                return $total + $colorProbability;
            },
            0
        );
        $totalValueProbability = count($validValues) / count($valueManager->getAllValues());

        return $totalColorProbability * $totalValueProbability;
    }

    /**
     * Given the remaining die in the bag and the current board state, returns the probability that the die placement
     * will be possible on a turn.
     *
     * @param DiePlacement $diePlacement
     * @param DiceBag $diceBag
     * @throws \Exception
     * @return float
     */
    public function getProbabilityOfDiePlacement(DiePlacement $diePlacement, DiceBag $diceBag) : float
    {
        $color = $diePlacement->getDie()->getColor();
        return $this->getProbabilityOfDieColorDraw($color, $diceBag) * $this->getProbabilityOfDieValueRoll();
    }

    /**
     * Given the remaining die in the bag, returns the probability that the color will be drawn on a turn.
     *
     * @param DiceColorInterface $color
     * @param DiceBag $diceBag
     * @return float
     * @throws \Exception
     */
    public function getProbabilityOfDieColorDraw (
        DiceColorInterface $color,
        DiceBag $diceBag
    ) : float {
        $remainingDice = $diceBag->getAllRemainingCount();
        if ($remainingDice === 0) {
            return 0.0;
        }
        $remainingDiceOfColor = $diceBag->getColorRemainingCount($color);
        return floatval($remainingDiceOfColor / $remainingDice);
    }

    /**
     * Returns the probability that a certain value will be rolled.
     *
     * @return float|int
     */
    public function getProbabilityOfDieValueRoll()
    {
        $valueManager = new DiceValueManager();
        $allValues = $valueManager->getAllValues();
        return floatval(1 / count($allValues));
    }

    /**
     * @param BoardSpace $boardSpace
     * @param PlacementFinder $placementFinder
     * @return float
     */
    public function getProbabilityOfAnyValidValueMoveOnSpace(
        BoardSpace $boardSpace,
        PlacementFinder $placementFinder) : float {
        $valueManager = new DiceValueManager();
        $validValues = $placementFinder->getAllIntrinsicallyValidValuesForBoardSpace($boardSpace);
        return floatval(count($validValues) / count($valueManager->getAllValues()));
    }
}
