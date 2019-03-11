<?php
declare(strict_types=1);

namespace Sagrada\Dice\Color;

final class DiceColorPurple implements DiceColorInterface
{
    public function getValue(): int
    {
        return 5;
    }

    public function toString(): string
    {
        return 'purple';
    }

    public function getSymbol(): string
    {
        return 'p';
    }
}
