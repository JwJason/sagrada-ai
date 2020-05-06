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
            $this->simulateRandomTurn($gameState);
        }

        return $gameState;
    }

    public function simulateTurn(Game\State $gameState, Turn $turn, bool $pullFromDieBag=false): void
    {
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
    }

    // TODO - Add 'Pass' turn option to this, as well as to the node expansion
    public function simulateRandomTurn(Game\State $gameState): void
    {
        $placementFinder = $gameState->getGame()->getPlacementFinder();
        $placementPlacer = $gameState->getGame()->getPlacementPlacer();

        $board = $gameState->getCurrentPlayer()->getState()->getBoard();

        $diePlacements = null;

        $dieCollection = $gameState->getDraftPool();

        foreach ($dieCollection->getAll() as $die) {
            $diePlacements = $placementFinder->getAllValidDiePlacementsForDie($die, $board);
            if (count($diePlacements) > 0) {
                break;
            }
        }

        if (empty($diePlacements)) {
            $gameState->getCurrentPlayer()->getState()->getTurnHistory()->add(new Pass());
            $gameState->nextTurn();
            return;
        }

        /** @var DiePlacement $diePlacement */
        $diePlacement = $diePlacements[array_rand($diePlacements)];
        $gameState->getDraftPool()->remove($diePlacement->getDie());
        $placementPlacer->putDiePlacementOnBoard($diePlacement, $board);
        $gameState->getCurrentPlayer()->getState()->getTurnHistory()->add(new Turn\DiePlacement($diePlacement));
        $gameState->nextTurn();
    }
}
