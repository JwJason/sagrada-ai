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
                /** @var DiePlacement $placement */
                foreach ($placements as $placement) {
                    $turn = new Turn\DiePlacement($placement);
                    $newGameState = $gameState->deepCopy();
                    $this->getGameSimulator()->simulateTurn($newGameState, $turn);
                    $newGameStateNode = new Tree\GameStateNode;
                    $newGameStateNode->setGameState($newGameState);
                    $node->addChild($newGameStateNode);
                }
            } else {
                $newGameState = $gameState->deepCopy();
                $this->getGameSimulator()->simulateTurn($newGameState, new Turn\Pass());
                $newGameStateNode = new Tree\GameStateNode();
                $newGameStateNode->setGameState($newGameState);
                $node->addChild($newGameStateNode);
            }
        } catch (\Throwable $t) {
            echo sprintf("expandGameNode() failed: %s\n", $t);
            echo sprintf("Game State:\n%s\n", $node->getGameState());
            die();
        }
    }

    public function expandDiePlacementNode(Node $node): void
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

        /** @var DiePlacement $placement */
        foreach ($placements as $placement) {
            $newDiePlacementNode = new Tree\TurnNode();
            $newDiePlacementNode->setTurn(new Turn\DiePlacement($placement));
            $node->addChild($newDiePlacementNode);
        }
    }

    // TODO : Needs test
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
            echo sprintf("reconstructGameStateFromTurnNode failed: %s\n", $t);
            echo sprintf("Last game state prior to reconstruction:\n%s\n\n", $node->getLastKnownGameState());
            echo sprintf("Last reconstructed game state:\n%s\n\n", $gameState);
            echo sprintf("Turns used for reconstruction:\n%s\n", implode(' -> ' . PHP_EOL, $turnsSinceGameState));
            die();
        }

        return $gameState;
    }

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

    /**
     * @param GameSimulator $gameSimulator
     */
    public function setGameSimulator(GameSimulator $gameSimulator): void
    {
        $this->gameSimulator = $gameSimulator;
    }
}
