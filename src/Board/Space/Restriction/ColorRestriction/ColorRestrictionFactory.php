<?php
declare(strict_types=1);

namespace Sagrada\Board\Space\Restriction\ColorRestriction;

use Sagrada\Dice\Color\DiceColorFactory;

class ColorRestrictionFactory
{
    /**
     * @param string $symbol
     * @return ColorRestriction
     * @throws \Exception
     */
    public function createRestrictionFromSymbol(string $symbol): ColorRestriction
    {
        $colorFactory = new DiceColorFactory();
        return new ColorRestriction($colorFactory->createDiceColorFromSymbol($symbol));
    }

    public function canCreateRestrictionFromSymbol(string $symbol): bool
    {
        $colorFactory = new DiceColorFactory();
        return $colorFactory->canCreateColorFromSymbol($symbol);
    }
}
