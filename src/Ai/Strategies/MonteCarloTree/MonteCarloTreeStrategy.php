<?php
declare(strict_types=1);

namespace Sagrada\Ai\Strategies\MonteCarloTree;

use Sagrada\Ai\Simulations\GameSimulator;
use Sagrada\Ai\Strategies\MonteCarloTree\Tree\Node;
use Sagrada\Ai\Strategies\MonteCarloTree\Tree\Tree;
use Sagrada\Ai\Strategies\StrategyInterface;
use Sagrada\Dice\SagradaDie;
use Sagrada\DieCollection;
use Sagrada\DiePlacement;
use Sagrada\Game;
use Sagrada\Game\PlayerState;
use Sagrada\Game\Score;

class MonteCarloTreeStrategy implements StrategyInterface
{
    /**
     * @var GameSimulator
     */
    protected $gameSimulator;

    /**
     * @var Uct
     */
    protected $uct;

    public function __construct(GameSimulator $gameSimulator, Uct $uct)
    {
        $this->gameSimulator = $gameSimulator;
        $this->uct = $uct;
    }

    public function getBestDiePlacement(Game\State $gameState): ?DiePlacement
    {
        $tree = $this->createTreeFromGameState($gameState);
        $endTime = time() + 12;
        $rootNode = $tree->getRootNode();
        // DEBUG
        for ($i = 0; $i < 20000; $i++) {
//        while (time() < $endTime) {
            $node = $this->selectPromisingNode($rootNode);
            $nodeToExplore = $node;

            if (empty($node->getChildArray())) {
                $this->expandNode($node);
            }

            $childNodes = $node->getChildArray();

            if (!empty($childNodes)) {
                $nodeToExplore = $childNodes[array_rand($childNodes)];
            }

            $gameResult = $this->gameSimulator->simulateRandomPlayout($nodeToExplore->getData()->getGameState());
            $this->backPropagateNodeData($nodeToExplore, $gameResult->getScore());
        }

        $this->debugChildNodes($rootNode);

        $bestNode = $this->getChildWithMaxScore($rootNode);

        if (!$bestNode) {
            return null;
        }
        return $bestNode->getData()->getLastDiePlacement();
    }

//    public function getBestDiePlacement(SagradaDie $die, Game\State $gameState): ?DiePlacement
//    {
//        $tree = $this->createTreeFromGameState($gameState);
//        $endTime = time() + 12;
//        $rootNode = $tree->getRootNode();
//
//        // DEBUG
//        for ($i = 0; $i < 20000; $i++) {
////        while (time() < $endTime) {
//            $node = $this->selectPromisingNode($rootNode);
//            $nodeToExplore = $node;
//
//            if (empty($node->getChildArray())) {
//                $this->expandNode($node, $die);
//            }
//
//            $childNodes = $node->getChildArray();
//
//            if (!empty($childNodes)) {
//                $nodeToExplore = $childNodes[array_rand($childNodes)];
//            }
//
//            $gameResult = $this->gameSimulator->simulateRandomPlayout($nodeToExplore->getData()->getGameState());
//            $this->backPropagateNodeData($nodeToExplore, $gameResult->getScore());
//        }
//
//        $this->debugChildNodes($rootNode);
//
//        $bestNode = $this->getChildWithMaxScore($rootNode);
//
//        if (!$bestNode) {
//            return null;
//        }
//        return $bestNode->getData()->getLastDiePlacement();
//    }

    public function debugChildNodes(Node $startingNode): void
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

    protected function createTreeFromGameState(Game\State $gameState): Tree
    {
        return new Tree(new Node(new NodeData($gameState, null)));
    }

    protected function getChildWithMaxScore(Node $startingNode): ?Node
    {
        $children = $startingNode->getChildArray();
        $max = 0;
        $bestNode = null;

        foreach ($children as $childNode) {
            $score = $childNode->getData()->getAggregateScore() / $childNode->getData()->getVisitCount();
            if ($score > $max) {
                $max = $score;
                $bestNode = $childNode;
            }
        }

        return $bestNode;
    }

    /**
     * @param Node $startingNode
     * @return Node
     */
    protected function selectPromisingNode(Node $startingNode): Node
    {
        $node = $startingNode;
        while (count($node->getChildArray()) > 0) {
            $bestNode = $this->uct->findBestNodeWithUct($node);
            if ($bestNode) {
                $node = $bestNode;
            } else {
                return $node;
            }
        }
        return $node;
    }

    /**
     * Adds child nodes to the node, which represents every potential play from the dice draft pool.
     *
     * @param Node $node
     * @throws \Exception
     */
    protected function expandNode(Node $node): void
    {
        $gameState = $node->getData()->getGameState();
        $game = $gameState->getGame();

        $placementFinder = $game->getPlacementFinder();

        $placements = $placementFinder->getAllValidDiePlacementsForDieCollection(
            $gameState->getDraftPool(),
            $gameState->getCurrentPlayer()->getState()->getBoard()
        );

        /** @var DiePlacement $placement */
        foreach ($placements as $placement) {
            $newGameState = $this->getGameSimulator()->simulateTurn($gameState, $placement);
            $nodeData = new NodeData($newGameState, $placement);
            $node->addNodeToChildArray(new Node($nodeData));
        }
    }

    /**
     * @param Node $initialNode
     * @param Score $score
     */
    protected function backPropagateNodeData(Node $initialNode, Score $score): void
    {
        $node = $initialNode;
        while ($node !== null) {
            $node->getData()->incrementVisitCount();
            $node->getData()->increaseAggregateScore($score->getTotal());
            $node = $node->getParent();
        }
    }

    /**
     * @return GameSimulator
     */
    public function getGameSimulator(): GameSimulator
    {
        return $this->gameSimulator;
    }

    /**
     * @return Uct
     */
    public function getUct(): Uct
    {
        return $this->uct;
    }
}
