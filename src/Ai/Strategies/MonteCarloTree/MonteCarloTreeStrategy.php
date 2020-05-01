<?php
declare(strict_types=1);

namespace Sagrada\Ai\Strategies\MonteCarloTree;

use Sagrada\Ai\Simulations\GameSimulator;
use Sagrada\Ai\Strategies\MonteCarloTree\Tree\Node;
use Sagrada\Ai\Strategies\MonteCarloTree\Tree\Tree;
use Sagrada\Ai\Strategies\StrategyInterface;
use Sagrada\Board\Board;
use Sagrada\Dice\Color\DiceColorInterface;
use Sagrada\Dice\SagradaDie;
use Sagrada\DiePlacement;
use Sagrada\DiePlacement\Finder;
use Sagrada\DiePlacement\BoardPlacer;
use Sagrada\Game\GameResults;
use Sagrada\Game\PlayerGameState;
use Sagrada\Game\Score;
use Sagrada\DiePlacement\IllegalBoardPlacementException;
use Sagrada\DiePlacement\Validator;

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

    /**
     * @param PlayerGameState $gameState
     * @param SagradaDie $die
     * @return DiePlacement
     * @throws \Exception
     */
    public function getBestDiePlacement(SagradaDie $die, PlayerGameState $gameState): ?DiePlacement
    {
        $tree = $this->createTreeFromGameState($gameState);
        $endTime = time() + 12;
        $rootNode = $tree->getRootNode();

        while (time() < $endTime) {
            $node = $this->selectPromisingNode($rootNode);
            $nodeToExplore = $node;

            if (empty($node->getChildArray())) {
                $this->expandNode($node, $die);
            }

            $childNodes = $node->getChildArray();

            if (!empty($childNodes)) {
                $nodeToExplore = $childNodes[array_rand($childNodes)];
            }

            $gameResult = $this->gameSimulator->simulateRandomPlayout($nodeToExplore->getData()->getGameState());
            $this->backPropagateNodeData($nodeToExplore, $gameResult->getScore());
        }

        $this->uct->debugChildNodes($rootNode);

        $bestNode = $this->getChildWithMaxScore($rootNode);

        if ($bestNode) {
            return $bestNode->getData()->getLastDiePlacement();
        } else {
            return null;
        }
    }

    protected function createTreeFromGameState(PlayerGameState $gameState): Tree
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
     * Adds child nodes to the node, which represent every potential play (dice roll + possible placements of that roll).
     *
     * @param Node $node
     * @param SagradaDie $matchingDie
     * @throws \Exception
     */
    protected function expandNode(Node $node, SagradaDie $matchingDie): void
    {
        $gameState = $node->getData()->getGameState();

        $placementValidator = new Validator();
        $placementFinder = new Finder($placementValidator);
        $placementManager = new BoardPlacer($placementValidator);

        $gameStateCopy = $gameState->deepCopy();
        $gameStateCopy->decrementTurnsRemaining();
        $gameStateCopy->getDiceBag()->removeOneDieOfColor($matchingDie->getColor());

        $placements = $placementFinder->getAllValidDiePlacementsForDie($matchingDie, $gameStateCopy->getBoard());

        foreach ($placements as $placement) {
            $gameStateCopyCopy = $gameStateCopy->deepCopy();
            $placementManager->putDiePlacementOnBoard($placement, $gameStateCopyCopy->getBoard());
            $newState = new NodeData($gameStateCopyCopy, $placement);
            $node->addNodeToChildArray(new Node($newState));
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
}
