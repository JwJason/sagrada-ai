<?php
declare(strict_types=1);

namespace Sagrada\Ai\Strategies\MonteCarloTree;

use Sagrada\Ai\Strategies\MonteCarloTree\Tree\Node;

class Uct
{
    public function debugChildNodes(Node $startingNode)
    {
        foreach ($startingNode->getChildArray() as $node) {
            echo sprintf(
                "Play=%s|AggregateScore=%f|AvgScore=%f|Visits=%d\n",
                $node->getData()->getLastDiePlacement(),
                $node->getData()->getAggregateScore(),
                $node->getData()->getAggregateScore() / $node->getData()->getVisitCount(),
                $node->getData()->getVisitCount()
            );
        }
    }

    public function findBestNodeWithUct(Node $startingNode): ?Node
    {
        $parentVisitCount = $startingNode->getData()->getVisitCount();
        $max = 0;
        $bestNode = null;

        foreach ($startingNode->getChildArray() as $node) {
            $uctValue = $this->getUctValue(
                $parentVisitCount,
                $node->getData()->getVisitCount(),
                $node->getData()->getAggregateScore()
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
        if ($nodeVisitCount === 0) {
            return PHP_INT_MAX;
        }
        return ((float)$totalScore / (double)$nodeVisitCount)
            + 1.41 * sqrt(log($parentVisitCount) / (float)$nodeVisitCount);
    }
}
