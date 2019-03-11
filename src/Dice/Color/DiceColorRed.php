<?php
declare(strict_types=1);

namespace Sagrada\Dice\Color;

final class DiceColorRed implements DiceColorInterface
{
    public function getValue(): int
    {
        return 1;
    }

    public function toString(): string
    {
        return 'red';
    }

    public function getSymbol(): string
    {
        return 'r';
    }
}
