<?php
declare(strict_types=1);

namespace Sagrada\Dice;

use Sagrada\Dice\Color\DiceColorFactory;
use Sagrada\Dice\Color\DiceColorInterface;
use Sagrada\Dice\Value\DiceValueFactory;

class DiceBag
{
    protected $amountOfEachColor;
    protected $colorCounter;
    protected $colorFactory;
    protected $valueFactory;

    public function __construct(int $amountOfEachColor)
    {
        $this->amountOfEachColor = $amountOfEachColor;
        $this->colorFactory = new DiceColorFactory();
        $this->valueFactory = new DiceValueFactory();

        $colorSymbols = $this->colorFactory->getColorSymbols();
        foreach ($colorSymbols as $colorSymbol) {
            $this->colorCounter[$colorSymbol] = $this->amountOfEachColor;
        }
    }

    /**
     * @return int
     */
    public function getAllRemainingCount(): int
    {
        return array_reduce(
            $this->colorCounter,
            function (int $amountRemaining, int $totalAmountRemaining) {
                return $amountRemaining + $totalAmountRemaining;
            },
            0
        );
    }

    /**
     * @param DiceColorInterface $color
     * @return int
     * @throws \Exception
     */
    public function getColorRemainingCount(DiceColorInterface $color): int
    {
        $colorSymbol = $color->getSymbol();
        if (!isset($this->colorCounter[$colorSymbol])) {
            throw new \Exception(sprintf('DiceBag has no colors with symbol: "%s"', $colorSymbol));
        }
        return $this->colorCounter[$colorSymbol];
    }

    /**
     * @return bool
     */
    public function hasRemainingDice(): bool
    {
        return $this->getAllRemainingCount() > 0;
    }

    /**
     * @return SagradaDie
     * @throws \Exception
     */
    public function drawDie(): SagradaDie
    {
        if ($this->hasRemainingDice() === false) {
            throw new \Exception("No remaining die in bag.");
        }

        $filteredColorCounter = $this->getColorCounterForRemainingColors();
        $colorSymbol = array_rand($filteredColorCounter);
        $valueSymbol = strval(random_int(1, 6));

        $die = new SagradaDie(
            $this->colorFactory->createDiceColorFromSymbol($colorSymbol),
            $this->valueFactory->createDiceValueFromSymbol($valueSymbol)
        );

        $this->colorCounter[$colorSymbol]--;

        return $die;
    }

    /**
     * @return array
     */
    protected function getColorCounterForRemainingColors(): array
    {
        return array_filter(
            $this->colorCounter,
            function (int $amountRemaining) {
                return $amountRemaining > 0;
            }
        );
    }
}
