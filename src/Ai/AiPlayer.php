<?php
declare(strict_types=1);

namespace Sagrada\Ai;

use Sagrada\Ai\Strategies\StrategyInterface;
use Sagrada\Dice\SagradaDie;
use Sagrada\Game\PlayerGameState;

class AiPlayer
{
    protected $evaluationStrategy;

    public function __construct(StrategyInterface $evaluationStrategy)
    {
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
        try {
            $bestDiePlacement = $this->evaluationStrategy->getBestDiePlacement($die, $gameState);
        } catch (NoAvailableMoveException $e) {
            echo $e->getMessage() . "\n";
            return;
        }
        $gameState->placeDieOnBoardSpace($bestDiePlacement);
    }
}
