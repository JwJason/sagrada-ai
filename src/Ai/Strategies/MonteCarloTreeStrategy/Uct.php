<?php
declare(strict_types=1);

namespace Sagrada\Ai\Strategies\MonteCarloTreeStrategy;

use Sagrada\Ai\Strategies\MonteCarloTreeStrategy;
use Sagrada\Ai\Strategies\MonteCarloTreeStrategy\Tree\Node;

class Uct
{
    public function findBestNodeWithUct(Node $startingNode): ?Node
    {
        $parentVisitCount = $startingNode->getVisitCount();
        $max = 0;
        $bestNode = null;

        foreach ($startingNode->getChildren() as $node) {
            $uctValue = $this->getUctValue(
                $parentVisitCount,
                $node->getVisitCount(),
                $node->getAggregateScore()
            );
            if ($uctValue > $max) {
                $max = $uctValue;
                $bestNode = $node;
            }
        }

        return $bestNode;
    }

    public function getUctValue(int $parentVisitCount, int $nodeVisitCount, float $totalScore): float
    {
        if ($nodeVisitCount < MonteCarloTreeStrategy::MINIMUM_VISITS_PER_NODE) {
            return PHP_INT_MAX;
        }
        return ($totalScore / (double)$nodeVisitCount)
            + 1.41 * sqrt(log($parentVisitCount) / (float)$nodeVisitCount);
    }
}
