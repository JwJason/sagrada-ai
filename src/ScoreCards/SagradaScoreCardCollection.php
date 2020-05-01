<?php
declare(strict_types=1);

namespace Sagrada\ScoreCards;

class SagradaScoreCardCollection
{
    protected $scoreCards;

    public function __construct()
    {
        $this->scoreCards = [];
    }

    /**
     * @param SagradaScoreCardInterface $scoreCard
     * @throws \Exception
     */
    public function addScoreCard(SagradaScoreCardInterface $scoreCard)
    {
        if ($this->hasScoreCard($scoreCard, get_class($scoreCard))) {
            throw new \Exception(sprintf('I already contain a score card of type "%s"', get_class($scoreCard)));
        }
        $this->scoreCards[] = $scoreCard;
    }

    public function getScoreCards() : array
    {
        return $this->scoreCards;
    }

    public function hasScoreCard(SagradaScoreCardInterface $scoreCard): bool
    {
        $scoreCardType = get_class($scoreCard);
        foreach ($this->getScoreCards() as $existingScoreCard) {
            if ($existingScoreCard instanceof $scoreCardType) {
                return true;
            }
        }
        return false;
    }
}
