<?php
declare(strict_types=1);

namespace Sagrada\Board\Space\Restriction\ColorRestriction;

use Sagrada\Board\Space\Restriction\PartialRestrictionInterface;
use Sagrada\Dice\Color\DiceColorInterface;

class ColorRestriction implements ColorRestrictionInterface, PartialRestrictionInterface
{
    protected $color;

    public function __construct(DiceColorInterface $color)
    {
        $this->color = $color;
    }

    public function getColor(): DiceColorInterface
    {
        return $this->color;
    }

    public function getSymbol(): string
    {
        return $this->color->getSymbol();
    }
}
