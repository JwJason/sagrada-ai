<?php
declare(strict_types=1);

namespace Sagrada\Board\Space\Restriction\ValueRestriction;

use Sagrada\Dice\Value\DiceValueFactory;

class ValueRestrictionFactory
{
    /**
     * @param string $symbol
     * @return ValueRestriction
     * @throws \Exception
     */
    public function createRestrictionFromSymbol(string $symbol): ValueRestriction
    {
        $valueFactory = new DiceValueFactory();
        return new ValueRestriction($valueFactory->createDiceValueFromSymbol($symbol));
    }

    public function canCreateRestrictionFromSymbol(string $symbol): bool
    {
        $valueFactory = new DiceValueFactory();
        return $valueFactory->canCreateDiceValueFromSymbol($symbol);
    }
}
