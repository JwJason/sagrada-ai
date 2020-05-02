<?php
declare(strict_types=1);

namespace Sagrada\Ai\Strategies\MonteCarloTree;

use function DeepCopy\deep_copy;
use Sagrada\DiePlacement;
use Sagrada\Game;

/**
 * Class NodeState
 *
 * @package Sagrada\Ai\Strategies\MonteCarloTree
 */
class NodeData
{
    /**
     * @var Game\State
     */
    protected $gameState;
    /**
     * @var DiePlacement|null
     */
    protected $lastDiePlacement;
    /**
     * @var int
     */
    protected $aggregateScore;
    /**
     * @var int
     */
    protected $visitCount;

    public function __construct(Game\State $gameState, ?DiePlacement $lastDiePlacement)
    {
        $this->gameState = deep_copy($gameState);
        $this->lastDiePlacement = $lastDiePlacement;
        $this->visitCount = 0;
        $this->aggregateScore = 0;
    }

    public function getGameState(): Game\State
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
//     * @return PlayerState
//     */
//    public function playTurnWithDiePlacement(DiePlacement $diePlacement): PlayerState
//    {
//        $gameState = $this->getGameState();
//        $gameState->decrementTurnsRemaining();
//        $gameState->placeDieOnBoardSpace($diePlacement);
//        return $gameState;
//    }
//
//    /**
//     * @return PlayerState
//     * @throws \Exception
//     */
//    public function playRandomDieDrawAndTurn(): PlayerState
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

//    /**
//     * @return bool
//     */
//    public function hasPlayableTurnsRemaining(): bool
//    {
//        $gameState = $this->getGameState();
//        return $gameState->hasTurnsRemaining() && $gameState->hasAnyPossibleMovesRemaining();
//    }

}

