<?php
declare(strict_types=1);

namespace Sagrada\Ai\Strategies\MonteCarloTreeStrategy;

use Sagrada\Ai\GameSimulator;
use Sagrada\Ai\Strategies\MonteCarloTreeStrategy\Tree\Node;
use Sagrada\DiePlacement;
use Sagrada\Game;
use Sagrada\Turn;

class Helper
{
    /** @var GameSimulator */
    protected $gameSimulator;

    public function __construct(GameSimulator $gameSimulator)
    {
        $this->gameSimulator = $gameSimulator;
    }

    /**
     * Expands node by adding GameStateNode children for every possible die placement on the current board from the current dice pool.
     * @param Tree\GameStateNode $node
     */
    public function expandGameStateNode(Tree\GameStateNode $node): void
    {
        $gameState = $node->getGameState();

        if ($gameState->gameIsCompleted() === true) {
            return;
        }

        $game = $gameState->getGame();
        $placementFinder = $game->getPlacementFinder();

        $placements = $placementFinder->getAllValidDiePlacementsForDieCollection(
            $gameState->getDraftPool(),
            $gameState->getCurrentPlayer()->getState()->getBoard()
        );

        try {
            if (count($placements) > 0) {
                foreach ($placements as $placement) {
                    $this->addChildNodeForDiePlacement($node, $gameState, $placement);
                }
            } else {
                $this->addChildNodeForPassTurn($node, $gameState);
            }
        } catch (\Throwable $t) {
            echo sprintf("expandGameNode() failed: %s\n", $t) .
                 sprintf("Game State:\n%s\n", $node->getGameState());
            die();
        }
    }

    /**
     * Expands node by adding children for every possible die placement on the current board.
     * @param Node $node
     */
    public function expandNode(Node $node): void
    {
        $gameState = $this->getGameStateFromNode($node);

        if ($gameState->gameIsCompleted() === true) {
            return;
        }

        $possibleDiceRolls = $gameState->getDiceBag()->getAllPossibleRemainingDiceRolls();
        $placementFinder = $gameState->getGame()->getPlacementFinder();
        $placements = $placementFinder->getAllValidDiePlacementsForDieCollection(
            $possibleDiceRolls,
            $gameState->getCurrentPlayer()->getState()->getBoard()
        );

        foreach ($placements as $placement) {
            $newDiePlacementNode = new Tree\TurnNode();
            $newDiePlacementNode->setTurn(new Turn\DiePlacement($placement));
            $node->addChild($newDiePlacementNode);
        }
    }

    public function addChildNodeForDiePlacement(Node $node, Game\State $gameState, DiePlacement $diePlacement): void
    {
        $turn = new Turn\DiePlacement($diePlacement);
        $newGameState = $gameState->deepCopy();
        $this->getGameSimulator()->simulateTurn($newGameState, $turn);
        $newGameStateNode = new Tree\GameStateNode;
        $newGameStateNode->setGameState($newGameState);
        $node->addChild($newGameStateNode);
    }

    public function addChildNodeForPassTurn(Node $node, Game\State $gameState): void
    {
        $newGameState = $gameState->deepCopy();
        $this->getGameSimulator()->simulateTurn($newGameState, new Turn\Pass());
        $newGameStateNode = new Tree\GameStateNode();
        $newGameStateNode->setGameState($newGameState);
        $node->addChild($newGameStateNode);
    }

    /**
     * Reconstructs game state by traversing the given node's parents and applying each node's game turn to the game
     * @param Tree\TurnNode $node
     * @return Game\State
     */
    public function reconstructGameStateFromTurnNode(Tree\TurnNode $node): Game\State
    {
        $gameState = $node->getLastKnownGameState()->deepCopy();
        $turnsSinceGameState = $node->getAllPrecedingTurns();

        try {
            /** @var Turn $turn */
            foreach ($turnsSinceGameState as $turn) {
                $this->getGameSimulator()->simulateTurn($gameState, $turn, true);
            }
        } catch (\Throwable $t) {
            echo sprintf("reconstructGameStateFromTurnNode failed: %s\n", $t)
                 . sprintf("Last game state prior to reconstruction:\n%s\n\n", $node->getLastKnownGameState())
                 . sprintf("Last reconstructed game state:\n%s\n\n", $gameState)
                 . sprintf("Turns used for reconstruction:\n%s\n", implode(' -> ' . PHP_EOL, $turnsSinceGameState));
            die();
        }

        return $gameState;
    }

    /**
     * @param Node $node
     * @return Game\State
     */
    public function getGameStateFromNode(Node $node) : Game\State
    {
        if ($node instanceof Tree\GameStateNode) {
            return $node->getGameState();
        }
        if ($node instanceof Tree\TurnNode) {
            return $this->reconstructGameStateFromTurnNode($node);
        }
        throw new \LogicException(sprintf('Unhandled node instance type: %s', get_class($node)));
    }

    /**
     * @return GameSimulator
     */
    public function getGameSimulator(): GameSimulator
    {
        return $this->gameSimulator;
    }
}
