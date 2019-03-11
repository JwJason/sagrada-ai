<?php
declare(strict_types=1);

namespace Sagrada\Board\Space\Restriction\ValueRestriction;

use Sagrada\Board\Space\Restriction\PartialRestrictionInterface;
use Sagrada\Dice\Value\DiceValueInterface;

class ValueRestriction implements ValueRestrictionInterface, PartialRestrictionInterface
{
    protected $value;

    public function __construct(DiceValueInterface $value)
    {
        $this->value = $value;
    }

    public function getValue() : DiceValueInterface
    {
        return $this->value;
    }

    public function getSymbol(): string
    {
        return $this->value->getSymbol();
    }
}
