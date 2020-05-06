<?php
declare(strict_types=1);

namespace Sagrada\DiePlacement;

use Sagrada\Board\Space\BoardSpace;
use Sagrada\Board\Space\Restriction\NoRestriction;
use Sagrada\Dice\Color\DiceColorInterface;
use Sagrada\Dice\SagradaDie;
use Sagrada\Dice\Value\DiceValueInterface;
use Sagrada\DiePlacement;
use Sagrada\Board\Board;

/**
 * Class Validator
 *
 *  awareness of space value/color restrictions to determine if a given dice placement is invalid.
 *
 * @package Sagrada\Validators
 */
class Validator
{
    /**
     * @param DiePlacement $diePlacement
     * @param Board $board
     * @return bool
     * @throws \Exception
     */
    public function isValidDiePlacement(DiePlacement $diePlacement, Board $board): bool
    {
        if ($board->getGrid()->areValidCoordinates($diePlacement->getCoordinates()) === false) {
            throw new \Exception(sprintf("Invalid grid coordinates: %s", $diePlacement->getCoordinates()));
        }

        $boardSpace = $board->getSpace($diePlacement->getCoordinates());
        $die = $diePlacement->getDie();

        return $boardSpace->hasDie() === false
            && $this->dieMeetsIntrinsicRequirements($die, $boardSpace)
            && $this->placementMeetsBoardRequirements($diePlacement, $board)
            && $this->placementMeetsGameRequirements($diePlacement, $board);
    }

    /**
     * @param DiePlacement $diePlacement
     * @param Board $board
     * @return bool
     * @throws \Exception
     */
    public function placementMeetsGameRequirements(DiePlacement $diePlacement, Board $board): bool
    {
        // If there are no dice currently on the board, the new die must be placed along the outer edge.
        if ($board->hasAnyDice() === false) {
            $row = $diePlacement->getCoordinates()->getRow();
            $col = $diePlacement->getCoordinates()->getCol();
            return ($row === 0 || $row === $board->getGrid()->getRowCount() - 1)
                || ($col === 0 || $col === $board->getGrid()->getColCount() - 1);
        }
        // If there are dice currently on the board, the new die must be placed on space which is adjacent to other dice.
        else {
            $adjacentSpaces = $board->getAllAdjacentSpaces($diePlacement->getCoordinates());
            $adjacentSpacesWithDice = $adjacentSpaces->getFilteredByHavingDice();
            return $adjacentSpacesWithDice->getCount() > 0;
        }
    }

    /**
     * @param DiePlacement $diePlacement
     * @param Board $board
     * @return bool
     * @throws \Exception
     */
    public function placementMeetsBoardRequirements(DiePlacement $diePlacement, Board $board): bool
    {
        $adjacentSpaces = $board->getOrthongonallyAdjacentSpaces($diePlacement->getCoordinates());
        foreach ($adjacentSpaces->getItems() as $adjacentSpace) {
            if ($this->dieMeetsBoardSpaceRequirements($diePlacement->getDie(), $adjacentSpace) === false) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param DiceColorInterface $dieColor
     * @param BoardSpace $boardSpace
     * @return bool
     */
    public function colorMeetsIntrinsicColorRequirements(DiceColorInterface $dieColor, BoardSpace $boardSpace): bool
    {
        if ($boardSpace->hasIntrinsicColorRestriction() === false) {
            return true;
        }
        $requiredColor = $boardSpace->getIntrinsicRestriction()->getColor();

        return $dieColor instanceof $requiredColor;
    }

    /**
     * @param DiceValueInterface $dieValue
     * @param BoardSpace $boardSpace
     * @return bool
     */
    public function valueMeetsIntrinsicValueRequirements(DiceValueInterface $dieValue, BoardSpace $boardSpace): bool
    {
        if ($boardSpace->hasIntrinsicValueRestriction() === false) {
            return true;
        }
        $requiredValue = $boardSpace->getIntrinsicRestriction()->getValue();

        return $dieValue instanceof $requiredValue;
    }

    /**
     * @param SagradaDie $die
     * @param BoardSpace $boardSpace
     * @return bool
     */
    public function dieMeetsIntrinsicRequirements(SagradaDie $die, BoardSpace $boardSpace): bool
    {
        if ($boardSpace->hasIntrinsicRestriction() === false) {
            return true;
        }
        return $this->colorMeetsIntrinsicColorRequirements($die->getColor(), $boardSpace)
            && $this->valueMeetsIntrinsicValueRequirements($die->getValue(), $boardSpace);
    }

    /**
     * @param SagradaDie $die
     * @param BoardSpace $boardSpace
     * @return bool
     */
    public function dieMeetsBoardSpaceRequirements(SagradaDie $die, BoardSpace $boardSpace): bool
    {
        $restrictions = $boardSpace->getDieRestrictions();
        $colorRestriction = $restrictions->getColorRestriction();
        $valueRestriction = $restrictions->getValueRestriction();

        if (($colorRestriction instanceof NoRestriction) === false) {
            $prohibitedColor = $colorRestriction->getColor();
            $dieColor = $die->getColor();

            if ($dieColor instanceof $prohibitedColor) {
                return false;
            }
        }

        if (($valueRestriction instanceof NoRestriction) === false) {
            $prohibitedValue = $valueRestriction->getValue();
            $dieValue = $die->getValue();

            if ($dieValue instanceof $prohibitedValue) {
                return false;
            }
        }

        return true;
    }
}
