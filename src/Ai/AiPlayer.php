<?php
declare(strict_types=1);

namespace Sagrada\Ai;

use Sagrada\Ai\Strategies\StrategyInterface;
use Sagrada\Dice\SagradaDie;
use Sagrada\DieCollection;
use Sagrada\Game;

class AiPlayer
{
    /** @var StrategyInterface */
    protected $evaluationStrategy;
    /** @var Game */
    protected $game;

    public function __construct(StrategyInterface $evaluationStrategy, Game $game)
    {
        $this->evaluationStrategy = $evaluationStrategy;
        $this->game = $game;
    }

    public function takeTurn(Game\State $gameState): void
    {
        echo "AI Evaluating...\n";
        $bestDiePlacement = $this->getEvaluationStrategy()->getBestDiePlacement($gameState);
        if ($bestDiePlacement) {
            $this->getGame()->getPlacementPlacer()->putDiePlacementOnBoard($bestDiePlacement, $gameState->getPlayerState()->getBoard());
        }
    }

    /**
     * @return StrategyInterface
     */
    public function getEvaluationStrategy(): StrategyInterface
    {
        return $this->evaluationStrategy;
    }

    /**
     * @return Game
     */
    public function getGame(): Game
    {
        return $this->game;
    }
}
