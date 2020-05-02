<?php
declare(strict_types=1);

namespace Sagrada\Ai\Strategies\MonteCarloTree;

use Sagrada\DiePlacement;
use Sagrada\Game;

/**
 * Class NodeState
 *
 * @package Sagrada\Ai\Strategies\MonteCarloTree
 */
class NodeData
{
    /**
     * @var Game\State
     */
    protected $gameState;
    /**
     * @var DiePlacement|null
     */
    protected $lastDiePlacement;
    /**
     * @var int
     */
    protected $aggregateScore;
    /**
     * @var int
     */
    protected $visitCount;

    public function __construct(?DiePlacement $diePlacement)
    {
        $this->lastDiePlacement = $diePlacement;
        $this->visitCount = 0;
        $this->aggregateScore = 0;
    }

    /**
     * @return int
     */
    public function getVisitCount(): int
    {
        return $this->visitCount;
    }

    /**
     *
     */
    public function incrementVisitCount(): void
    {
        ++$this->visitCount;
    }

    /**
     * @return int
     */
    public function getAggregateScore(): int
    {
        return $this->aggregateScore;
    }

    /**
     * @param int $amount
     */
    public function increaseAggregateScore(int $amount): void
    {
        $this->aggregateScore += $amount;
    }
}
