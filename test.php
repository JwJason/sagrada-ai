<?php
declare(strict_types=1);

namespace Sagrada;

require_once('vendor/autoload.php');

use function DeepCopy\deep_copy;

use Sagrada\Ai\AiPlayer;
use Sagrada\Ai\ProbabilityCalculator;
use Sagrada\Ai\Strategies\MonteCarlo\MonteCarloSimulator;
use Sagrada\Ai\Strategies\MonteCarlo\MonteCarloStrategy;
use Sagrada\Board\Board;
use Sagrada\Board\Grid\GridCoordinates;
use Sagrada\Board\Iterators\RowIterator;
use Sagrada\Board\Meta;
use Sagrada\Dice\DiceBag;
use Sagrada\Dice\DiceDraftPool;
use Sagrada\Dice\SagradaDie;
use Sagrada\Game\PlayerGameState;
use Sagrada\Player\SagradaPlayer;
use Sagrada\Scoring\BoardScorer;
use Sagrada\Validators\DiePlacementValidator;
use Sagrada\Scoring\Scorers\ColumnColorVariety;
use Sagrada\Scoring\Scorers\RowColorVariety;

const DRAFT_POOL_SIZE = 5;

const AMOUNT_OF_EACH_COLOR_DICE = 18;

try {
    // Initialize
//    $meta = new Meta\Comitas();
    $meta = new Meta\SmallTestBoard();
    $board = new Board($meta);
    $player = new SagradaPlayer();
    $diceBag = new DiceBag(AMOUNT_OF_EACH_COLOR_DICE);
    $diePlacementValidator = new DiePlacementValidator();
    $diePlacementFinder = new DiePlacementFinder($diePlacementValidator);
    $aiPlayer = new AiPlayer(
        new MonteCarloStrategy(
            new DiePlacementFinder($diePlacementValidator),
            new MonteCarloSimulator($diePlacementFinder)
        )
    );

    // Example round start
//    $dice = [];
//    for ($i = 0; $i < DRAFT_POOL_SIZE; ++$i) {
//        $dice[] = $diceBag->drawDie();
//    }
//    $draftPool = new DiceDraftPool($dice);
    $draftPool = new DiceDraftPool([]);

    // Example turns
    $gameState = new PlayerGameState($board, $diceBag, $draftPool, $player, $diePlacementValidator);
    while ($gameState->hasTurnsRemaining()) {
        echo sprintf("TURN #%d\n", $gameState->getTurnsRemaining());
        echo sprintf("DICE LEFT: %d\n", $diceBag->getAllRemainingCount());
        $die = $diceBag->drawDie();
        $aiPlayer->takeTurn($die, $gameState);
        echo $gameState->getBoard() . "\n";
    }

    $scorer = new BoardScorer([
        new RowColorVariety\Scorer($board),
        new ColumnColorVariety\Scorer($board)
    ]);
//    echo "Number of occurances: " . $scorer->getNumberOfOccurances() . "\n";
    echo "Score: " . $scorer->getScore() . "\n";
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

///**
// * @param SagradaDie $die
// * @param TurnDirector $director
// * @throws \Exception
// */
//function boardPlacement(SagradaDie $die, TurnDirector $director): void
//{
//    $board = $director->getBoard();
//    $probabilityCalculator = new ProbabilityCalculator();
//    $probabilityData = [];
//
//    $placementColorProbability =
//
//    $iterator = new RowIterator($board);
//
//    // Get all color placement probabilities
//    // TODO
//
//    // Get all value placement probabilities
//    // TODO
//
//
//    // Get probability data for all valid placements
//    foreach ($iterator as $rowIndex => $row) {
//        foreach ($row->getItems() as $colIndex => $space) {
//            $coordinates = new GridCoordinates($rowIndex, $colIndex);
//            $diePlacement = new DiePlacement($die, $coordinates);
//
//            if ($director->getPlacementValidator()->isValidDiePlacement($diePlacement, $board)) {
////                $placementProbability = $probabilityCalculator->getProbabilityOfAnyValidMoveOnSpace(
////                    $space,
////                    $director->getDiceBag(),
////                    $director->getReferee()
////                );
//
////                $placementProbability = $probabilityCalculator->get
//
//
//                // Get probability data for all valid color placements on this space
//                // TODO
//
//                // Get probability data for all valid value placements on this space
//                // TODO
//
//                // Use value + color probability to get total probability
//                // TODO
//
//                $probabilityData[] = [
//                    'boardSpace'  => $space,
//                    'probability' => $placementProbability
//                ];
//            }
//        }
//    }
//
//    // Filter probability data to the lowest probability placements only
//    $lowestPlacementProbabilities = [];
//    $lowestPlacementProbability = 1;
//    foreach ($probabilityData as $probabilityDatum) {
//        $probability = $probabilityDatum['probability'];
//        if ($probability === $lowestPlacementProbability) {
//            $lowestPlacementProbabilities[] = $probabilityDatum;
//        } elseif ($probability < $lowestPlacementProbability) {
//            $lowestPlacementProbability = [$probabilityDatum];
//        }
//    }
//
//    // Filter probability data to spaces with intrinsic restrictions only
//    foreach ($lowestPlacementProbabilities as $probabilityDatum) {
//        // TODO
//    }
//
//    // For each remaining space:
//    //   Weigh each of that remaining space's valid color + value combos, based on if playing that combo would introduce
//    //   more restrictions to adjacent spaces
//        // TODO
//    //  When possible (depending on the active Scoring cards), determine which color + value combos would nullify
//    //  or achieve scoring combos like row color combo, etc (can we do this probabilistically?)
//}