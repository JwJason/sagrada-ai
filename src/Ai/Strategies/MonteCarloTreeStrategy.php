<?php
declare(strict_types=1);

namespace Sagrada\Ai\Strategies;

use Sagrada\Ai\Simulations\GameSimulator;
use Sagrada\Ai\Strategies\MonteCarloTreeStrategy\Helper;
use Sagrada\Ai\Strategies\MonteCarloTreeStrategy\Tree;
use Sagrada\Ai\Strategies\MonteCarloTreeStrategy\Tree\Node;
use Sagrada\Ai\Strategies\MonteCarloTreeStrategy\Uct;
use Sagrada\Game;
use Sagrada\Game\Score;
use Sagrada\Player\SagradaPlayer;
use Sagrada\Turn;

class MonteCarloTreeStrategy implements StrategyInterface
{
    public const MAX_TREE_DEPTH = 20;
    public const MINIMUM_VISITS_PER_NODE = 50;

    /** @var GameSimulator */
    protected $gameSimulator;

    /** @var Helper */
    protected $helper;

    /** @var Uct */
    protected $uct;

    public function __construct(GameSimulator $gameSimulator, Uct $uct)
    {
        $this->gameSimulator = $gameSimulator;
        $this->uct = $uct;
        $this->helper = new Helper($this->gameSimulator);
    }

    public function getBestTurn(Game\State $gameState): Turn
    {
        $totalSimulations = 0;
        $tree = $this->createTreeFromGameState($gameState);
        $endTime = time() + 30;
        $rootNode = $tree->getRootNode();
        $myPlayerIndex = $gameState->getGame()->getPlayerIndex($gameState->getCurrentPlayer());
        $myCurrentRound = $gameState->getCurrentRound();
        
        while (time() < $endTime) {
            $node = $this->selectPromisingNode($rootNode);
            $nodeToExplore = $node;

            if ($node->hasBeenPruned() === false) {
                $this->pruneNode($node);
            }

            if (empty($node->getChildren()) && ($node->getDepth() < self::MAX_TREE_DEPTH)) {
                $this->expandNode($node, $myCurrentRound);
            }

            $childNodes = $node->getChildren();

            if (!empty($childNodes)) {
                $nodeToExplore = $childNodes[array_rand($childNodes)];
            }

            $nodeGameState = $this->getHelper()->getGameStateFromNode($nodeToExplore);

            $simulatedGameState = $this->gameSimulator->simulateRandomPlayout($nodeGameState);
            /** @var SagradaPlayer $myPlayer */
            $myPlayer = $simulatedGameState->getGame()->getPlayers()[$myPlayerIndex];
            $this->backPropagateNodeData($nodeToExplore, $myPlayer->getState()->getScore());

            $totalSimulations++;
        }

        echo sprintf("TOTAL SIMULATIONS: %d\n", $totalSimulations);
        $this->debugChildNodes($rootNode);

        $bestNode = $this->getChildWithMaxScore($rootNode);

        if (!$bestNode) {
            return null;
        }

        $bestNodeGameState = $this->getHelper()->getGameStateFromNode($bestNode);
        /** @var SagradaPlayer $myPlayer */
        $myPlayer = $bestNodeGameState->getGame()->getPlayers()[$myPlayerIndex];
        return $myPlayer->getState()->getTurnHistory()->last();
    }

    public function debugChildNodes(Node $startingNode): void
    {
        foreach ($startingNode->getChildren() as $node) {
            // TODO - Remove hard-coded assumption that AI player is player 1
            /** @var SagradaPlayer $player */
            $player = $this->getHelper()->getGameStateFromNode($node)->getGame()->getPlayers()[0];
            $turn = $player->getState()->getTurnHistory()->last();

            if ($node->getVisitCount() > 0) {
                echo sprintf(
                    "Play=%s|AggregateScore=%f|AvgScore=%f|Visits=%d\n",
                    $turn,
                    $node->getAggregateScore(),
                    $node->getAggregateScore() / $node->getVisitCount(),
                    $node->getVisitCount()
                );
            } else {
                echo sprintf("Play=%s|UNVISITED\n", $turn);
            }
        }
    }

    protected function createTreeFromGameState(Game\State $gameState): Tree
    {
        $gameStateNode = new Tree\GameStateNode();
        $gameStateNode->setGameState($gameState);
        return new Tree($gameStateNode);
    }

    protected function getChildWithMaxScore(Node $startingNode): ?Node
    {
        $children = $startingNode->getChildren();
        $max = 0;
        $bestNode = null;

        foreach ($children as $childNode) {
            if ($childNode->getVisitCount() === 0) {
                continue;
            }
            $score = $childNode->getAggregateScore() / $childNode->getVisitCount();
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

    protected function expandNode(Node $node, int $myCurrentRound): void
    {
        // Limit construction of GameStateNodes to simulating the current round only.
        // GameStateNodes are only useful in the current round, when we know the state of the draft pool
        if ($node instanceof Tree\GameStateNode) {
            if ($node->getGameState()->getCurrentRound() === $myCurrentRound) {
                $this->getHelper()->expandGameStateNode($node);
            } else {
                $this->getHelper()->expandDiePlacementNode($node);
            }
        } else if ($node instanceof Tree\TurnNode) {
            $this->getHelper()->expandDiePlacementNode($node);
        } else {
            throw new \LogicException(sprintf('Unhandled node instance type: %s', get_class($node)));
        }
    }

    protected function pruneNode(Node $node): void
    {
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
            $childAverageSum += ($childNode->getAggregateScore() / $childNode->getVisitCount());
        }

        $childAverageMean = $childAverageSum / $numberOfChildren;

        /** @var Node $childNode */
        foreach ($children as $key => $childNode) {
             $childMean = $childNode->getAggregateScore() / $childNode->getVisitCount();
             if ($childMean < $childAverageMean) {
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
            if ($node->hasBeenPruned() === false) {
                $this->pruneNode($node);
            }
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
}
