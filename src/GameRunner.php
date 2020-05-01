<?php
declare(strict_types=1);

namespace Sagrada;

use Sagrada\ScoreCards\SagradaScoreCardCollection;
use Sagrada\Scoring\BoardScorer;
use Sagrada\Scoring\Scorers\FromSagradaScoreCardFactory;

class GameRunner
{
    /** @var DiePlacement\Finder */
    protected $placementFinder;
    /** @var DiePlacement\BoardPlacer */
    protected $placementPlacer;
    /** @var DiePlacement\Validator */
    protected $placementValidator;
    /** @var Player\SagradaPlayer */
    protected $player1;
    /** @var Player\SagradaPlayer */
    protected $player2;
    /** @var SagradaScoreCardCollection */
    protected $scoreCards;

    public function getPlayer1BoardScore(): int
    {
        $scorerFactory = new FromSagradaScoreCardFactory();
        $boardScorer = $scorerFactory->createFromScoreCardCollection($this->getScoreCards(), $this->getPlayer1()->getBoard());
        return $boardScorer->getScore();
    }

    public function getPlayer2BoardScore(): int
    {
        $scorerFactory = new FromSagradaScoreCardFactory();
        $boardScorer = $scorerFactory->createFromScoreCardCollection($this->getScoreCards(), $this->getPlayer2()->getBoard());
        return $boardScorer->getScore();
    }

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
    public function getPlacementPlacer(): DiePlacement\BoardPlacer
    {
        return $this->placementPlacer;
    }

    /**
     * @param DiePlacement\BoardPlacer $placementPlacer
     */
    public function setPlacementPlacer(DiePlacement\BoardPlacer $placementPlacer): void
    {
        $this->placementPlacer = $placementPlacer;
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

    /**
     * @return Player\SagradaPlayer
     */
    public function getPlayer1(): Player\SagradaPlayer
    {
        return $this->player1;
    }

    /**
     * @param Player\SagradaPlayer $player1
     */
    public function setPlayer1(Player\SagradaPlayer $player1): void
    {
        $this->player1 = $player1;
    }

    /**
     * @return Player\SagradaPlayer
     */
    public function getPlayer2(): Player\SagradaPlayer
    {
        return $this->player2;
    }

    /**
     * @param Player\SagradaPlayer $player2
     */
    public function setPlayer2(Player\SagradaPlayer $player2): void
    {
        $this->player2 = $player2;
    }

    /**
     * @return SagradaScoreCardCollection
     */
    public function getScoreCards(): SagradaScoreCardCollection
    {
        return $this->scoreCards;
    }

    /**
     * @param SagradaScoreCardCollection $scoreCards
     */
    public function setScoreCards(SagradaScoreCardCollection $scoreCards): void
    {
        $this->scoreCards = $scoreCards;
    }
}
