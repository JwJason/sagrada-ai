<?php
declare(strict_types=1);

namespace Sagrada\Dice\Value;

/**
 * Class DiceValueFactory
 * @package Sagrada\Dice\Value
 */
class DiceValueFactory
{
    /**
     * @param string $symbol
     * @return DiceValueInterface
     * @throws \Exception
     */
    public function createDiceValueFromSymbol(string $symbol): DiceValueInterface
    {
        switch ($symbol) {
            case '1':
                {
                    return new DiceValue1();
                }
            case '2':
                {
                    return new DiceValue2();
                }
            case '3':
                {
                    return new DiceValue3();
                }
            case '4':
                {
                    return new DiceValue4();
                }
            case '5':
                {
                    return new DiceValue5();
                }
            case '6':
                {
                    return new DiceValue6();
                }
            default:
                throw new \Exception("Invalid value symbol: '$symbol");
        }
    }

    public function canCreateDiceValueFromSymbol(string $symbol): bool
    {
        return preg_match('/^[1-6]$/', $symbol) === 1;
    }

    /**
     * @return array
     */
    public function getValidSymbols(): array
    {
        return ['1', '2', '3', '4', '5'];
    }
}
