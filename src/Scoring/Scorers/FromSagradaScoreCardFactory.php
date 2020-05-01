<?php
declare(strict_types=1);

namespace Sagrada\Scoring\Scorers;

use Sagrada\Board\Board;
use Sagrada\ScoreCards\Cards;
use Sagrada\ScoreCards\SagradaScoreCardCollection;
use Sagrada\ScoreCards\SagradaScoreCardInterface;
use Sagrada\Scoring\BoardScorer;
use Sagrada\Scoring\Scorers;

class FromSagradaScoreCardFactory
{
    public function createFromScoreCard(SagradaScoreCardInterface $scoreCard, Board $board) : ScorerInterface
    {
        $scoreCardClass = get_class($scoreCard);

        switch ($scoreCardClass) {
            case Cards\ColumnColorVariety::class:
                return new Scorers\ColumnColorVariety\Scorer($board);
            case Cards\RowColorVariety::class:
                return new Scorers\RowColorVariety\Scorer($board);
            default:
                throw new \Exception('Unable to resolve scorer from score card: %s', $scoreCardClass);
        }
    }

    public function createFromScoreCardCollection(SagradaScoreCardCollection $scoreCards, Board $board) : BoardScorer
    {
        $scorers = [];
        foreach ($scoreCards->getScoreCards() as $scoreCard) {
            $scorers[] = $this->createFromScoreCard($scoreCard, $board);
        }
        return new BoardScorer($scorers);
    }
}
