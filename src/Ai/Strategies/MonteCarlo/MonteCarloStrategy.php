<?php
declare(strict_types=1);

namespace Sagrada\Ai\Strategies\MonteCarlo;

use Sagrada\Ai\NoAvailableMoveException;
use Sagrada\Ai\Strategies\StrategyInterface;
use Sagrada\Dice\SagradaDie;
use Sagrada\DiePlacement;
use Sagrada\DiePlacementFinder;
use Sagrada\Game\PlayerGameState;

/**
 * Class MonteCarloStrategy
 * @package Sagrada\Ai\Strategies\MonteCarlo
 */
class MonteCarloStrategy implements StrategyInterface
{
    /**
     * @var DiePlacementFinder
     */
    protected $diePlacementFinder;
    /**
     * @var MonteCarloSimulator
     */
    protected $simulator;

    /**
     * MonteCarloStrategy constructor.
     * @param DiePlacementFinder $diePlacementFinder
     * @param MonteCarloSimulator $simulator
     */
    public function __construct(DiePlacementFinder $diePlacementFinder, MonteCarloSimulator $simulator)
    {
        $this->diePlacementFinder = $diePlacementFinder;
        $this->simulator = $simulator;
    }

    /**
     * @param SagradaDie $die
     * @param PlayerGameState $gameState
     * @return DiePlacement
     * @throws \Exception
     */
    public function getBestDiePlacement(SagradaDie $die, PlayerGameState $gameState): DiePlacement
    {
        $diePlacementFinder = $this->diePlacementFinder;
        $board = $gameState->getBoard();
        $results = [];

        $validDiePlacements = $diePlacementFinder->getAllValidDiePlacementsForDie($die, $board);

        if (empty($validDiePlacements)) {
            throw new NoAvailableMoveException('No valid die placements were found.');
        }

        foreach ($validDiePlacements as $initialDiePlacement) {
            $score = $this->playSimulatedGamesWithInitialDiePlacement(
                $initialDiePlacement,
                $gameState,
                1000
            );
            $result = ['diePlacement' => $initialDiePlacement, 'score' => $score];
            $results[] = $result;
        }

        usort($results, function($result1, $result2) {
            return $result2['score'] <=> $result1['score'];
        });

        return $results[0]['diePlacement'];
    }

    /**
     * @param DiePlacement $initialDiePlacement
     * @param PlayerGameState $gameState
     * @param int $numberOfGames
     * @return float
     * @throws \Exception
     */
    protected function playSimulatedGamesWithInitialDiePlacement(
        DiePlacement $initialDiePlacement,
        PlayerGameState $gameState,
        int $numberOfGames
    ) : float {
        $simulator = $this->simulator;
        $scores = [];

        for ($i = 0; $i < $numberOfGames; $i++) {
            $gameResult = $simulator->playSimulatedGameWithInitialDiePlacement($initialDiePlacement, $gameState);
            $scores[] = $gameResult->getScore()->getTotal();
        }

        return $this->scoreSimulationResults($scores);
    }

    protected function scoreSimulationResults(array $resultScores): float
    {
        return array_sum($resultScores) / count($resultScores);
    }
}
