<?php
declare(strict_types=1);

namespace Sagrada\Ai\Strategies;

use Sagrada\Dice\SagradaDie;
use Sagrada\DiePlacement;
use Sagrada\Game\PlayerGameState;

/**
 * Interface StrategyInterface
 * @package Sagrada\Ai\Strategies
 */
interface StrategyInterface
{
    /**
     * @param SagradaDie $die
     * @param PlayerGameState $gameState
     * @return DiePlacement
     */
    public function getBestDiePlacement(SagradaDie $die, PlayerGameState $gameState): DiePlacement;
}
