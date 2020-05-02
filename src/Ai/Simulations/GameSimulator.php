<?php
declare(strict_types=1);

namespace Sagrada\Ai\Simulations;

use Sagrada\DiePlacement;
use Sagrada\Game\GameResults;
use Sagrada\Game;

/**
 * Class GameSimulator
 * @package Sagrada\Ai\Simulations
 */
class GameSimulator
{
    public function simulateRandomPlayout(Game\State $initialGameState): GameResults
    {
        $gameState = $initialGameState->deepCopy();

        while ($gameState->hasRoundsRemaining()) {
            $gameState = $this->simulateRandomTurn($gameState);
            echo "after simulateRandomTurn()";
            echo memory_get_usage() . "\n";
        }

        // TODO- Remove hard-coded assumption that AI player is player 1
        return new GameResults(
            $gameState->getGame()->getPlayers()[0]->getState()->getBoard(),
            $gameState->getGame()->getPlayers()[0]->getState()->getScore()
        );
    }

    public function simulateTurn(Game\State $initialGameState, DiePlacement $diePlacement): Game\State
    {
        $gameState = $initialGameState;
        $player = $gameState->getCurrentPlayer();
        $board = $player->getState()->getBoard();

        $gameState->getDraftPool()->remove($diePlacement->getDie());
        $gameState->getGame()->getPlacementPlacer()->putDiePlacementOnBoard($diePlacement, $board);
        $gameState->nextTurn();

        return $gameState;
    }

    public function simulateRandomTurn(Game\State $gameState): Game\State
    {
        $placementFinder = $gameState->getGame()->getPlacementFinder();
        $placementPlacer = $gameState->getGame()->getPlacementPlacer();

        $board = $gameState->getCurrentPlayer()->getState()->getBoard();
        $draftPool = $gameState->getDraftPool();
        $diePlacements = $placementFinder->getAllValidDiePlacementsForDieCollection($draftPool, $board);

        if (!empty($diePlacements)) {
            /** @var DiePlacement $diePlacement */
            $diePlacement = $diePlacements[array_rand($diePlacements)];
            $gameState->getDraftPool()->remove($diePlacement->getDie());
            $placementPlacer->putDiePlacementOnBoard($diePlacement, $board);
        }

        $gameState->nextTurn();
        return $gameState;
    }
}
