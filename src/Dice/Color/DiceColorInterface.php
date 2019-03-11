<?php
declare(strict_types=1);

namespace Sagrada\Dice\Color;

interface DiceColorInterface
{
    public function toString() : string;
    public function getSymbol() : string;
    public function getValue() : int;
}
