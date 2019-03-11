<?php
declare(strict_types=1);

namespace Sagrada\Board\Space\Restriction;

use Sagrada\Board\Space\Restriction\ColorRestriction\ColorRestrictionInterface;
use Sagrada\Board\Space\Restriction\ValueRestriction\ValueRestrictionInterface;
use Sagrada\Dice\Color\DiceColorInterface;
use Sagrada\Dice\Color\DiceColorNone;
use Sagrada\Dice\Value\DiceValueInterface;
use Sagrada\Dice\Value\DiceValueNone;

class NoRestriction implements PartialRestrictionInterface, ColorRestrictionInterface, ValueRestrictionInterface
{
    public function getColor(): DiceColorInterface
    {
        return new DiceColorNone();
    }

    public function getSymbol(): string
    {
        return '_';
    }

    public function getValue(): DiceValueInterface
    {
        return new DiceValueNone();
    }
}
