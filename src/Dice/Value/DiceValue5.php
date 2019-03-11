<?php
declare(strict_types=1);

namespace Sagrada\Dice\Value;

use Sagrada\Dice\Shade\SagradaDiceShadeDark;
use Sagrada\Dice\Shade\SagradaDiceShadeInterface;

final class DiceValue5 implements DiceValueInterface
{
    public function getShade() : SagradaDiceShadeInterface
    {
        return new SagradaDiceShadeDark();
    }

    public function getValue(): int
    {
        return 5;
    }

    public function getSymbol(): string
    {
        return '5';
    }
}
