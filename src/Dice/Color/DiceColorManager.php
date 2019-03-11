<?php
declare(strict_types=1);

namespace Sagrada\Dice\Color;

/**
 * Class DiceColorManager
 * @package Sagrada\Dice\Color
 */
class DiceColorManager
{
    /**
     * @return array
     */
    public function getAllColors()
    {
        return [
            new DiceColorBlue(),
            new DiceColorGreen(),
            new DiceColorPurple(),
            new DiceColorRed(),
            new DiceColorYellow()
        ];
    }
}
