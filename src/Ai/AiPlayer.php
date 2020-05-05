<?php
declare(strict_types=1);

namespace Sagrada\Ai;

use Sagrada\Ai\Simulations\GameSimulator;
use Sagrada\Ai\Strategies\StrategyInterface;
use Sagrada\Game;

class AiPlayer
{
    /** @var StrategyInterface */
    protected $evaluationStrategy;
    /** @var GameSimulator */
    protected $gameSimulator;

    public function __construct(StrategyInterface $evaluationStrategy, GameSimulator $gameSimulator)
    {
        $this->evaluationStrategy = $evaluationStrategy;
        $this->gameSimulator = $gameSimulator;
    }

    public function takeTurn(Game\State $gameState): void
    {
        echo "AI Evaluating...\n";
        $turn = $this->getEvaluationStrategy()->getBestTurn($gameState);
        $this->getGameSimulator()->simulateTurn($gameState, $turn);
    }

    /**
     * @return StrategyInterface
     */
    public function getEvaluationStrategy(): StrategyInterface
    {
        return $this->evaluationStrategy;
    }

    /**
     * @return GameSimulator
     */
    public function getGameSimulator(): GameSimulator
    {
        return $this->gameSimulator;
    }
}
