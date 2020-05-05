<?php
declare(strict_types=1);

namespace Sagrada\Ai;

use Sagrada\DiePlacement;
use Sagrada\Game;
use Sagrada\Turn;
use Sagrada\Turn\Pass;

/**
 * Class GameSimulator
 * @package Sagrada\Ai\Simulations
 */
class GameSimulator
{
    public function simulateRandomPlayout(Game\State $initialGameState): Game\State
    {
        $gameState = $initialGameState->deepCopy();

        while ($gameState->gameIsCompleted() === false) {
            $gameState = $this->simulateRandomTurn($gameState);
        }

        return $gameState;
    }

    public function simulateTurn(Game\State $initialGameState, Turn $turn, bool $pullFromDieBag=false): Game\State
    {
        $gameState = $initialGameState->deepCopy();
        $player = $gameState->getCurrentPlayer();

        if ($turn instanceof Turn\DiePlacement) {
            $placementPlacer = $gameState->getGame()->getPlacementPlacer();
            $board = $player->getState()->getBoard();
            $diePlacement = $turn->getDiePlacement();

            if ($pullFromDieBag === true) {
                $gameState->getDiceBag()->removeOneDieOfColor($diePlacement->getDie()->getColor());
            } else {
                $gameState->getDraftPool()->remove($diePlacement->getDie());
            }

            $placementPlacer->putDiePlacementOnBoard($diePlacement, $board);
            $player->getState()->getTurnHistory()->add($turn);
        } else if ($turn instanceof Turn\Pass) {
            $player->getState()->getTurnHistory()->add(new Pass());
        } else {
            throw new \LogicException(sprintf('Unknown Turn type: %s', get_class($turn)));
        }

        $gameState->nextTurn();
        return $gameState;
    }

    public function simulateTurns(Game\State $initialGameState, Turn\Collection $turns)
    {

    }

    // TODO - Add 'Pass' turn option to this, as well as to the node expansion
    public function simulateRandomTurn(Game\State $initialGameState): Game\State
    {
        $gameState = $initialGameState->deepCopy();
        $placementFinder = $gameState->getGame()->getPlacementFinder();
        $placementPlacer = $gameState->getGame()->getPlacementPlacer();

        $board = $gameState->getCurrentPlayer()->getState()->getBoard();
        $draftPool = $gameState->getDraftPool();
        $diePlacements = $placementFinder->getAllValidDiePlacementsForDieCollection($draftPool, $board);

        if (empty($diePlacements)) {
            $gameState->getCurrentPlayer()->getState()->getTurnHistory()->add(new Pass());
            $gameState->nextTurn();
            return $gameState;
        }

        /** @var DiePlacement $diePlacement */
        $diePlacement = $diePlacements[array_rand($diePlacements)];
        $gameState->getDraftPool()->remove($diePlacement->getDie());
        $placementPlacer->putDiePlacementOnBoard($diePlacement, $board);
        $gameState->getCurrentPlayer()->getState()->getTurnHistory()->add(new Turn\DiePlacement($diePlacement));
        $gameState->nextTurn();

        return $gameState;
    }
}
