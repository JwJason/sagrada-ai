<?php
declare(strict_types=1);

namespace Sagrada\Dice\Color;

final class DiceColorBlue implements DiceColorInterface
{
    public function getValue(): int
    {
        return 4;
    }

    public function toString(): string
    {
        return 'blue';
    }

    public function getSymbol(): string
    {
        return 'b';
    }
}
