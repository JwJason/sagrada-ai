<?php
declare(strict_types=1);

namespace Sagrada\Ai\Strategies;

use Sagrada\DiePlacement;
use Sagrada\Game;

/**
 * Interface StrategyInterface
 * @package Sagrada\Ai\Strategies
 */
interface StrategyInterface
{
//    public function getBestDiePlacement(SagradaDie $die, Game\State $gameState): ?DiePlacement;
    public function getBestDiePlacement(Game\State $gameState): ?DiePlacement;
}
