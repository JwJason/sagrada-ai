<?php
declare(strict_types=1);

namespace Sagrada\Ai\Strategies\MonteCarloTreeStrategy;

use Sagrada\Ai\Simulations\GameSimulator;
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
        $game = $gameState->getGame();

        $placementFinder = $game->getPlacementFinder();

        $placements = $placementFinder->getAllValidDiePlacementsForDieCollection(
            $gameState->getDraftPool(),
            $gameState->getCurrentPlayer()->getState()->getBoard()
        );

//        echo sprintf(
//            "Expanding game state node (round %d, turn %d, %d children)\n",
//            $gameState->getCurrentRound(),
//            $gameState->getCurrentTurn(),
//            count($placements)
//        );

        if (count($placements) > 0) {
            /** @var DiePlacement $placement */
            foreach ($placements as $placement) {
//                echo sprintf("--- %s\n", $placement);
                $turn = new Turn\DiePlacement($placement);
                $newGameState = $this->getGameSimulator()->simulateTurn($gameState, $turn);
                $newGameStateNode = new Tree\GameStateNode;
                $newGameStateNode->setGameState($newGameState);
                $node->addChild($newGameStateNode);
            }
        } else {
            $newGameState = $this->getGameSimulator()->simulateTurn($gameState, new Turn\Pass());
            $newGameStateNode = new Tree\GameStateNode();
            $newGameStateNode->setGameState($newGameState);
            $node->addChild($newGameStateNode);
        }
    }

    public function expandDiePlacementNode(Node $node): void
    {
       $gameState = $this->getGameStateFromNode($node);

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
        $gameState = $node->getLastKnownGameState();
        $turnsSinceGameState = $node->getAllPrecedingTurns();

        /** @var Turn $turn */
        foreach ($turnsSinceGameState as $turn) {
            $gameState = $this->getGameSimulator()->simulateTurn($gameState, $turn, true);
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