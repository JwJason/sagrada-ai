<?php
declare(strict_types=1);

namespace Sagrada\Board\Space;

use Sagrada\Board\Board;
use Sagrada\Board\Grid\GridCoordinates;
use Sagrada\Board\Space\Restriction\ColorRestriction\ColorRestrictionInterface;
use Sagrada\Board\Space\Restriction\NoRestriction;
use Sagrada\Board\Space\Restriction\Restrictions;
use Sagrada\Board\Space\Restriction\RestrictionsFactory;
use Sagrada\Board\Space\Restriction\ValueRestriction\ValueRestrictionInterface;
use Sagrada\DieSpace\DieSpace;
use Sagrada\Board\Space\Restriction\PartialRestrictionInterface;

/**
 * Class BoardSpace
 * @package Sagrada\Board\Space
 */
class BoardSpace
{
    /**
     * @var Board
     */
    protected $board;

    /**
     * @var GridCoordinates
     */
    protected $coordinates;

    /**
     * @var DieSpace
     */
    protected $dieSpace;

    /**
     * @var PartialRestrictionInterface
     */
    protected $intrinsicRestriction;

    /**
     * BoardSpace constructor.
     * @param GridCoordinates $coordinates
     * @param Board $board
     * @param DieSpace $dieSpace
     * @param PartialRestrictionInterface $intrinsicRestriction
     */
    public function __construct(
        GridCoordinates $coordinates,
        Board $board,
        DieSpace $dieSpace,
        PartialRestrictionInterface $intrinsicRestriction
    ) {
        $this->coordinates = $coordinates;
        $this->board = $board;
        $this->dieSpace = $dieSpace;
        $this->intrinsicRestriction = $intrinsicRestriction;
    }

    /**
     * @return GridCoordinates
     */
    public function getCoordinates(): GridCoordinates
    {
        return $this->coordinates;
    }

    /**
     * @return Restrictions
     */
    public function getDieRestrictions(): Restrictions
    {
        $factory = new RestrictionsFactory();

        if ($this->getDieSpace()->hasDie() === false) {
            return $factory->createEmptyRestrictions();
        }

        $die = $this->getDieSpace()->getDie();

        return $factory->createRestrictionsFromDiceColorAndValue(
            $die->getColor(),
            $die->getValue()
        );
    }

    /**
     * @return DieSpace
     */
    public function getDieSpace(): DieSpace
    {
        return $this->dieSpace;
    }

    /**
     * @param DieSpace $dieSpace
     */
    public function setDieSpace(DieSpace $dieSpace): void
    {
        $this->dieSpace = $dieSpace;
    }

    /**
     * @param PartialRestrictionInterface $intrinsicRestriction
     */
    public function setIntrinsicRestriction(PartialRestrictionInterface $intrinsicRestriction): void
    {
        $this->intrinsicRestriction = $intrinsicRestriction;
    }

    /**
     * @return PartialRestrictionInterface
     */
    public function getIntrinsicRestriction(): PartialRestrictionInterface
    {
        return $this->intrinsicRestriction;
    }

    /**
     * @return bool
     */
    public function hasDie(): bool
    {
        return $this->getDieSpace()->hasDie();
    }

    /**
     * @return bool
     */
    public function hasIntrinsicRestriction(): bool
    {
        return ($this->getIntrinsicRestriction() instanceof NoRestriction) === false;
    }

    /**
     * @return bool
     */
    public function hasIntrinsicColorRestriction(): bool
    {
        return $this->getIntrinsicRestriction() instanceof ColorRestrictionInterface;
    }

    /**
     * @return bool
     */
    public function hasIntrinsicValueRestriction(): bool
    {
        return $this->getIntrinsicRestriction() instanceof ValueRestrictionInterface;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getDieSpace()->hasDie()
            ? sprintf('<%s>', $this->getDieSpace()->getDie()->toString())
            : sprintf('[ %s ]', $this->getIntrinsicRestriction()->getSymbol());
    }

    /**
     * @return string
     */
    public function toString() : string
    {
        return $this->__toString();
    }
}
