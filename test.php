<?php
declare(strict_types=1);

namespace Sagrada;

require_once('vendor/autoload.php');

use Sagrada\Ai\Simulations\GameSimulator;
use Sagrada\Ai\Strategies\MonteCarloTreeStrategy;
use Sagrada\Board;
use Sagrada\DiePlacement\BoardPlacer;
use Sagrada\DiePlacement\Finder;
use Sagrada\Game\PlayerState;
use Sagrada\Game\State;
use Sagrada\Player\SagradaPlayer;
use Sagrada\ScoreCards\Cards;
use Sagrada\ScoreCards\SagradaScoreCardCollection;
use Sagrada\DiePlacement\Validator;

const DRAFT_POOL_SIZE = 5;

const AMOUNT_OF_EACH_COLOR_DICE = 18;

function initializeGame(): Game
{
    $placementValidator = new Validator();
    $placementFinder = new Finder($placementValidator);
    $placementPlacer = new BoardPlacer($placementValidator);

    $scoreCards = new SagradaScoreCardCollection();
    $scoreCards->addScoreCard(new Cards\RowColorVariety());
    $scoreCards->addScoreCard(new Cards\ColumnColorVariety());

    $game = new Game();
    $game->setPlacementFinder($placementFinder);
    $game->setPlacementPlacer($placementPlacer);
    $game->setPlacementValidator($placementValidator);
    $game->setScoreCards($scoreCards);
    $game->setDraftPoolSize(5);

    $gameState = new State();
    $gameState->setDiceBag(new DiceBag(AMOUNT_OF_EACH_COLOR_DICE));
    $gameState->setGame($game);

    $aiPlayer = new SagradaPlayer();
    $aiPlayerBoard = new Board\Board(new Board\Meta\Comitas());
    $aiPlayer->setName('AI');
    $aiPlayer->setState(new PlayerState($aiPlayer, $aiPlayerBoard, $game));

    $game->setState($gameState);
    $game->setPlayers([$aiPlayer]);

    return $game;
}

try {
    $game = initializeGame();
    $gameSimulator = new GameSimulator();

    $aiStrategy = new MonteCarloTreeStrategy(
        $gameSimulator,
        new MonteCarloTreeStrategy\Uct()
    );

    // Example turns
    $gameState = $game->getState();
    $gameState->initializeFirstRound();
    while ($gameState->gameIsCompleted() === false) {
        echo (string)$gameState;
        $turn = $aiStrategy->getBestTurn($gameState);
        echo sprintf("Playing %s\n", $turn);
        $gameState = $gameSimulator->simulateTurn($gameState, $turn);
    }
    echo "Score: " . $gameState->getGame()->getPlayers()[0]->getState()->getScore() . "\n";
} catch (\Throwable $t) {
    throw $t;
}
