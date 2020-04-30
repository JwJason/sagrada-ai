<?php
declare(strict_types=1);

namespace Sagrada\Ai\Simulations;

use Sagrada\DiePlacementFinder;
use Sagrada\DiePlacementManager;
use Sagrada\Game\GameResults;
use Sagrada\Game\PlayerGameState;
use Sagrada\Game\Score;
use Sagrada\Scoring\BoardScorer;
use Sagrada\Validators\DiePlacementValidator;
use Sagrada\Scoring\Scorers\RowColorVariety;
use Sagrada\Scoring\Scorers\ColumnColorVariety;

/**
 * Class GameSimulator
 * @package Sagrada\Ai\Simulations
 */
class GameSimulator
{
    /**
     * @var DiePlacementFinder
     */
    protected $placementFinder;
    /**
     * @var DiePlacementManager
     */
    protected $placementManager;
    /**
     * @var DiePlacementValidator
     */
    protected $placementValidator;

    /**
     * GameSimulator constructor.
     */
    public function __construct()
    {
        $this->placementValidator = new DiePlacementValidator();
        $this->placementManager = new DiePlacementManager($this->placementValidator);
        $this->placementFinder = new DiePlacementFinder($this->placementValidator);
    }

    /**
     * @param PlayerGameState $initialGameState
     * @return GameResults
     * @throws \Sagrada\IllegalBoardPlacementException
     * @throws \Exception
     */
    public function simulateRandomPlayout(PlayerGameState $initialGameState): GameResults
    {
        $gameState = $initialGameState->deepCopy();
        $placementFinder = $this->placementFinder;
        $placementManager = $this->placementManager;

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
        $board = $gameState->getBoard();

        $scorer = new BoardScorer([
            new RowColorVariety\Scorer($board),
            new ColumnColorVariety\Scorer($board)
        ]);

        return new Score($scorer->getScore());
    }
}
