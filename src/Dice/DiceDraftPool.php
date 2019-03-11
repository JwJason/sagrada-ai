<?php
declare(strict_types=1);

namespace Sagrada\Dice;

/**
 * Class DieDraftPool
 * @package Sagrada\Dice
 */
class DiceDraftPool
{
    /**
     * @var array
     */
    protected $dice;

    /**
     * DieDraftPool constructor.
     * @param array $dice
     */
    public function __construct(array $dice)
    {
        $this->dice = $dice;
    }

    /**
     * @return array
     */
    public function getDice(): array
    {
        return $this->dice;
    }

    /**
     * @param array $dice
     */
    public function setDice(array $dice): void
    {
        $this->dice = $dice;
    }

    /**
     * @param int $index
     * @return SagradaDie
     * @throws \Exception
     */
    public function draftDie(int $index): SagradaDie
    {
        $dice = $this->getDice();
        if (!isset($dice[$index])) {
            throw new \Exception(sprintf('Invalid draft pool index: %d', $index));
        }
        $die = $dice[$index];
        array_splice($dice, $index, 1);
        $this->dice = $dice;
        return $die;
    }

    /**
     * @return int
     */
    public function getDiceCount(): int
    {
        return count($this->dice);
    }
}
