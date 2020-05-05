<?php
declare(strict_types=1);

namespace Sagrada\Ai\Strategies;

use Sagrada\Game;
use Sagrada\Turn;

/**
 * Interface StrategyInterface
 * @package Sagrada\Ai\Strategies
 */
interface StrategyInterface
{
    public function getBestTurn(Game\State $gameState): Turn;
}
