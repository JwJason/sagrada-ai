<?php
declare(strict_types=1);

namespace Sagrada\Ai\Simulations;

use Sagrada\Game\GameResults;
use Sagrada\Game\PlayerGameState;
use Sagrada\Game\Score;
use Sagrada\GameRunner;
use Sagrada\Scoring\Scorers\FromSagradaScoreCardFactory;

/**
 * Class GameSimulator
 * @package Sagrada\Ai\Simulations
 */
class GameSimulator
{
    /** @var GameRunner */
    protected $game;

    /**
     * GameSimulator constructor.
     */
    public function __construct(GameRunner $game)
    {
        $this->game = $game;
    }

    /**
     * @param PlayerGameState $initialGameState
     * @return GameResults
     * @throws \Sagrada\DiePlacement\IllegalBoardPlacementException
     * @throws \Exception
     */
    public function simulateRandomPlayout(PlayerGameState $initialGameState): GameResults
    {
        $gameState = $initialGameState->deepCopy();
        $placementFinder = $this->game->getPlacementFinder();
        $placementManager = $this->game->getPlacementPlacer();

        while ($gameState->hasTurnsRemaining() && $gameState->hasAnyPossibleMovesRemaining()) {
            $gameState->decrementTurnsRemaining();

            $board = $gameState->getBoard();
            $die = $gameState->getDiceBag()->drawDie();

            $validDiePlacements = $placementFinder->getAllValidDiePlacementsForDie($die, $board);

            if (empty($validDiePlacements)) {
                continue;
            }

            $diePlacement = $validDiePlacements[array_rand($validDiePlacements)];
            $placementManager->putDiePlacementOnBoard($diePlacement, $board);
        }

        return new GameResults($gameState->getBoard(), $this->scoreGame($gameState));
    }

    // XXX TODO: THIS

    /**
     * @param PlayerGameState $gameState
     * @return Score
     * @throws \Exception
     */
    protected function scoreGame(PlayerGameState $gameState): Score
    {
        $scorerFactory = new FromSagradaScoreCardFactory();
        $boardScorer = $scorerFactory->createFromScoreCardCollection($this->getGame()->getScoreCards(), $gameState->getBoard());
        return new Score($boardScorer->getScore());
    }

    /**
     * @return GameRunner
     */
    public function getGame(): GameRunner
    {
        return $this->game;
    }
}
