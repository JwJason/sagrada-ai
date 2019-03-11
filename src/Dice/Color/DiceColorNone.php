<?php
declare(strict_types=1);

namespace Sagrada\Dice\Color;

final class DiceColorNone implements DiceColorInterface
{
    public function getValue(): int
    {
        return 0;
    }

    public function toString(): string
    {
        return 'none';
    }

    public function getSymbol(): string
    {
        return '_';
    }
}
