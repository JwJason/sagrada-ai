<?php
declare(strict_types=1);

namespace Sagrada\Board\Space\Restriction\ColorRestriction;

use Sagrada\Dice\Color\DiceColorInterface;

interface ColorRestrictionInterface
{
    public function getColor(): DiceColorInterface;
}
