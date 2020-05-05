<?php
declare(strict_types=1);

namespace Sagrada\Ai\Strategies\MonteCarloTreeStrategy\Tree;

use Sagrada\Game;

class GameStateNode extends Node
{
    /** @var Game\State */
    protected $gameState;

    /**
     * @return Game\State
     */
    public function getGameState(): Game\State
    {
        return $this->gameState;
    }

    /**
     * @param Game\State $gameState
     */
    public function setGameState(Game\State $gameState): void
    {
        $this->gameState = $gameState;
    }
}
