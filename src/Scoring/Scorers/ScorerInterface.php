<?php
declare(strict_types=1);

namespace Sagrada\Scoring\Scorers;

use Sagrada\Board\Board;

interface ScorerInterface
{
    public function __construct(Board $board);
    public function getScore(): int;
    public function getNumberOfOccurrences(): int;
}
