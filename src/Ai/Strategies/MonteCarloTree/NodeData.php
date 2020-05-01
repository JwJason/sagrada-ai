<?php
declare(strict_types=1);

namespace Sagrada\Ai\Strategies\MonteCarloTree;

use function DeepCopy\deep_copy;
use Sagrada\Dice\SagradaDie;
use Sagrada\DiePlacement;
use Sagrada\DiePlacement\Finder;
use Sagrada\Game\PlayerGameState;
use Sagrada\DiePlacement\Validator;

/**
 * Class NodeState
 *
 * @package Sagrada\Ai\Strategies\MonteCarloTree
 */
class NodeData
{
    /**
     * @var mixed
     */
    protected $gameState;
    /**
     * @var DiePlacement|null
     */
    protected $lastDiePlacement;
    /**
     * @var SagradaDie
     */
    // XXX TODO DELETE THIS
    protected $currentDie;
    /**
     * @var int
     */
    protected $aggregateScore;
    /**
     * @var int
     */
    protected $visitCount;

    /**
     * NodeState constructor.
     * @param PlayerGameState $gameState
     * @param DiePlacement $lastDiePlacement
     * @param SagradaDie $currentDie
     */
    public function __construct(PlayerGameState $gameState, ?DiePlacement $lastDiePlacement/*, SagradaDie $currentDie*/)
    {
        $this->gameState = deep_copy($gameState);
        $this->lastDiePlacement = $lastDiePlacement;
//        $this->currentDie = $currentDie;
        $this->visitCount = 0;
        $this->aggregateScore = 0;
    }

    /**
     * @return PlayerGameState
     */
    public function getGameState(): PlayerGameState
    {
        return $this->gameState;
    }

    /**
     * @return DiePlacement
     */
    public function getLastDiePlacement(): DiePlacement
    {
        return $this->lastDiePlacement;
    }

//    /**
//     * @return SagradaDie
//     */
//    public function getCurrentDie(): SagradaDie
//    {
//        return $this->currentDie;
//    }

    /**
     * @return int
     */
    public function getVisitCount(): int
    {
        return $this->visitCount;
    }

    /**
     *
     */
    public function incrementVisitCount(): void
    {
        ++$this->visitCount;
    }

//    /**
//     * @param SagradaDie $die
//     * @return array
//     * @throws \Exception
//     */
//    protected function getAllValidPlacementsForDie(SagradaDie $die)
//    {
//        $gameState = $this->getGameState();
//        $placementFinder = new Finder(new Validator());
//        $board = $gameState->getBoard();
//        $validDiePlacements = $placementFinder->getAllValidDiePlacementsForDie($die, $board);
//        return $validDiePlacements;
//    }

//    /**
//     * @param DiePlacement $diePlacement
//     * @throws \Exception
//     * @return PlayerGameState
//     */
//    public function playTurnWithDiePlacement(DiePlacement $diePlacement): PlayerGameState
//    {
//        $gameState = $this->getGameState();
//        $gameState->decrementTurnsRemaining();
//        $gameState->placeDieOnBoardSpace($diePlacement);
//        return $gameState;
//    }
//
//    /**
//     * @return PlayerGameState
//     * @throws \Exception
//     */
//    public function playRandomDieDrawAndTurn(): PlayerGameState
//    {
//        $gameState = $this->getGameState();
//        $gameState->decrementTurnsRemaining();
//
//        $die = $gameState->getDiceBag()->drawDie();
//
//        if (empty($validDiePlacements)) {
//            return $gameState;
//        }
//
//        $diePlacement = $validDiePlacements[array_rand($validDiePlacements)];
//        $gameState->placeDieOnBoardSpace($diePlacement);
//
//        return $gameState;
//    }

    /**
     * @return bool
     */
    public function hasPlayableTurnsRemaining(): bool
    {
        $gameState = $this->getGameState();
        return $gameState->hasTurnsRemaining() && $gameState->hasAnyPossibleMovesRemaining();
    }

    /**
     * @return int
     */
    public function getAggregateScore(): int
    {
        return $this->aggregateScore;
    }

    /**
     * @param int $amount
     */
    public function increaseAggregateScore(int $amount): void
    {
        $this->aggregateScore += $amount;
    }
}

