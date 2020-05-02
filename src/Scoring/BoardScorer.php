<?php
declare(strict_types=1);

namespace Sagrada\Scoring;

use Sagrada\Scoring\Scorers\ScorerInterface;

class BoardScorer
{
    protected $scorers;

    public function __construct(array $scorers)
    {
        foreach ($scorers as $scorer) {
            if ($scorer instanceof ScorerInterface === false) {
                throw new \Exception(sprintf('Expecting instance of ScorerInterface; got %s', get_class($scorer)));
            }
        }

        $this->scorers = $scorers;
    }

    /**
     * @return int
     */
    public function getScore(): int
    {
        $total = 0;
        foreach ($this->getScorers() as $scorer) {
            $total += $scorer->getScore();
        }
        return $total;
    }

    /**
     * @return array
     */
    public function getScorers(): array
    {
        return $this->scorers;
    }
}
