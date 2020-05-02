<?php
declare(strict_types=1);

namespace Sagrada;

require_once('vendor/autoload.php');

use function DeepCopy\deep_copy;

use Sagrada\Ai\AiPlayer;
use Sagrada\Ai\ProbabilityCalculator;
use Sagrada\Ai\Simulations\GameSimulator;
use Sagrada\Ai\Strategies\MonteCarloTree\MonteCarloTreeStrategy;
use Sagrada\Ai\Strategies\MonteCarloTree\Uct;
use Sagrada\Board;
use Sagrada\Board\Grid\GridCoordinates;
use Sagrada\Board\Iterators\RowIterator;
use Sagrada\DiceBag;
use Sagrada\Dice\SagradaDie;
use Sagrada\DiePlacement\BoardPlacer;
use Sagrada\DiePlacement\Finder;
use Sagrada\Game\PlayerState;
use Sagrada\Game\State;
use Sagrada\Player\SagradaPlayer;
use Sagrada\ScoreCards\Cards;
use Sagrada\ScoreCards\SagradaScoreCardCollection;
use Sagrada\DiePlacement\Validator;
use Sagrada\Scoring\Scorers\FromSagradaScoreCardFactory;

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

    $humanPlayer = new SagradaPlayer();
    $humanPlayer->setName('Human');
    $humanPlayerBoard = new Board\Board(new Board\Meta\Comitas());
    $humanPlayer->setState(new PlayerState($humanPlayer, $humanPlayerBoard, $game));

    $game->setState($gameState);
    $game->setPlayers([$aiPlayer, $humanPlayer]);

    return $game;
}

try {
    $game = initializeGame();
    $gameSimulator = new GameSimulator();

    $aiStrategy = new MonteCarloTreeStrategy(
        $gameSimulator,
        new Uct()
    );

    // Example turns
    $gameState = $game->getState();
    $gameState->initializeFirstRound();
    while ($gameState->hasRoundsRemaining()) {
        echo sprintf("ROUND #%d\n", $gameState->getCurrentRound());
        echo sprintf("CURRENT PLAYER: %s\n", $gameState->getCurrentPlayer()->getName());
        echo sprintf("DRAFT POOL %s\n", $gameState->getDraftPool());
        $diePlacement = $aiStrategy->getBestDiePlacement($gameState);
        if ($diePlacement) {
//            $gameState = $gameSimulator->simulateTurn($gameState->deepCopy(), $diePlacement);
            $player = $gameState->getCurrentPlayer();
            $board = $player->getState()->getBoard();

            $gameState->getDraftPool()->remove($diePlacement->getDie());
            $gameState->getGame()->getPlacementPlacer()->putDiePlacementOnBoard($diePlacement, $board);
            $gameState->nextTurn();
        } else {
            $gameState->nextTurn();
        }
        echo $gameState->getCurrentPlayer()->getState()->getBoard() . "\n";
    }

//    echo "Number of occurances: " . $scorer->getNumberOfOccurances() . "\n";
//    echo "Player 1 Score: " . $gameState->getPlayerState()->getScore() . "\n";
} catch (\Throwable $t) {
    throw $t;
}

/*
 * AI Will:
 * - Get a list of all valid die placements from the referee
 * - Of those valid die placements, calculate which of them are available for play with the given dice
 * - Of those available die placements, calculate the probability that he'll be able to play each one in the future
 *      - Calculate this by comparing the already-played dice with the pooled dice.
 *      - Also factor in the second player's turn, based on the other player's best available play
 * - Also calculate the potential score he could get with each dice placement (the ideal end-game board)
 */
