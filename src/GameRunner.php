<?php
declare(strict_types=1);

namespace Sagrada;

class GameRunner
{
    /** @var DiePlacement\Finder */
    protected $placementFinder;
    /** @var DiePlacement\BoardPlacer */
    protected $placementManager;
    /** @var DiePlacement\Validator */
    protected $placementValidator;
    /** @var Player\SagradaPlayer */
    protected $player1;
    /** @var Player\SagradaPlayer */
    protected $player2;

    /**
     * @return DiePlacement\Finder
     */
    public function getPlacementFinder(): DiePlacement\Finder
    {
        return $this->placementFinder;
    }

    /**
     * @param DiePlacement\Finder $placementFinder
     */
    public function setPlacementFinder(DiePlacement\Finder $placementFinder): void
    {
        $this->placementFinder = $placementFinder;
    }

    /**
     * @return DiePlacement\BoardPlacer
     */
    public function getPlacementManager(): DiePlacement\BoardPlacer
    {
        return $this->placementManager;
    }

    /**
     * @param DiePlacement\BoardPlacer $placementManager
     */
    public function setPlacementManager(DiePlacement\BoardPlacer $placementManager): void
    {
        $this->placementManager = $placementManager;
    }

    /**
     * @return DiePlacement\Validator
     */
    public function getPlacementValidator(): DiePlacement\Validator
    {
        return $this->placementValidator;
    }

    /**
     * @param DiePlacement\Validator $placementValidator
     */
    public function setPlacementValidator(DiePlacement\Validator $placementValidator): void
    {
        $this->placementValidator = $placementValidator;
    }
}
