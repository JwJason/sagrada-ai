<?php
declare(strict_types=1);

namespace Sagrada;

use Sagrada\Dice\Color\DiceColorFactory;
use Sagrada\Dice\Color\DiceColorInterface;
use Sagrada\Dice\SagradaDie;
use Sagrada\Dice\Value\DiceValueFactory;

class DiceBag
{
    /** @var int int */
    protected $amountOfEachColor;

    /** @var array */
    protected $colorCounter;

    /** @var DiceColorFactory $colorFactory */
    protected $colorFactory;

    /** @var DiceValueFactory $valueFactory */
    protected $valueFactory;

    /** @var int */
    protected $diceRemainingCount;

    public function __construct(int $amountOfEachColor)
    {
        $this->amountOfEachColor = $amountOfEachColor;
        $this->colorFactory = new DiceColorFactory();
        $this->valueFactory = new DiceValueFactory();

        $colorSymbols = $this->colorFactory->getColorSymbols();
        foreach ($colorSymbols as $colorSymbol) {
            $this->colorCounter[$colorSymbol] = $this->amountOfEachColor;
        }

        $this->diceRemainingCount = count($this->colorCounter) * $this->amountOfEachColor;
    }

    /**
     * @return int
     */
    public function getAllRemainingCount(): int
    {
        return $this->diceRemainingCount;
    }

    /**
     * @param DiceColorInterface $color
     * @return int
     * @throws \Exception
     */
    public function getColorRemainingCount(DiceColorInterface $color): int
    {
        return $this->getColorRemainingCountForColorSymbol($color->getSymbol());
    }

    public function getColorRemainingCountForColorSymbol(string $colorSymbol): int
    {
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

        $colorProbabilityMap = $this->getColorProbabilityMap();
        $colorSymbol = $this->getRandomWeightedElement($colorProbabilityMap);
        $valueSymbol = (string)(random_int(1, 6));

        $die = new SagradaDie(
            $this->colorFactory->createDiceColorFromSymbol($colorSymbol),
            $this->valueFactory->createDiceValueFromSymbol($valueSymbol)
        );

        $this->colorCounter[$colorSymbol]--;
        $this->diceRemainingCount--;

        return $die;
    }

    public function drawDieCollection(int $numberOfDieToDraw): DieCollection
    {
        $collection = new DieCollection();
        for ($i = 0; $i < $numberOfDieToDraw; $i++) {
            $die = $this->drawDie();
            $collection->add($die);
        }
        return $collection;
    }

    /**
     * @param DiceColorInterface $diceColor
     */
    public function removeOneDieOfColor(DiceColorInterface $diceColor): void
    {
        $colorSymbol = $diceColor->getSymbol();
        $this->colorCounter[$colorSymbol]--;
        $this->diceRemainingCount--;
    }

    /**
     * @return DieCollection
     * @throws \Exception
     */
    public function getAllPossibleRemainingDiceRolls(): DieCollection
    {
        $colorCounter = $this->getColorCounterForRemainingColors();
        $dieCollection = new DieCollection();

        foreach ($colorCounter as $colorSymbol => $count) {
            for ($value = 1; $value <= 6; $value++) {
                $dieCollection->add(new SagradaDie(
                    $this->colorFactory->createDiceColorFromSymbol($colorSymbol),
                    $this->valueFactory->createDiceValueFromSymbol((string)$value)
                ));
            }
        }

        return $dieCollection;
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

    protected function getColorProbabilityMap(): array
    {
        $colorCountMap = $this->getColorCounterForRemainingColors();
        $colorProbabilityMap = [];
        foreach ($colorCountMap as $colorSymbol => $numberOfColorsRemaining) {
            $colorProbabilityMap[$colorSymbol] =
                ($this->getColorRemainingCountForColorSymbol($colorSymbol) / $this->getAllRemainingCount()) * 100;
        }
        return $colorProbabilityMap;
    }

    /**
     * https://stackoverflow.com/questions/445235/generating-random-results-by-weight-in-php
     * getRandomWeightedElement()
     * Utility function for getting random values with weighting.
     * Pass in an associative array, such as array('A'=>5, 'B'=>45, 'C'=>50)
     * An array like this means that "A" has a 5% chance of being selected, "B" 45%, and "C" 50%.
     * The return value is the array key, A, B, or C in this case.  Note that the values assigned
     * do not have to be percentages.  The values are simply relative to each other.  If one value
     * weight was 2, and the other weight of 1, the value with the weight of 2 has about a 66%
     * chance of being selected.  Also note that weights should be integers.
     *
     * @param array $weightedValues
     */
    function getRandomWeightedElement(array $weightedValues) {
        $rand = random_int(1, (int) array_sum($weightedValues));

        foreach ($weightedValues as $key => $value) {
            $rand -= $value;
            if ($rand <= 0) {
                return $key;
            }
        }

        return array_rand($weightedValues);
    }
}
