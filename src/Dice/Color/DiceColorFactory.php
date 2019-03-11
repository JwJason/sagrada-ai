<?php
declare(strict_types=1);

namespace Sagrada\Dice\Color;

/**
 * Class DiceColorFactory
 * @package Sagrada\Dice\Color
 */
class DiceColorFactory
{
    /**
     * @param string $symbol
     * @return DiceColorInterface
     * @throws \Exception
     */
    public function createDiceColorFromSymbol(string $symbol): DiceColorInterface
    {
        switch ($symbol) {
            case 'b':
                return new DiceColorBlue();
            case 'g':
                return new DiceColorGreen();
            case 'p':
                return new DiceColorPurple();
            case 'r':
                return new DiceColorRed();
            case 'y':
                return new DiceColorYellow();
            default:
                throw new \Exception("Invalid color symbol: '$symbol");
        }
    }

    /**
     * @param string $symbol
     * @return bool
     */
    public function canCreateColorFromSymbol(string $symbol): bool
    {
        return preg_match('/^(b|g|p|r|y)$/', $symbol) === 1;
    }

    /**
     * @return array
     */
    public function getColorSymbols(): array
    {
        return ['b', 'g', 'p', 'r', 'y'];
    }
}
