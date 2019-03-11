<?php
declare(strict_types=1);

namespace Sagrada\Scoring\Scorers\RowColorVariety;

use Sagrada\Board\Board;
use Sagrada\Board\Iterators\RowIterator;
use Sagrada\Scoring\Helpers\ColorVarietyHelper;
use Sagrada\Scoring\Scorers\ScorerInterface;

class Scorer implements ScorerInterface
{
    protected const PER_DIEM_SCORE = 6;

    protected $board;
    protected $occurances;
    protected $score;

    public function __construct(Board $board)
    {
        $this->board = $board;
        $this->score($board);
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function getNumberOfOccurances(): int
    {
        return $this->occurances;
    }

    protected function score(Board $board): void
    {
        $occurances = 0;
        $score = 0;

        $calculator = new ColorVarietyHelper();
        $iterator = new RowIterator($board);

        foreach ($iterator as $row) {
            if ($row->allSpacesHaveDice() && $calculator->hasColorVariety($row)) {
                $occurances++;
                $score += self::PER_DIEM_SCORE;
            }
        }

        $this->occurances = $occurances;
        $this->score = $score;
    }
}
