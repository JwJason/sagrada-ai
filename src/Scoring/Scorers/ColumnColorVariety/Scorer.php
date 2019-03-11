<?php
declare(strict_types=1);

namespace Sagrada\Scoring\Scorers\ColumnColorVariety;

use Sagrada\Board\Board;
use Sagrada\Board\Iterators\ColumnIterator;
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
        $iterator = new ColumnIterator($board);

        foreach ($iterator as $col) {
            if ($col->allSpacesHaveDice() && $calculator->hasColorVariety($col)) {
                $occurances++;
                $score += self::PER_DIEM_SCORE;
            }
        }

        $this->occurances = $occurances;
        $this->score = $score;
    }
}
