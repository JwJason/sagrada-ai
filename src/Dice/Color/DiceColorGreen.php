<?php
declare(strict_types=1);

namespace Sagrada\Dice\Color;

final class DiceColorGreen implements DiceColorInterface
{
    public function getValue(): int
    {
        return 2;
    }

    public function toString(): string
    {
        return 'green';
    }

    public function getSymbol(): string
    {
        return 'g';
    }
}
