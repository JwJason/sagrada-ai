<?php
declare(strict_types=1);

namespace Sagrada;

require_once('vendor/autoload.php');

use Sagrada\Ai\GameSimulator;
use Sagrada\Ai\Strategies\MonteCarloTreeStrategy;
use Sagrada\Board;
use Sagrada\DiePlacement\BoardPlacer;
use Sagrada\DiePlacement\Finder;
use Sagrada\DiePlacement\Validator;
use Sagrada\Game\PlayerState;
use Sagrada\Game\State;
use Sagrada\Player\SagradaPlayer;
use Sagrada\ScoreCards\Cards;
use Sagrada\ScoreCards\SagradaScoreCardCollection;

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
    $aiPlayerBoard = new Board\Board(new Board\Meta\Comitas(), true);
    $aiPlayer->setName('AI');
    $aiPlayer->setState(new PlayerState($aiPlayer, $aiPlayerBoard, $game));

    $game->setState($gameState);
    $game->setPlayers([$aiPlayer]);

    return $game;
}

function main(): void
{
    $game = initializeGame();
    $gameSimulator = new GameSimulator();

    $aiStrategy = new MonteCarloTreeStrategy(
        $gameSimulator,
        new MonteCarloTreeStrategy\Uct()
    );

    // Example turns
    $gameState = $game->getState();
    $gameState->initializeFirstRound();
//    while ($gameState->currentRoundHasTurnsRemaining() && $gameState->getCurrentRound() === 1) {
    while ($gameState->gameIsCompleted() === false) {
        echo (string)$gameState;
        $turn = $aiStrategy->getBestTurn($gameState);
        echo sprintf("Playing %s\n", $turn);
        $gameSimulator->simulateTurn($gameState, $turn);
    }
    echo (string)$gameState;
}

try {
    main();
} catch (\Throwable $t) {
    echo $t->getMessage() . PHP_EOL;
    echo $t->getFile() . PHP_EOL;
    echo $t->getLine() . PHP_EOL;
}
