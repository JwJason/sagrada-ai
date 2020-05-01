<?php
declare(strict_types=1);

namespace Sagrada\Ai\Strategies\MonteCarlo;

use function DeepCopy\deep_copy;
use Sagrada\DiePlacement\Finder;
use Sagrada\Game\PlayerGameState;
use Sagrada\Game\GameResults;
use Sagrada\DiePlacement;
use Sagrada\Game\Score;
use Sagrada\Scoring\BoardScorer;
use Sagrada\Scoring\Scorers\RowColorVariety;
use Sagrada\Scoring\Scorers\ColumnColorVariety;

// TODO: we don't want the simulator to play a combination of moves that were already played

/**
 * Class MonteCarloSimulator
 * @package Sagrada\Ai\Strategies\MonteCarlo
 */
class MonteCarloSimulator
{
    /**
     * @var Finder
     */
    protected $placementFinder;

    /**
     * MonteCarloSimulator constructor.
     * @param Finder $placementFinder
     */
    public function __construct(Finder $placementFinder)
    {
        $this->placementFinder = $placementFinder;
    }

    /**
     * @param DiePlacement $initialDiePlacement
     * @param PlayerGameState $gameState
     * @return GameResults
     * @throws \Exception
     */
    public function playSimulatedGameWithInitialDiePlacement(
        DiePlacement $initialDiePlacement,
        PlayerGameState $gameState
    ) : GameResults {
        $this->debug('===== Running new simulation =====');

        $gameStateCopy = deep_copy($gameState);
        $this->playMove($initialDiePlacement, $gameStateCopy);
        while ($gameStateCopy->hasTurnsRemaining() && $gameStateCopy->hasAnyPossibleMovesRemaining()) {
            $this->playTurn($gameStateCopy);
        }

        $board = $gameStateCopy->getBoard();
        $scorer = new BoardScorer([
            new RowColorVariety\Scorer($board),
            new ColumnColorVariety\Scorer($board)
        ]);

        return new GameResults($gameStateCopy->getBoard(), new Score($scorer->getScore()));
    }

    /**
     * @param DiePlacement $diePlacement
     * @param PlayerGameState $gameState
     * @throws \Exception
     */
    protected function playMove(DiePlacement $diePlacement, PlayerGameState $gameState)
    {
        $gameState->placeDieOnBoardSpace($diePlacement);
    }

    /**
     * @param PlayerGameState $gameState
     * @return PlayerGameState
     * @throws \Exception
     */
    protected function playTurn(PlayerGameState $gameState) : PlayerGameState
    {
        $gameState->decrementTurnsRemaining();

        $placementFinder = $this->placementFinder;
        $board = $gameState->getBoard();
        $die = $gameState->getDiceBag()->drawDie();

        $validDiePlacements = $placementFinder->getAllValidDiePlacementsForDie($die, $board);

        if (empty($validDiePlacements)) {
            $this->debug('(No valid move; passing)');
            return $gameState;
        }

        $diePlacement = $validDiePlacements[array_rand($validDiePlacements)];

        $this->debug('Placing die');

        $this->playMove($diePlacement, $gameState);

        return $gameState;
    }

    protected function debug(string $message): void
    {
        echo sprintf('SIMULATOR: %s' . PHP_EOL, $message);
    }
}
