<?php
declare(strict_types=1);

namespace Sagrada\Dice\Value;

/**
 * Class DiceValueManager
 * @package Sagrada\Dice\Value
 */
class DiceValueManager
{
    /**
     * @return array
     */
    public function getAllValues(): array
    {
        return [
            new DiceValue1(),
            new DiceValue2(),
            new DiceValue3(),
            new DiceValue4(),
            new DiceValue5(),
            new DiceValue6()
        ];
    }
}
