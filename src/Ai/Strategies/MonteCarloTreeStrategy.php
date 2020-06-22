<?php
declare(strict_types=1);

namespace Sagrada\Ai\Strategies;

use Sagrada\Ai\GameSimulator;
use Sagrada\Ai\Strategies\MonteCarloTreeStrategy\Helper;
use Sagrada\Ai\Strategies\MonteCarloTreeStrategy\Tree;
use Sagrada\Ai\Strategies\MonteCarloTreeStrategy\Tree\Node;
use Sagrada\Ai\Strategies\MonteCarloTreeStrategy\Uct;
use Sagrada\Game;
use Sagrada\Game\Score;
use Sagrada\Turn;

class MonteCarloTreeStrategy implements StrategyInterface
{
    public const MAX_TREE_DEPTH = 20;
    public const MAX_TREE_VISIT_TIME = 30;
    public const MINIMUM_VISITS_PER_NODE = 50;

    /** @var GameSimulator */
    protected $gameSimulator;

    /** @var Helper */
    protected $helper;

    /** @var bool */
    protected $pruneNodes;

    /** @var Uct */
    protected $uct;

    public function __construct(GameSimulator $gameSimulator, Uct $uct, bool $pruneNodes=true)
    {
        $this->gameSimulator = $gameSimulator;
        $this->uct = $uct;
        $this->pruneNodes = $pruneNodes;
        $this->helper = new Helper($this->gameSimulator);
    }

    /**
     * Return the best possible move for the current game state, using the Monte Carlo Tree Search algorithm.
     * The algorithm works by simulating a bunch of random game playouts for each possible move and then identifying the
     * move with the highest average player final score.
     *
     * As the algorithm runs, it progressively builds a tree-node structure representing chains of possible moves (dice plays).
     * The algorithm traverses the tree, looking for unvisited nodes or revisiting potentially good nodes.
     * @param Game\State $gameState
     * @return Turn
     */
    public function getBestTurn(Game\State $gameState): Turn
    {
        $tree = $this->createTreeFromGameState($gameState);
        $endTime = time() + self::MAX_TREE_VISIT_TIME;
        $rootNode = $tree->getRootNode();
        $myPlayerIndex = $gameState->getGame()->getPlayerIndex($gameState->getCurrentPlayer());
        $myCurrentRound = $gameState->getCurrentRound();

        while (time() < $endTime) {
            $node = $this->selectPromisingNode($rootNode);
            $nodeToExplore = $node;

            $this->expandNode($node, $myCurrentRound);

            $childNodes = $node->getChildren();
            if (!empty($childNodes)) {
                $nodeToExplore = $childNodes[array_rand($childNodes)];
            }

            $nodeGameState = $this->getHelper()->getGameStateFromNode($nodeToExplore);
            $simulatedGameState = $this->gameSimulator->simulateRandomPlayout($nodeGameState);
            $myPlayer = $simulatedGameState->getGame()->getPlayer($myPlayerIndex);

            $this->backPropagateNodeData($nodeToExplore, $myPlayer->getState()->getScore());
        }

        $bestNode = $this->getChildWithMaxScore($rootNode);

        if (!$bestNode) {
            throw new \RuntimeException('No best turn found');
        }

        $bestNodeGameState = $this->getHelper()->getGameStateFromNode($bestNode);
        $myPlayer = $bestNodeGameState->getGame()->getPlayer($myPlayerIndex);
        return $myPlayer->getState()->getTurnHistory()->last();
    }

    /**
     * @param Game\State $gameState
     * @return Tree
     */
    protected function createTreeFromGameState(Game\State $gameState): Tree
    {
        $gameStateNode = new Tree\GameStateNode();
        $gameStateNode->setGameState($gameState);
        return new Tree($gameStateNode);
    }

