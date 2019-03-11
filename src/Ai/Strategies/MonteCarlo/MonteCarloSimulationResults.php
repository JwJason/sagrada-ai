<?php
declare(strict_types=1);

namespace Sagrada\Ai\Strategies\MonteCarlo;

use Sagrada\DiePlacement;
use Sagrada\Game\GameResults;

/**
 * Class MonteCarloSimulationResults
 * @package Sagrada\Ai\Strategies\MonteCarlo
 */
class MonteCarloSimulationResults
{
    /**
     * @var DiePlacement
     */
    protected $initialDiePlacement;
    /**
     * @var GameResults
     */
    protected $gameResults;

    /**
     * MonteCarloSimulationResults constructor.
     * @param DiePlacement $initialDiePlacement
     * @param GameResults $gameResults
     */
    public function __construct(DiePlacement $initialDiePlacement, GameResults $gameResults)
    {
        $this->initialDiePlacement = $initialDiePlacement;
        $this->gameResults = $gameResults;
    }
}
