<?php
declare(strict_types=1);

namespace Sagrada\Ai;

use Sagrada\Ai\Strategies\StrategyInterface;
use Sagrada\Dice\SagradaDie;
use Sagrada\DiePlacement\BoardPlacer;
use Sagrada\Game\PlayerGameState;

class AiPlayer
{
    protected $diePlacementManager;
    protected $evaluationStrategy;

    public function __construct(StrategyInterface $evaluationStrategy, BoardPlacer $placementManager)
    {
        $this->diePlacementManager = $placementManager;
        $this->evaluationStrategy = $evaluationStrategy;
    }

    /**
     * @param SagradaDie $die
     * @param PlayerGameState $gameState
     * @throws \Exception
     */
    public function takeTurn(SagradaDie $die, PlayerGameState $gameState): void
    {
        $gameState->decrementTurnsRemaining();
        echo sprintf("Evaluating play for die %s\n", $die);
        $bestDiePlacement = $this->evaluationStrategy->getBestDiePlacement($die, $gameState);
        if ($bestDiePlacement) {
            $this->diePlacementManager->putDiePlacementOnBoard($bestDiePlacement, $gameState->getBoard());
        }
    }
}