    /**
     * Find the child node with the highest average score.
     * @param Node $startingNode
     * @return Node | null
     */
    protected function getChildWithMaxScore(Node $startingNode): ?Node
    {
        $children = $startingNode->getChildren();
        $max = 0;
        $bestNode = null;

        foreach ($children as $childNode) {
            if ($childNode->getVisitCount() === 0) {
                continue;
            }
            $score = $childNode->getAverageScore();
            if ($score > $max) {
                $max = $score;
                $bestNode = $childNode;
            }
        }

        return $bestNode;
    }

    /**
     * Select a node with good play potential, using the UCT algorithm (https://www.chessprogramming.org/UCT)
     * @param Node $startingNode
     * @return Node
     */
    protected function selectPromisingNode(Node $startingNode): Node
    {
        $node = $startingNode;
        while (count($node->getChildren()) > 0) {
            $bestNode = $this->getUct()->findBestNodeWithUct($node);
            if ($bestNode) {
                $node = $bestNode;
            } else {
                return $node;
            }
        }
        return $node;
    }

    /**
     * Expand to the node (i.e. add child nodes if they don't already exist)
     * @param Node $node
     * @param int $myCurrentRound
     */
    protected function expandNode(Node $node, int $myCurrentRound): void
    {
        if (!empty($node->getChildren()) || ($node->getDepth() > self::MAX_TREE_DEPTH)) {
            return;
        }

        $helper = $this->getHelper();

        if ($node instanceof Tree\GameStateNode) {
            // Limit construction of GameStateNodes to the current round only. Use TurnNodes to represent subsequent rounds.
            // GameStateNodes are only useful in the current round, when we know the state of the dice pool.
            if ($node->getGameState()->getCurrentRound() === $myCurrentRound) {
                $helper->expandGameStateNode($node);
            } else {
                $helper->expandNode($node);
            }
        } else if ($node instanceof Tree\TurnNode) {
            $helper->expandNode($node);
        } else {
            throw new \LogicException(sprintf('Unhandled node instance type: %s', get_class($node)));
        }
    }

    /**
     * Removes child nodes that are deemed to be bad.
     * Bad nodes have a lower than average play score compared to their sibling nodes.
     * Pruning only happens once per node.
     * @param Node $node
     */
    protected function pruneNode(Node $node): void
    {
        if ($this->doesPruneNodes() === false || $node->hasBeenPruned() === true) {
            return;
        }

        $children = $node->getChildren();
        $numberOfChildren = count($children);
        $childAverageSum = 0;

        if ($numberOfChildren === 0) {
            return;
        }

        /** @var Node $childNode */
        foreach ($children as $childNode) {
            if ($childNode->getVisitCount() < self::MINIMUM_VISITS_PER_NODE) {
                return;
            }
            $childAverageSum += $childNode->getAverageScore();
        }

        $childAverageMean = $childAverageSum / $numberOfChildren;

        /** @var Node $childNode */
        foreach ($children as $key => $childNode) {
             if ($childNode->getAverageScore() < $childAverageMean) {
                 unset($children[$key]);
             }
        }

        $node->setChildren($children);
        $node->setHasBeenPruned(true);
    }

    /**
     * @param Node $initialNode
     * @param Score $score
     */
    protected function backPropagateNodeData(Node $initialNode, Score $score): void
    {
        $node = $initialNode;
        while ($node !== null) {
            $node->incrementVisitCount();
            $node->increaseAggregateScore($score->getTotal());
            $this->pruneNode($node);
            $node = $node->getParent();
        }
    }

    /** @return GameSimulator */
    public function getGameSimulator(): GameSimulator
    {
        return $this->gameSimulator;
    }

    /** @return Uct */
    public function getUct(): Uct
    {
        return $this->uct;
    }

    /** @return Helper */
    public function getHelper(): Helper
    {
        return $this->helper;
    }

    /** @return bool */
    public function doesPruneNodes(): bool
    {
        return $this->pruneNodes;
    }
}
