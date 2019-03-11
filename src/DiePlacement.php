<?php
declare(strict_types=1);

namespace Sagrada;

use Sagrada\Board\Grid\GridCoordinates;
use Sagrada\Dice\SagradaDie;
use Sagrada\RestrictionModifiers\SagradaRestrictionModifierInterface;

class DiePlacement
{
    protected $coordinates;
    protected $die;

    /**
     * @var SagradaRestrictionModifierInterface
     */
    protected $restrictionModifier;

    public function __construct(SagradaDie $die, GridCoordinates $coordinates)
    {
        $this->coordinates = $coordinates;
        $this->die = $die;
    }

    /**
     * @return GridCoordinates
     */
    public function getCoordinates(): GridCoordinates
    {
        return $this->coordinates;
    }

    /**
     * @param GridCoordinates $coordinates
     */
    public function setCoordinates(GridCoordinates $coordinates): void
    {
        $this->coordinates = $coordinates;
    }

    /**
     * @return SagradaDie
     */
    public function getDie(): SagradaDie
    {
        return $this->die;
    }

    /**
     * @param SagradaDie $die
     */
    public function setDie(SagradaDie $die): void
    {
        $this->die = $die;
    }

    /**
     * @return SagradaRestrictionModifierInterface
     */
    public function getRestrictionModifier(): SagradaRestrictionModifierInterface
    {
        return $this->restrictionModifier;
    }

    /**
     * @param SagradaRestrictionModifierInterface $restrictionModifier
     */
    public function setRestrictionModifier(SagradaRestrictionModifierInterface $restrictionModifier): void
    {
        $this->restrictionModifier = $restrictionModifier;
    }
}
