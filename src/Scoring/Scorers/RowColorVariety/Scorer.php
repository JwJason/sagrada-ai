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

    /** @var Board */
    protected $board;
    /** @var int */
    protected $occurrences;
    /** @var int */
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

    public function getNumberOfOccurrences(): int
    {
        return $this->occurrences;
    }

    protected function score(Board $board): void
    {
        $occurrences = 0;
        $score = 0;

        $calculator = new ColorVarietyHelper();
        $iterator = new RowIterator($board);

        foreach ($iterator as $row) {
            if ($row->allSpacesHaveDice() && $calculator->hasColorVariety($row)) {
                $occurrences++;
                $score += self::PER_DIEM_SCORE;
            }
        }

        $this->occurrences = $occurrences;
        $this->score = $score;
    }
}
