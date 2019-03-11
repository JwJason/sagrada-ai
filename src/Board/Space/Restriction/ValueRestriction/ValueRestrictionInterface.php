<?php
declare(strict_types=1);

namespace Sagrada\Board\Space\Restriction\ValueRestriction;

use Sagrada\Dice\Value\DiceValueInterface;

interface ValueRestrictionInterface
{
    public function getValue() : DiceValueInterface;
}
