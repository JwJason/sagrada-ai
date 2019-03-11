<?php
declare(strict_types=1);

namespace Sagrada\Dice\Color;

final class DiceColorYellow implements DiceColorInterface
{
    public function getValue(): int
    {
        return 3;
    }

    public function toString(): string
    {
        return 'yellow';
    }

    public function getSymbol(): string
    {
        return 'y';
    }
}
